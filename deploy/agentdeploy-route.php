<?php
/**
 * Temporary Code Snippets payload for one authenticated deployment.
 *
 * The orchestrator replaces all template markers, creates this as an active
 * global snippet, deploys one allow-listed package, finalizes or rolls back,
 * and deletes the snippet in a finally block.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('HEA_LTH_AGENT_DEPLOY_TOKEN')) {
    define('HEA_LTH_AGENT_DEPLOY_TOKEN', '__DEPLOY_TOKEN__');
}

if (!defined('HEA_LTH_AGENT_DEPLOY_MAX_BYTES')) {
    define('HEA_LTH_AGENT_DEPLOY_MAX_BYTES', __MAX_PACKAGE_BYTES__);
}

if (!function_exists('hea_lth_agent_deploy_allowed_slugs')) {
    function hea_lth_agent_deploy_allowed_slugs(): array
    {
        $decoded = base64_decode('__ALLOWED_SLUGS_B64__', true);
        $slugs = is_string($decoded) ? json_decode($decoded, true) : null;

        return is_array($slugs) ? array_values(array_filter(array_map('sanitize_key', $slugs))) : [];
    }
}

if (!function_exists('hea_lth_agent_deploy_permission')) {
    function hea_lth_agent_deploy_permission(WP_REST_Request $request)
    {
        if (!current_user_can('update_plugins')) {
            return new WP_Error('agentdeploy_forbidden', 'Plugin update capability is required.', ['status' => 403]);
        }

        $provided = (string) $request->get_param('_agent_token');
        if ('' === $provided || !hash_equals(HEA_LTH_AGENT_DEPLOY_TOKEN, $provided)) {
            return new WP_Error('agentdeploy_bad_token', 'The one-time deployment token is invalid.', ['status' => 401]);
        }

        return true;
    }
}

if (!function_exists('hea_lth_agent_deploy_load_filesystem')) {
    function hea_lth_agent_deploy_load_filesystem()
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/theme.php';

        if ('direct' !== get_filesystem_method()) {
            return new WP_Error(
                'agentdeploy_filesystem_not_direct',
                'WordPress filesystem access is not direct; unattended deployment is unsafe on this host.',
                ['status' => 500]
            );
        }

        if (!WP_Filesystem()) {
            return new WP_Error('agentdeploy_filesystem_failed', 'WordPress could not initialize filesystem access.', ['status' => 500]);
        }

        return true;
    }
}

if (!function_exists('hea_lth_agent_deploy_state_key')) {
    function hea_lth_agent_deploy_state_key(string $deploymentId): string
    {
        return 'hea_lth_agent_' . substr(hash('sha256', $deploymentId), 0, 32);
    }
}

if (!function_exists('hea_lth_agent_deploy_validate_id')) {
    function hea_lth_agent_deploy_validate_id(string $deploymentId): bool
    {
        return 1 === preg_match('/^[a-zA-Z0-9._-]{8,96}$/', $deploymentId);
    }
}

if (!function_exists('hea_lth_agent_deploy_ensure_directory')) {
    /**
     * Create a deployment directory recursively and prove WordPress can use it.
     *
     * WP_Filesystem_Direct::mkdir() creates only the final path segment. The
     * rollback root is intentionally nested, so a first deployment on a clean
     * host must use WordPress's recursive directory helper.
     *
     * @return true|WP_Error
     */
    function hea_lth_agent_deploy_ensure_directory(string $path)
    {
        global $wp_filesystem;

        if (!isset($wp_filesystem) || !wp_mkdir_p($path) || !$wp_filesystem->is_dir($path)) {
            return new WP_Error(
                'agentdeploy_backup_dir_failed',
                'Could not create the recursive rollback directory.',
                ['status' => 500]
            );
        }
        if (!$wp_filesystem->is_writable($path)) {
            return new WP_Error(
                'agentdeploy_backup_dir_not_writable',
                'The rollback directory is not writable.',
                ['status' => 500]
            );
        }

        return true;
    }
}

