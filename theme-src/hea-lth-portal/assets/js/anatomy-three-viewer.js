import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

const modelConfig = window.heaLthAnatomyViewer;

const isLocalTestHost = () => ['127.0.0.1', 'localhost', '::1'].includes(window.location.hostname);

const isApprovedConfiguration = (config) => (
  config
  && config.status === 'approved'
  && config.engine === 'three-webgl'
  && (!config.testOnly || isLocalTestHost())
  && config.asset
  && Array.isArray(config.asset.lods)
  && config.asset.lods.length > 0
);

const dispatch = (name, detail = {}) => {
  window.dispatchEvent(new CustomEvent(name, { detail }));
};

const webglAvailable = () => {
  try {
    const canvas = document.createElement('canvas');
    return Boolean(canvas.getContext('webgl2') || canvas.getContext('webgl'));
  } catch (error) {
    return false;
  }
};

const selectRuntimeLod = (lods) => {
  const preferredPurpose = window.matchMedia('(max-width: 767px)').matches ? 'mobile' : 'desktop';

  return lods.find((lod) => lod.purpose === preferredPurpose)
    || lods.find((lod) => lod.purpose === 'preview')
    || lods.find((lod) => lod.purpose === 'desktop')
    || lods[0];
};

const materialsFor = (mesh) => (Array.isArray(mesh.material) ? mesh.material : [mesh.material]);

const normalizeMeshKey = (value) => String(value || '')
  .toLowerCase()
  .replace(/[^a-z0-9]/g, '');

class AnatomyThreeViewer {
  constructor(stage, config) {
    this.stage = stage;
    this.config = config;
    this.lod = selectRuntimeLod(config.asset.lods);
    this.scene = new THREE.Scene();
    this.camera = new THREE.PerspectiveCamera(35, 1, 0.01, 5000);
    this.renderer = null;
    this.controls = null;
    this.canvas = null;
    this.model = null;
    this.resizeObserver = null;
    this.animationFrame = null;
    this.raycaster = new THREE.Raycaster();
    this.pointer = new THREE.Vector2();
    this.pointerStart = null;
    this.meshesByName = new Map();
    this.meshesByNormalizedName = new Map();
    this.structureByMeshName = new Map();
    this.layerMeshes = new Map();
    this.highlightedMeshes = new Set();
    this.materialState = new WeakMap();
    this.defaultCamera = null;
    this.activeLayerId = null;
  }

  mount() {
    this.stage.dataset.state = 'loading';
    this.stage.replaceChildren();

    this.canvas = document.createElement('canvas');
    this.canvas.className = 'hp-anatomy-webgl-canvas';
    this.canvas.setAttribute('role', 'img');
    this.canvas.setAttribute('aria-label', 'מודל אנטומי תלת ממדי אינטראקטיבי');
    this.canvas.tabIndex = 0;
    this.stage.appendChild(this.canvas);

    if (this.config.testOnly) {
      const badge = document.createElement('span');
      badge.className = 'hp-anatomy-fixture-badge';
      badge.textContent = 'TEST FIXTURE. NOT A MEDICAL ASSET.';
      this.stage.appendChild(badge);
    }

    this.renderer = new THREE.WebGLRenderer({
      canvas: this.canvas,
      antialias: true,
      alpha: true,
      powerPreference: 'high-performance'
    });
    this.renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    this.renderer.outputColorSpace = THREE.SRGBColorSpace;
    this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
    this.renderer.toneMappingExposure = 1.05;
    this.renderer.setClearColor(0x000000, 0);

    this.controls = new OrbitControls(this.camera, this.canvas);
    this.controls.enableDamping = true;
    this.controls.dampingFactor = 0.07;
    this.controls.enablePan = false;
    this.controls.minDistance = 0.25;
    this.controls.maxDistance = 100;
    this.controls.addEventListener('change', () => this.render());

    this.scene.add(new THREE.HemisphereLight(0xdff4e9, 0x07251f, 2.7));

    const key = new THREE.DirectionalLight(0xfff4dd, 3.8);
    key.position.set(3, 7, 6);
    this.scene.add(key);

    const rim = new THREE.DirectionalLight(0x8ed5c5, 2.2);
    rim.position.set(-5, 3, -4);
    this.scene.add(rim);

    this.stage.appendChild(this.createCameraControls());
    this.bindPointerSelection();
    this.bindResize();
    this.bindRegionSelection();
    this.canvas.addEventListener('webglcontextlost', (event) => {
      event.preventDefault();
      this.fail('webgl-context-lost');
    }, { once: true });

    this.loadModel();
    this.animate();
  }

