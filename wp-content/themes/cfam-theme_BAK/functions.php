<?php
// remove_action('shutdown', 'wp_ob_end_flush_all', 1);

$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
    $timber = new Timber\Timber();
}

get_template_part('app/lucera');
remove_action('shutdown', 'wp_ob_end_flush_all', 1);