if (!function_exists('hea_lth_agent_deploy_restore')) {
    function hea_lth_agent_deploy_restore(array $state)
    {
        global $wp_filesystem;

        $target = (string) ($state['target'] ?? '');
        $backup = (string) ($state['backup'] ?? '');
        $backupRoot = (string) ($state['backup_root'] ?? '');
        $hadTarget = !empty($state['had_target']);

        if ('' === $target || !isset($wp_filesystem)) {
            return new WP_Error('agentdeploy_bad_rollback_state', 'Rollback state is incomplete.');
        }

        if ($wp_filesystem->exists($target) && !$wp_filesystem->delete($target, true)) {
            return new WP_Error('agentdeploy_rollback_delete_failed', 'Could not remove the failed deployment target.');
        }

        if ($hadTarget) {
            if ('' === $backup || !$wp_filesystem->is_dir($backup)) {
                return new WP_Error('agentdeploy_backup_missing', 'The rollback backup is missing.');
            }

            $copied = copy_dir($backup, $target);
            if (is_wp_error($copied)) {
                return $copied;
            }
        }

        if ('plugin' === ($state['kind'] ?? '') && !empty($state['was_active']) && !empty($state['plugin_file'])) {
            $activated = activate_plugin((string) $state['plugin_file']);
            if (is_wp_error($activated)) {
                return $activated;
            }
        }

        if ('' !== $backupRoot && $wp_filesystem->exists($backupRoot)) {
            $wp_filesystem->delete($backupRoot, true);
        }

        wp_cache_flush();
        do_action('litespeed_purge_all');

        $expectedVersion = (string) ($state['previous_version'] ?? '');
        $restoredVersion = '';
        if ($hadTarget && 'plugin' === ($state['kind'] ?? '') && !empty($state['plugin_file'])) {
            $data = get_plugin_data(trailingslashit(WP_PLUGIN_DIR) . (string) $state['plugin_file'], false, false);
            $restoredVersion = (string) ($data['Version'] ?? '');
        } elseif ($hadTarget && 'theme' === ($state['kind'] ?? '') && !empty($state['slug'])) {
            $restoredVersion = (string) wp_get_theme((string) $state['slug'])->get('Version');
        }

        if ($hadTarget && '' !== $expectedVersion && !hash_equals($expectedVersion, $restoredVersion)) {
            return new WP_Error('agentdeploy_rollback_version_mismatch', 'Rollback did not restore the expected version.');
        }

        return [
            'had_target' => $hadTarget,
            'version' => $restoredVersion,
        ];
    }
}

if (!function_exists('hea_lth_agent_deploy_rollback_payload')) {
    function hea_lth_agent_deploy_rollback_payload(string $deploymentId, $restored): array
    {
        if (is_wp_error($restored)) {
            return [
                'status' => 'failed',
                'deployment_id' => $deploymentId,
                'error' => $restored->get_error_code(),
            ];
        }

        return [
            'status' => 'rolled_back',
            'deployment_id' => $deploymentId,
            'had_target' => !empty($restored['had_target']),
            'version' => (string) ($restored['version'] ?? ''),
        ];
    }
}

if (!function_exists('hea_lth_agent_deploy_preflight')) {
    function hea_lth_agent_deploy_preflight(WP_REST_Request $request)
    {
        $filesystem = hea_lth_agent_deploy_load_filesystem();
        if (is_wp_error($filesystem)) {
            return $filesystem;
        }

        $backupBase = trailingslashit(WP_CONTENT_DIR) . 'upgrade-temp-backup/hea-lth-agent';
        $backupReady = hea_lth_agent_deploy_ensure_directory($backupBase);
        if (is_wp_error($backupReady)) {
            return $backupReady;
        }

        return [
            'status' => 'ready',
            'filesystem_method' => get_filesystem_method(),
            'php_version' => PHP_VERSION,
            'rollback_directory' => 'ready',
            'max_upload_bytes' => min((int) wp_max_upload_size(), (int) HEA_LTH_AGENT_DEPLOY_MAX_BYTES),
            'allowed_slugs' => hea_lth_agent_deploy_allowed_slugs(),
        ];
    }
}

