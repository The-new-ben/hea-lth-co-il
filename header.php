<?php
/**
 * Header template.
 */
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site-shell">
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                <span class="logo-mark" aria-hidden="true">H</span>
                <span><?php bloginfo('name'); ?></span>
            </a>
            <nav class="nav" aria-label="<?php esc_attr_e('Primary navigation', 'health-revenue'); ?>">
                <a href="#money">שירותים פרטיים</a>
                <a href="#process">תהליך</a>
                <a href="#lead">תיאום פנייה</a>
            </nav>
            <a class="header-cta" href="#lead">בדיקת התאמה</a>
        </div>
    </header>
