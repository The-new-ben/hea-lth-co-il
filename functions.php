<?php
/**
 * Health Revenue theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

function health_revenue_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_editor_style('style.css');
    register_nav_menus([
        'primary' => __('Primary Menu', 'health-revenue'),
    ]);
}
add_action('after_setup_theme', 'health_revenue_setup');

function health_revenue_assets(): void {
    wp_enqueue_style('health-revenue-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'health_revenue_assets');

function health_revenue_lead_statuses(): array {
    return [
        'new' => __('New', 'health-revenue'),
        'triage_needed' => __('Triage needed', 'health-revenue'),
        'needs_clinical_review' => __('Needs clinical review', 'health-revenue'),
        'provider_match' => __('Provider match', 'health-revenue'),
        'appointment_requested' => __('Appointment requested', 'health-revenue'),
        'refund_route' => __('Refund route', 'health-revenue'),
        'closed_lost' => __('Closed lost', 'health-revenue'),
    ];
}

function health_revenue_register_lead_type(): void {
    register_post_type('health_lead', [
        'labels' => [
            'name' => __('Health Leads', 'health-revenue'),
            'singular_name' => __('Health Lead', 'health-revenue'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-heart',
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);

    $meta_fields = [
        'lead_name',
        'lead_phone',
        'lead_email',
        'service_category',
        'specialty_needed',
        'lead_city',
        'lead_urgency',
        'payer_type',
        'insurance_provider',
        'preferred_route',
        'age_group',
        'lead_status',
        'lead_score',
        'lead_priority',
        'lead_consent',
        'privacy_ack',
        'landing_url',
        'referrer_url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    foreach ($meta_fields as $field) {
        register_post_meta('health_lead', $field, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => static function (): bool {
                return current_user_can('edit_posts');
            },
        ]);
    }
}
add_action('init', 'health_revenue_register_lead_type');

function health_revenue_clean(string $key): string {
    return isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
}

function health_revenue_clean_url(string $key): string {
    return isset($_POST[$key]) ? esc_url_raw(wp_unslash($_POST[$key])) : '';
}

function health_revenue_initial_status(string $service_category, string $urgency, string $preferred_route): string {
    $context = $service_category . ' ' . $urgency . ' ' . $preferred_route;

    if (strpos($context, 'חוות דעת') !== false || strpos($context, 'שנייה') !== false) {
        return 'needs_clinical_review';
    }

    if (strpos($context, 'החזר') !== false || strpos($context, 'ביטוח') !== false) {
        return 'refund_route';
    }

    if (strpos($context, 'פרימיום') !== false || strpos($context, 'השוואת אפשרויות') !== false) {
        return 'provider_match';
    }

    if (strpos($context, 'רופא פרטי') !== false || strpos($context, 'תור מהיר') !== false || strpos($context, 'מומחה') !== false) {
        return 'appointment_requested';
    }

    if (strpos($context, 'MRI') !== false || strpos($context, 'CT') !== false || strpos($context, 'ביקור') !== false || strpos($context, 'היום') !== false || strpos($context, 'מחר') !== false) {
        return 'triage_needed';
    }

    return 'new';
}

function health_revenue_priority_from_score(int $score): string {
    if ($score >= 80) {
        return 'Hot';
    }

    if ($score >= 50) {
        return 'Warm';
    }

    return 'Watch';
}

function health_revenue_score_lead(string $service_category, string $specialty_needed, string $city, string $urgency, string $payer_type, string $preferred_route): int {
    $score = 20;

    if ($service_category !== '') {
        $score += 20;
    }
    if ($specialty_needed !== '') {
        $score += 10;
    }
    if ($city !== '') {
        $score += 10;
    }
    if ($urgency !== '') {
        $score += 20;
    }
    if ($payer_type !== '') {
        $score += 10;
    }
    if ($preferred_route !== '') {
        $score += 10;
    }

    return min(100, $score);
}

function health_revenue_handle_lead(): void {
    if (!isset($_POST['health_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['health_nonce'])), 'health_lead')) {
        wp_safe_redirect(add_query_arg('lead', 'bad_nonce', home_url('/')));
        exit;
    }

    if (health_revenue_clean('company_website') !== '') {
        wp_safe_redirect(add_query_arg('lead', 'received', home_url('/')));
        exit;
    }

    $name = health_revenue_clean('lead_name');
    $phone = health_revenue_clean('lead_phone');
    $email = sanitize_email(wp_unslash($_POST['lead_email'] ?? ''));
    $service_category = health_revenue_clean('service_category');
    $specialty_needed = health_revenue_clean('specialty_needed');
    $city = health_revenue_clean('lead_city');
    $urgency = health_revenue_clean('lead_urgency');
    $payer_type = health_revenue_clean('payer_type');
    $insurance_provider = health_revenue_clean('insurance_provider');
    $preferred_route = health_revenue_clean('preferred_route');
    $age_group = health_revenue_clean('age_group');
    $message = sanitize_textarea_field(wp_unslash($_POST['lead_message'] ?? ''));
    $consent = isset($_POST['lead_consent']) ? 'yes' : '';
    $privacy_ack = isset($_POST['privacy_ack']) ? 'yes' : '';

    if ($name === '' || $phone === '' || $service_category === '' || $consent !== 'yes' || $privacy_ack !== 'yes') {
        wp_safe_redirect(add_query_arg('lead', 'missing_required', home_url('/#lead')));
        exit;
    }

    $initial_status = health_revenue_initial_status($service_category, $urgency, $preferred_route);
    $lead_score = health_revenue_score_lead($service_category, $specialty_needed, $city, $urgency, $payer_type, $preferred_route);
    $lead_priority = health_revenue_priority_from_score($lead_score);

    $title = sprintf('%s - %s - %s', $name, $service_category, current_time('Y-m-d H:i'));
    $lead_id = wp_insert_post([
        'post_type' => 'health_lead',
        'post_status' => 'private',
        'post_title' => $title,
        'post_content' => $message,
    ], true);

    if (!is_wp_error($lead_id)) {
        $fields = [
            'lead_name' => $name,
            'lead_phone' => $phone,
            'lead_email' => $email,
            'service_category' => $service_category,
            'specialty_needed' => $specialty_needed,
            'lead_city' => $city,
            'lead_urgency' => $urgency,
            'payer_type' => $payer_type,
            'insurance_provider' => $insurance_provider,
            'preferred_route' => $preferred_route,
            'age_group' => $age_group,
            'lead_status' => $initial_status,
            'lead_score' => (string) $lead_score,
            'lead_priority' => $lead_priority,
            'lead_consent' => $consent,
            'privacy_ack' => $privacy_ack,
            'landing_url' => health_revenue_clean_url('landing_url') ?: home_url('/'),
            'referrer_url' => health_revenue_clean_url('referrer_url') ?: esc_url_raw(wp_get_referer() ?: ''),
            'utm_source' => health_revenue_clean('utm_source'),
            'utm_medium' => health_revenue_clean('utm_medium'),
            'utm_campaign' => health_revenue_clean('utm_campaign'),
            'utm_term' => health_revenue_clean('utm_term'),
            'utm_content' => health_revenue_clean('utm_content'),
        ];

        foreach ($fields as $key => $value) {
            update_post_meta($lead_id, $key, $value);
        }

        $admin_url = admin_url('post.php?post=' . absint($lead_id) . '&action=edit');
        wp_mail(get_option('admin_email'), 'Health lead received', 'New private health lead received. Review it inside WordPress: ' . $admin_url);
    }

    wp_safe_redirect(add_query_arg('lead', 'received', home_url('/')));
    exit;
}
add_action('admin_post_nopriv_health_lead', 'health_revenue_handle_lead');
add_action('admin_post_health_lead', 'health_revenue_handle_lead');

function health_revenue_lead_columns(array $columns): array {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ($key === 'title') {
            $new_columns['lead_phone'] = __('Phone', 'health-revenue');
            $new_columns['service_category'] = __('Service', 'health-revenue');
            $new_columns['lead_urgency'] = __('Urgency', 'health-revenue');
            $new_columns['lead_status'] = __('Status', 'health-revenue');
            $new_columns['lead_priority'] = __('Priority', 'health-revenue');
            $new_columns['utm_source'] = __('UTM source', 'health-revenue');
            $new_columns['landing_url'] = __('Landing URL', 'health-revenue');
        }
    }
    return $new_columns;
}
add_filter('manage_health_lead_posts_columns', 'health_revenue_lead_columns');

function health_revenue_lead_column_content(string $column, int $post_id): void {
    if (in_array($column, ['lead_phone', 'service_category', 'lead_urgency', 'lead_priority', 'utm_source', 'landing_url'], true)) {
        echo esc_html((string) get_post_meta($post_id, $column, true));
        return;
    }

    if ($column === 'lead_status') {
        $statuses = health_revenue_lead_statuses();
        $status = (string) get_post_meta($post_id, 'lead_status', true);
        echo esc_html($statuses[$status] ?? $status);
    }
}
add_action('manage_health_lead_posts_custom_column', 'health_revenue_lead_column_content', 10, 2);

function health_revenue_lead_meta_box(): void {
    add_meta_box('health_lead_details', __('Lead details', 'health-revenue'), 'health_revenue_render_lead_meta_box', 'health_lead', 'normal', 'high');
}
add_action('add_meta_boxes_health_lead', 'health_revenue_lead_meta_box');

function health_revenue_render_lead_meta_box(WP_Post $post): void {
    wp_nonce_field('health_lead_admin', 'health_lead_admin_nonce');
    $statuses = health_revenue_lead_statuses();
    $current_status = (string) get_post_meta($post->ID, 'lead_status', true);
    $rows = [
        __('Name', 'health-revenue') => get_post_meta($post->ID, 'lead_name', true),
        __('Phone', 'health-revenue') => get_post_meta($post->ID, 'lead_phone', true),
        __('Email', 'health-revenue') => get_post_meta($post->ID, 'lead_email', true),
        __('Service', 'health-revenue') => get_post_meta($post->ID, 'service_category', true),
        __('Specialty', 'health-revenue') => get_post_meta($post->ID, 'specialty_needed', true),
        __('City', 'health-revenue') => get_post_meta($post->ID, 'lead_city', true),
        __('Urgency', 'health-revenue') => get_post_meta($post->ID, 'lead_urgency', true),
        __('Payer type', 'health-revenue') => get_post_meta($post->ID, 'payer_type', true),
        __('Insurance provider', 'health-revenue') => get_post_meta($post->ID, 'insurance_provider', true),
        __('Preferred route', 'health-revenue') => get_post_meta($post->ID, 'preferred_route', true),
        __('Age group', 'health-revenue') => get_post_meta($post->ID, 'age_group', true),
        __('Priority', 'health-revenue') => get_post_meta($post->ID, 'lead_priority', true),
        __('Lead score', 'health-revenue') => get_post_meta($post->ID, 'lead_score', true),
        __('Landing URL', 'health-revenue') => get_post_meta($post->ID, 'landing_url', true),
        __('Referrer URL', 'health-revenue') => get_post_meta($post->ID, 'referrer_url', true),
        __('UTM source', 'health-revenue') => get_post_meta($post->ID, 'utm_source', true),
        __('UTM campaign', 'health-revenue') => get_post_meta($post->ID, 'utm_campaign', true),
    ];
    ?>
    <p>
        <label for="lead_status"><strong><?php esc_html_e('Status', 'health-revenue'); ?></strong></label>
        <select id="lead_status" name="lead_status">
            <?php foreach ($statuses as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($current_status ?: 'new', $value); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <table class="widefat striped">
        <tbody>
            <?php foreach ($rows as $label => $value) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($label); ?></th>
                    <td><?php echo esc_html((string) $value); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

function health_revenue_save_lead_status(int $post_id): void {
    if (!isset($_POST['health_lead_admin_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['health_lead_admin_nonce'])), 'health_lead_admin')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $status = health_revenue_clean('lead_status');
    if (array_key_exists($status, health_revenue_lead_statuses())) {
        update_post_meta($post_id, 'lead_status', $status);
    }
}
add_action('save_post_health_lead', 'health_revenue_save_lead_status');

function health_revenue_lead_board_menu(): void {
    add_submenu_page(
        'edit.php?post_type=health_lead',
        __('Revenue Board', 'health-revenue'),
        __('Revenue Board', 'health-revenue'),
        'edit_posts',
        'health-revenue-board',
        'health_revenue_render_lead_board'
    );
}
add_action('admin_menu', 'health_revenue_lead_board_menu');

function health_revenue_render_lead_board(): void {
    $statuses = health_revenue_lead_statuses();
    $leads = get_posts([
        'post_type' => 'health_lead',
        'post_status' => 'private',
        'numberposts' => 50,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    $status_counts = array_fill_keys(array_values($statuses), 0);
    $source_counts = [];
    $landing_counts = [];

    foreach ($leads as $lead) {
        $status_key = (string) get_post_meta($lead->ID, 'lead_status', true);
        $status_label = $statuses[$status_key ?: 'new'] ?? $status_key;
        $status_counts[$status_label] = ($status_counts[$status_label] ?? 0) + 1;

        $source = trim((string) get_post_meta($lead->ID, 'utm_source', true)) ?: __('Direct / unknown', 'health-revenue');
        $landing = trim((string) get_post_meta($lead->ID, 'landing_url', true)) ?: __('Home / unknown', 'health-revenue');
        $source_counts[$source] = ($source_counts[$source] ?? 0) + 1;
        $landing_counts[$landing] = ($landing_counts[$landing] ?? 0) + 1;
    }

    $status_counts = array_filter($status_counts, static function ($count): bool {
        return (int) $count > 0;
    });
    arsort($status_counts);
    arsort($source_counts);
    arsort($landing_counts);

    $render_counts = static function (array $counts, string $empty_label): void {
        if (!$counts) {
            echo '<tr><td colspan="2">' . esc_html($empty_label) . '</td></tr>';
            return;
        }

        foreach (array_slice($counts, 0, 10, true) as $label => $count) {
            echo '<tr><td style="word-break: break-word;">' . esc_html((string) $label) . '</td><td>' . esc_html(number_format_i18n((int) $count)) . '</td></tr>';
        }
    };
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Revenue Board', 'health-revenue'); ?></h1>
        <p><?php esc_html_e('Last 50 private leads by status, source, and landing page for weekly revenue triage.', 'health-revenue'); ?></p>
        <p><a class="button button-primary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=health_export_lead_board'), 'health_export_lead_board')); ?>"><?php esc_html_e('Export board CSV', 'health-revenue'); ?></a></p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin: 16px 0;">
            <div class="card" style="max-width: none;">
                <h2><?php esc_html_e('Status', 'health-revenue'); ?></h2>
                <table class="widefat striped"><tbody><?php $render_counts($status_counts, __('No leads yet.', 'health-revenue')); ?></tbody></table>
            </div>
            <div class="card" style="max-width: none;">
                <h2><?php esc_html_e('UTM source', 'health-revenue'); ?></h2>
                <table class="widefat striped"><tbody><?php $render_counts($source_counts, __('No source data yet.', 'health-revenue')); ?></tbody></table>
            </div>
            <div class="card" style="max-width: none;">
                <h2><?php esc_html_e('Landing URL', 'health-revenue'); ?></h2>
                <table class="widefat striped"><tbody><?php $render_counts($landing_counts, __('No landing data yet.', 'health-revenue')); ?></tbody></table>
            </div>
        </div>
        <h2><?php esc_html_e('Latest leads', 'health-revenue'); ?></h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e('Date', 'health-revenue'); ?></th>
                    <th scope="col"><?php esc_html_e('Lead', 'health-revenue'); ?></th>
                    <th scope="col"><?php esc_html_e('Status', 'health-revenue'); ?></th>
                    <th scope="col"><?php esc_html_e('UTM source', 'health-revenue'); ?></th>
                    <th scope="col"><?php esc_html_e('Landing URL', 'health-revenue'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$leads) : ?>
                    <tr><td colspan="5"><?php esc_html_e('No leads yet.', 'health-revenue'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($leads as $lead) : ?>
                    <?php
                    $status_key = (string) get_post_meta($lead->ID, 'lead_status', true);
                    $landing_url = (string) get_post_meta($lead->ID, 'landing_url', true);
                    $lead_title = get_the_title($lead) ?: sprintf(__('Lead #%d', 'health-revenue'), $lead->ID);
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_the_date('', $lead)); ?></td>
                        <td><a href="<?php echo esc_url((string) get_edit_post_link($lead->ID)); ?>"><?php echo esc_html($lead_title); ?></a></td>
                        <td><?php echo esc_html($statuses[$status_key ?: 'new'] ?? $status_key); ?></td>
                        <td><?php echo esc_html((string) get_post_meta($lead->ID, 'utm_source', true)); ?></td>
                        <td style="word-break: break-all;"><?php echo $landing_url ? '<a href="' . esc_url($landing_url) . '" target="_blank" rel="noopener">' . esc_html($landing_url) . '</a>' : esc_html__('Home / unknown', 'health-revenue'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function health_revenue_export_lead_board(): void {
    if (!current_user_can('edit_posts')) {
        wp_die(esc_html__('You are not allowed to export this board.', 'health-revenue'));
    }

    check_admin_referer('health_export_lead_board');

    $statuses = health_revenue_lead_statuses();
    $leads = get_posts([
        'post_type' => 'health_lead',
        'post_status' => 'private',
        'numberposts' => 50,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="health-lead-board-' . gmdate('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    if ($output === false) {
        wp_die(esc_html__('Could not open CSV output stream.', 'health-revenue'));
    }

    fputcsv($output, ['lead_id', 'date', 'status', 'priority', 'score', 'utm_source', 'utm_medium', 'utm_campaign', 'landing_url', 'edit_url']);

    foreach ($leads as $lead) {
        $status_key = (string) get_post_meta($lead->ID, 'lead_status', true);
        fputcsv($output, [
            $lead->ID,
            get_the_date('Y-m-d H:i:s', $lead),
            $statuses[$status_key ?: 'new'] ?? $status_key,
            get_post_meta($lead->ID, 'lead_priority', true),
            get_post_meta($lead->ID, 'lead_score', true),
            get_post_meta($lead->ID, 'utm_source', true),
            get_post_meta($lead->ID, 'utm_medium', true),
            get_post_meta($lead->ID, 'utm_campaign', true),
            get_post_meta($lead->ID, 'landing_url', true),
            get_edit_post_link($lead->ID, 'raw'),
        ]);
    }

    fclose($output);
    exit;
}
add_action('admin_post_health_export_lead_board', 'health_revenue_export_lead_board');

function health_revenue_medical_disclosure(): string {
    return '<aside class="medical-disclosure">' . esc_html__('גילוי רפואי חשוב: האתר אינו מספק אבחון, טיפול, המלצה רפואית או שירות חירום. המידע מיועד לתיאום שירותים בלבד ואינו מחליף התייעצות עם רופא מורשה. במקרה חירום, כאב חזה, קוצר נשימה, סימני שבץ, דימום חמור או סכנת חיים יש לפנות מיד למד״א 101 או לחדר מיון.', 'health-revenue') . '</aside>';
}
add_shortcode('health_medical_disclosure', 'health_revenue_medical_disclosure');

function health_revenue_schema(): void {
    if (!is_front_page()) {
        return;
    }
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'MedicalBusiness',
        'name' => 'Hea-lth',
        'url' => home_url('/'),
        'areaServed' => 'IL',
        'inLanguage' => 'he-IL',
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}
add_action('wp_head', 'health_revenue_schema');

function health_revenue_attribution_script(): void {
    if (!is_front_page()) {
        return;
    }
    ?>
    <script>
    (function () {
        var params = new URLSearchParams(window.location.search);
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (key) {
            var value = params.get(key) || window.localStorage.getItem('health_' + key) || '';
            if (params.get(key)) {
                window.localStorage.setItem('health_' + key, params.get(key));
            }
            var input = document.querySelector('[name="' + key + '"]');
            if (input) {
                input.value = value;
            }
        });
        var landing = document.querySelector('[name="landing_url"]');
        var referrer = document.querySelector('[name="referrer_url"]');
        if (landing) {
            landing.value = window.location.href;
        }
        if (referrer) {
            referrer.value = document.referrer;
        }
    }());
    </script>
    <?php
}
add_action('wp_footer', 'health_revenue_attribution_script');

function health_revenue_conversion_event_script(): void {
    if (!is_front_page() || !isset($_GET['lead']) || sanitize_text_field(wp_unslash($_GET['lead'])) !== 'received') {
        return;
    }

    $payload = [
        'event' => 'generate_lead',
        'lead_form' => 'health_lead',
        'portfolio_site' => 'hea-lth.co.il',
        'lead_result' => 'received',
        'conversion_source' => 'wordpress_thank_you_query',
    ];
    ?>
    <script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(<?php echo wp_json_encode($payload); ?>);
    </script>
    <?php
}
add_action('wp_footer', 'health_revenue_conversion_event_script');