if (!function_exists('hea_lth_agent_deploy_run')) {
    function hea_lth_agent_deploy_run(WP_REST_Request $request)
    {
        $filesystem = hea_lth_agent_deploy_load_filesystem();
        if (is_wp_error($filesystem)) {
            return $filesystem;
        }

        global $wp_filesystem;

        $kind = sanitize_key((string) $request->get_param('kind'));
        $slug = sanitize_key((string) $request->get_param('slug'));
        $mainFile = ltrim((string) $request->get_param('main_file'), '/\\');
        $expectedVersion = sanitize_text_field((string) $request->get_param('version'));
        $expectedSha256 = strtolower((string) $request->get_param('sha256'));
        $deploymentId = sanitize_text_field((string) $request->get_param('deployment_id'));
        $activate = rest_sanitize_boolean($request->get_param('activate'));

        if (!in_array($kind, ['plugin', 'theme'], true)) {
            return new WP_Error('agentdeploy_bad_kind', 'Package kind must be plugin or theme.', ['status' => 400]);
        }
        if (!in_array($slug, hea_lth_agent_deploy_allowed_slugs(), true)) {
            return new WP_Error('agentdeploy_slug_not_allowed', 'Package slug is not allow-listed.', ['status' => 403]);
        }
        if (!hea_lth_agent_deploy_validate_id($deploymentId)) {
            return new WP_Error('agentdeploy_bad_id', 'Deployment ID format is invalid.', ['status' => 400]);
        }
        if (1 !== preg_match('/^[a-f0-9]{64}$/', $expectedSha256)) {
            return new WP_Error('agentdeploy_bad_sha256', 'A valid SHA-256 digest is required.', ['status' => 400]);
        }
        if ('' === $expectedVersion) {
            return new WP_Error('agentdeploy_bad_version', 'Expected package version is required.', ['status' => 400]);
        }
        if ('theme' === $kind && $activate) {
            return new WP_Error('agentdeploy_theme_activation_blocked', 'Automatic theme activation is prohibited.', ['status' => 400]);
        }

        $targetExists = 'plugin' === $kind
            ? is_dir(trailingslashit(WP_PLUGIN_DIR) . $slug)
            : is_dir(trailingslashit(get_theme_root()) . $slug);
        $requiredCapability = 'plugin' === $kind
            ? ($targetExists ? 'update_plugins' : 'install_plugins')
            : ($targetExists ? 'update_themes' : 'install_themes');
        if (!current_user_can($requiredCapability)) {
            return new WP_Error(
                'agentdeploy_package_capability_missing',
                sprintf('The deployment user lacks the required %s capability.', $requiredCapability),
                ['status' => 403]
            );
        }

        $files = $request->get_file_params();
        $package = $files['package'] ?? null;
        if (!is_array($package) || UPLOAD_ERR_OK !== (int) ($package['error'] ?? UPLOAD_ERR_NO_FILE)) {
            return new WP_Error('agentdeploy_upload_missing', 'A successful package upload is required.', ['status' => 400]);
        }

        $tmpName = (string) ($package['tmp_name'] ?? '');
        $size = (int) ($package['size'] ?? 0);
        if ('' === $tmpName || !is_readable($tmpName) || $size < 1 || $size > (int) HEA_LTH_AGENT_DEPLOY_MAX_BYTES) {
            return new WP_Error('agentdeploy_upload_invalid', 'Uploaded package is unreadable or exceeds the allowed size.', ['status' => 400]);
        }

        $actualSha256 = hash_file('sha256', $tmpName);
        if (!is_string($actualSha256) || !hash_equals($expectedSha256, strtolower($actualSha256))) {
            return new WP_Error('agentdeploy_checksum_mismatch', 'Uploaded package checksum does not match.', ['status' => 400]);
        }

        $stateKey = hea_lth_agent_deploy_state_key($deploymentId);
        if (false !== get_transient($stateKey)) {
            return new WP_Error('agentdeploy_replay', 'This deployment ID has already been used.', ['status' => 409]);
        }

        $pluginFile = '';
        $wasActive = false;
        if ('plugin' === $kind) {
            $pluginFile = $slug . '/' . $mainFile;
            if ('' === $mainFile || 0 !== validate_file($pluginFile)) {
                return new WP_Error('agentdeploy_bad_main_file', 'Plugin main file is invalid.', ['status' => 400]);
            }
            $target = trailingslashit(WP_PLUGIN_DIR) . $slug;
            $wasActive = is_plugin_active($pluginFile);
        } else {
            $target = trailingslashit(get_theme_root()) . $slug;
        }

        $backupRoot = trailingslashit(WP_CONTENT_DIR) . 'upgrade-temp-backup/hea-lth-agent/' . $deploymentId;
        $backup = trailingslashit($backupRoot) . $slug;
        $hadTarget = $wp_filesystem->is_dir($target);
        $previousVersion = '';
        if ($hadTarget && 'plugin' === $kind) {
            $previousData = get_plugin_data(trailingslashit(WP_PLUGIN_DIR) . $pluginFile, false, false);
            $previousVersion = (string) ($previousData['Version'] ?? '');
        } elseif ($hadTarget) {
            $previousVersion = (string) wp_get_theme($slug)->get('Version');
        }

        if ($wp_filesystem->exists($backupRoot)) {
            $wp_filesystem->delete($backupRoot, true);
        }
        $backupReady = hea_lth_agent_deploy_ensure_directory($backupRoot);
        if (is_wp_error($backupReady)) {
            return $backupReady;
        }
        if ($hadTarget) {
            $copied = copy_dir($target, $backup);
            if (is_wp_error($copied)) {
                $wp_filesystem->delete($backupRoot, true);
                return $copied;
            }
        }

        $state = [
            'deployment_id' => $deploymentId,
            'kind' => $kind,
            'slug' => $slug,
            'target' => $target,
            'backup_root' => $backupRoot,
            'backup' => $backup,
            'had_target' => $hadTarget,
            'plugin_file' => $pluginFile,
            'was_active' => $wasActive,
            'previous_version' => $previousVersion,
        ];
        set_transient($stateKey, $state, HOUR_IN_SECONDS);

        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = 'plugin' === $kind ? new Plugin_Upgrader($skin) : new Theme_Upgrader($skin);
        $result = $upgrader->install($tmpName, ['overwrite_package' => true]);

        if (is_wp_error($result) || true !== $result) {
            $restored = hea_lth_agent_deploy_restore($state);
            delete_transient($stateKey);
            return new WP_Error(
                'agentdeploy_install_failed',
                is_wp_error($result) ? $result->get_error_message() : 'WordPress did not report a successful install.',
                [
                    'status' => 500,
                    'rolled_back' => !is_wp_error($restored),
                    'rollback' => hea_lth_agent_deploy_rollback_payload($deploymentId, $restored),
                    'messages' => $skin->get_upgrade_messages(),
                ]
            );
        }

        if ('plugin' === $kind && ($activate || $wasActive)) {
            $activated = activate_plugin($pluginFile);
            if (is_wp_error($activated)) {
                $restored = hea_lth_agent_deploy_restore($state);
                delete_transient($stateKey);
                return new WP_Error(
                    'agentdeploy_activation_failed',
                    $activated->get_error_message(),
                    [
                        'status' => 500,
                        'rolled_back' => !is_wp_error($restored),
                        'rollback' => hea_lth_agent_deploy_rollback_payload($deploymentId, $restored),
                    ]
                );
            }
        }

        wp_cache_flush();
        do_action('litespeed_purge_all');

        if ('plugin' === $kind) {
            $data = get_plugin_data(trailingslashit(WP_PLUGIN_DIR) . $pluginFile, false, false);
            $installedVersion = (string) ($data['Version'] ?? '');
        } else {
            $installedVersion = (string) wp_get_theme($slug)->get('Version');
        }

        if (!hash_equals($expectedVersion, $installedVersion)) {
            $restored = hea_lth_agent_deploy_restore($state);
            delete_transient($stateKey);
            return new WP_Error(
                'agentdeploy_version_mismatch',
                'Installed package version does not match the expected version.',
                [
                    'status' => 500,
                    'installed_version' => $installedVersion,
                    'rolled_back' => !is_wp_error($restored),
                    'rollback' => hea_lth_agent_deploy_rollback_payload($deploymentId, $restored),
                ]
            );
        }

        error_log(sprintf('Hea-lth deployment installed %s %s version %s (%s).', $kind, $slug, $installedVersion, $deploymentId));

        return [
            'status' => 'installed_pending_verification',
            'deployment_id' => $deploymentId,
            'kind' => $kind,
            'slug' => $slug,
            'version' => $installedVersion,
            'active' => 'plugin' === $kind ? is_plugin_active($pluginFile) : false,
            'messages' => $skin->get_upgrade_messages(),
        ];
    }
}

