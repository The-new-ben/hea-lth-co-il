# Anatomy engine technical fixture

`anatomy-engine-test-fixture.glb` is generated locally by Blender to test the
Hea-lth WebGL loader, orbit controls, mesh hit testing, layer visibility, and
semantic handoff.

It is not a medical model. It is not a visual fallback. It is not a production
asset. It must never be copied into `theme-src`, uploaded to WordPress, or used
in public screenshots as if it were the licensed human anatomy experience.

Generate it locally:

```powershell
& 'C:\Program Files\Blender Foundation\Blender 5.1\blender.exe' --background --python tooling\anatomy-fixture\generate_fixture.py
```