  createCameraControls() {
    const tools = document.createElement('div');
    tools.className = 'hp-anatomy-webgl-tools';
    tools.setAttribute('role', 'group');
    tools.setAttribute('aria-label', 'שליטה בתצוגת המודל');

    const actions = [
      ['front', 'מבט קדמי'],
      ['back', 'מבט אחורי'],
      ['reset', 'איפוס מבט']
    ];

    actions.forEach(([action, label]) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.textContent = label;
      button.dataset.anatomyView = action;
      button.addEventListener('click', () => this.setCameraView(action));
      tools.appendChild(button);
    });

    return tools;
  }

  createLayerControls() {
    if (!Array.isArray(this.config.layers) || this.config.layers.length < 2) {
      return;
    }

    const tools = document.createElement('div');
    tools.className = 'hp-anatomy-layer-tools';
    tools.setAttribute('role', 'group');
    tools.setAttribute('aria-label', 'בחירת שכבה אנטומית');

    this.config.layers.forEach((layer) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.textContent = layer.label || layer.id;
      button.dataset.anatomyLayer = layer.id;
      button.setAttribute('aria-pressed', String(layer.id === this.activeLayerId));
      button.addEventListener('click', () => this.setActiveLayer(layer.id));
      tools.appendChild(button);
    });

    this.stage.appendChild(tools);
  }

  bindPointerSelection() {
    this.canvas.addEventListener('pointerdown', (event) => {
      this.pointerStart = { x: event.clientX, y: event.clientY };
    });

    this.canvas.addEventListener('pointerup', (event) => {
      if (!this.pointerStart) {
        return;
      }

      const moved = Math.hypot(event.clientX - this.pointerStart.x, event.clientY - this.pointerStart.y);
      this.pointerStart = null;
      if (moved > 8) {
        return;
      }

      this.pickStructure(event);
    });

    this.canvas.addEventListener('keydown', (event) => {
      if (event.key === 'Home') {
        event.preventDefault();
        this.setCameraView('front');
      }

      if (event.key === 'End') {
        event.preventDefault();
        this.setCameraView('back');
      }
    });
  }

  bindResize() {
    this.resizeObserver = new ResizeObserver(() => this.resize());
    this.resizeObserver.observe(this.stage);
  }

  bindRegionSelection() {
    window.addEventListener('hea-lth:anatomy-region-selected', (event) => {
      const regionId = event.detail && event.detail.regionId;
      if (regionId) {
        this.highlightRegion(regionId, event.detail.source === 'model');
      }
    });
  }

  loadModel() {
    if (!this.lod || typeof this.lod.path !== 'string' || this.lod.path.length === 0) {
      this.fail('missing-runtime-lod');
      return;
    }

    const loader = new GLTFLoader();
    loader.load(
      this.lod.path,
      (gltf) => this.onModelLoaded(gltf),
      undefined,
      () => this.fail('model-load-failed')
    );
  }

  onModelLoaded(gltf) {
    this.model = gltf.scene;
    this.model.traverse((object) => {
      if (!object.isMesh) {
        return;
      }

      object.castShadow = true;
      object.receiveShadow = true;
      this.meshesByName.set(object.name, object);
      this.meshesByNormalizedName.set(normalizeMeshKey(object.name), object);
      this.prepareMaterial(object);
    });

    this.config.structures.forEach((structure) => {
      structure.meshIds.forEach((meshId) => {
        this.structureByMeshName.set(meshId, structure);
        this.structureByMeshName.set(normalizeMeshKey(meshId), structure);
      });
    });

    this.config.layers.forEach((layer) => {
      const meshes = layer.meshIds
        .map((meshId) => this.getMeshById(meshId))
        .filter(Boolean);
      this.layerMeshes.set(layer.id, meshes);
    });

    this.scene.add(this.model);
    this.frameCamera();
    this.applyDefaultLayers();
    this.createLayerControls();
    this.stage.dataset.state = 'ready';
    this.render();

    dispatch('hea-lth:anatomy-viewer-ready', {
      engine: 'three-webgl',
      modelId: this.config.modelId,
      lod: this.lod.id
    });
  }

  prepareMaterial(mesh) {
    materialsFor(mesh).forEach((material) => {
      if (!material || !material.emissive) {
        return;
      }

      this.materialState.set(material, {
        emissive: material.emissive.clone(),
        emissiveIntensity: material.emissiveIntensity
      });
    });
  }

  frameCamera() {
    const box = new THREE.Box3().setFromObject(this.model);
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());
    const largestAxis = Math.max(size.x, size.y, size.z, 1);
    const distance = largestAxis * 1.65;

    this.controls.target.copy(center);
    this.camera.position.set(center.x, center.y + (size.y * 0.06), center.z + distance);
    this.camera.near = Math.max(0.01, largestAxis / 1000);
    this.camera.far = Math.max(100, largestAxis * 100);
    this.camera.updateProjectionMatrix();
    this.controls.update();
    this.defaultCamera = {
      position: this.camera.position.clone(),
      target: this.controls.target.clone(),
      distance
    };
  }

  applyDefaultLayers() {
    const defaults = this.config.layers.filter((layer) => layer.defaultVisible);
    if (defaults.length === 0) {
      return;
    }

    const initialLayer = defaults.find((layer) => layer.kind === 'surface') || defaults[0];
    this.setActiveLayer(initialLayer.id, true);
  }

  setActiveLayer(layerId, initial = false) {
    if (!this.layerMeshes.has(layerId)) {
      return;
    }

    this.activeLayerId = layerId;
    this.layerMeshes.forEach((meshes, id) => {
      meshes.forEach((mesh) => {
        mesh.visible = id === layerId;
      });
    });

    this.stage.querySelectorAll('[data-anatomy-layer]').forEach((button) => {
      button.setAttribute('aria-pressed', String(button.dataset.anatomyLayer === layerId));
    });

    if (!initial) {
      this.clearHighlight();
    }
    this.render();
  }

  pickStructure(event) {
    if (!this.model) {
      return;
    }

    const rect = this.canvas.getBoundingClientRect();
    this.pointer.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    this.pointer.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
    this.raycaster.setFromCamera(this.pointer, this.camera);

    const intersections = this.raycaster.intersectObject(this.model, true);
    const match = intersections.find((intersection) => {
      const structure = this.findStructureForObject(intersection.object);
      return structure && intersection.object.visible;
    });

    if (!match) {
      return;
    }

    const structure = this.findStructureForObject(match.object);
    if (!structure) {
      return;
    }

    this.highlightStructure(structure);
    dispatch('hea-lth:anatomy-viewer-selection', {
      regionId: structure.regionId,
      structureId: structure.id
    });
  }

  findStructureForObject(object) {
    let node = object;
    while (node) {
      if (this.structureByMeshName.has(node.name)) {
        return this.structureByMeshName.get(node.name);
      }
      if (this.structureByMeshName.has(normalizeMeshKey(node.name))) {
        return this.structureByMeshName.get(normalizeMeshKey(node.name));
      }
      node = node.parent;
    }

    return null;
  }

  highlightRegion(regionId, alreadySelectedFromModel) {
    const structure = this.config.structures.find((item) => item.regionId === regionId);
    if (!structure) {
      return;
    }

    this.highlightStructure(structure);
    if (!alreadySelectedFromModel) {
      this.focusStructure(structure);
    }
  }

  highlightStructure(structure) {
    this.clearHighlight();

    structure.meshIds.forEach((meshId) => {
      const mesh = this.getMeshById(meshId);
      if (!mesh || !mesh.visible) {
        return;
      }

      this.highlightedMeshes.add(mesh);
      materialsFor(mesh).forEach((material) => {
        if (!material || !material.emissive) {
          return;
        }

        material.emissive.setHex(0xd6ad53);
        material.emissiveIntensity = 0.58;
      });
    });

    this.render();
  }

  clearHighlight() {
    this.highlightedMeshes.forEach((mesh) => {
      materialsFor(mesh).forEach((material) => {
        const base = this.materialState.get(material);
        if (base && material.emissive) {
          material.emissive.copy(base.emissive);
          material.emissiveIntensity = base.emissiveIntensity;
        }
      });
    });
    this.highlightedMeshes.clear();
  }

  focusStructure(structure) {
    const meshes = structure.meshIds
      .map((meshId) => this.getMeshById(meshId))
      .filter((mesh) => mesh && mesh.visible);
    if (meshes.length === 0) {
      return;
    }

    const bounds = new THREE.Box3();
    meshes.forEach((mesh) => bounds.expandByObject(mesh));
    const target = bounds.getCenter(new THREE.Vector3());
    const size = bounds.getSize(new THREE.Vector3());
    const distance = Math.max(size.length() * 2.6, this.defaultCamera.distance * 0.32);

    this.controls.target.copy(target);
    this.camera.position.set(target.x, target.y + (size.y * 0.2), target.z + distance);
    this.controls.update();
    this.render();
  }

  getMeshById(meshId) {
    return this.meshesByName.get(meshId) || this.meshesByNormalizedName.get(normalizeMeshKey(meshId));
  }

  setCameraView(view) {
    if (!this.defaultCamera) {
      return;
    }

    const target = this.controls.target.clone();
    const distance = this.defaultCamera.distance;

    if (view === 'reset') {
      this.camera.position.copy(this.defaultCamera.position);
      this.controls.target.copy(this.defaultCamera.target);
    } else if (view === 'front') {
      this.camera.position.set(target.x, target.y, target.z + distance);
    } else if (view === 'back') {
      this.camera.position.set(target.x, target.y, target.z - distance);
    }

    this.controls.update();
    this.render();
  }

  resize() {
    if (!this.renderer || !this.canvas) {
      return;
    }

    const { width, height } = this.stage.getBoundingClientRect();
    if (width === 0 || height === 0) {
      return;
    }

    this.camera.aspect = width / height;
    this.camera.updateProjectionMatrix();
    this.renderer.setSize(width, height, false);
    this.render();
  }

  animate() {
    this.animationFrame = window.requestAnimationFrame(() => this.animate());
    this.controls.update();
    this.render();
  }

  render() {
    if (this.renderer) {
      this.renderer.render(this.scene, this.camera);
    }
  }

  fail(reason) {
    if (this.animationFrame) {
      window.cancelAnimationFrame(this.animationFrame);
    }
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
    if (this.renderer) {
      this.renderer.dispose();
    }
    dispatch('hea-lth:anatomy-viewer-failed', { reason });
  }
}

const initialise = () => {
  if (!isApprovedConfiguration(modelConfig)) {
    return;
  }

  const stage = document.querySelector('[data-anatomy-model-stage]');
  if (!stage || !webglAvailable()) {
    dispatch('hea-lth:anatomy-viewer-failed', { reason: 'webgl-unavailable' });
    return;
  }

  const viewer = new AnatomyThreeViewer(stage, modelConfig);
  viewer.mount();
  window.heaLthAnatomyThreeViewer = viewer;
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initialise, { once: true });
} else {
  initialise();
}