if (!function_exists('hea_lth_agent_deploy_rollback')) {
    function hea_lth_agent_deploy_rollback(WP_REST_Request $request)
    {
        $filesystem = hea_lth_agent_deploy_load_filesystem();
        if (is_wp_error($filesystem)) {
            return $filesystem;
        }

        $deploymentId = sanitize_text_field((string) $request->get_param('deployment_id'));
        if (!hea_lth_agent_deploy_validate_id($deploymentId)) {
            return new WP_Error('agentdeploy_bad_id', 'Deployment ID format is invalid.', ['status' => 400]);
        }

        $stateKey = hea_lth_agent_deploy_state_key($deploymentId);
        $state = get_transient($stateKey);
        if (!is_array($state)) {
            return new WP_Error('agentdeploy_state_missing', 'No rollback state exists for this deployment.', ['status' => 404]);
        }

        $result = hea_lth_agent_deploy_restore($state);
        if (is_wp_error($result)) {
            return $result;
        }

        delete_transient($stateKey);
        error_log(sprintf('Hea-lth deployment rolled back (%s).', $deploymentId));

        return [
            'status' => 'rolled_back',
            'deployment_id' => $deploymentId,
            'had_target' => !empty($result['had_target']),
            'version' => (string) ($result['version'] ?? ''),
        ];
    }
}

if (!function_exists('hea_lth_agent_deploy_finalize')) {
    function hea_lth_agent_deploy_finalize(WP_REST_Request $request)
    {
        $filesystem = hea_lth_agent_deploy_load_filesystem();
        if (is_wp_error($filesystem)) {
            return $filesystem;
        }

        global $wp_filesystem;

        $deploymentId = sanitize_text_field((string) $request->get_param('deployment_id'));
        if (!hea_lth_agent_deploy_validate_id($deploymentId)) {
            return new WP_Error('agentdeploy_bad_id', 'Deployment ID format is invalid.', ['status' => 400]);
        }

        $stateKey = hea_lth_agent_deploy_state_key($deploymentId);
        $state = get_transient($stateKey);
        if (!is_array($state)) {
            return new WP_Error('agentdeploy_state_missing', 'No deployment state exists to finalize.', ['status' => 404]);
        }

        $backupRoot = (string) ($state['backup_root'] ?? '');
        if ('' !== $backupRoot && $wp_filesystem->exists($backupRoot)) {
            $wp_filesystem->delete($backupRoot, true);
        }
        delete_transient($stateKey);

        return ['status' => 'finalized', 'deployment_id' => $deploymentId];
    }
}

add_action('rest_api_init', static function (): void {
    $permission = 'hea_lth_agent_deploy_permission';
    register_rest_route('agentdeploy/v1', '/preflight', [
        'methods' => WP_REST_Server::CREATABLE,
        'permission_callback' => $permission,
        'callback' => 'hea_lth_agent_deploy_preflight',
    ]);
    register_rest_route('agentdeploy/v1', '/run', [
        'methods' => WP_REST_Server::CREATABLE,
        'permission_callback' => $permission,
        'callback' => 'hea_lth_agent_deploy_run',
    ]);
    register_rest_route('agentdeploy/v1', '/rollback', [
        'methods' => WP_REST_Server::CREATABLE,
        'permission_callback' => $permission,
        'callback' => 'hea_lth_agent_deploy_rollback',
    ]);
    register_rest_route('agentdeploy/v1', '/finalize', [
        'methods' => WP_REST_Server::CREATABLE,
        'permission_callback' => $permission,
        'callback' => 'hea_lth_agent_deploy_finalize',
    ]);
});
