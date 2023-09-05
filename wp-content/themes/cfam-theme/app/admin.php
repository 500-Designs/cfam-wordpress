<?php

/*** Admin Environment Label ***/

function admin_environment($wp_admin_bar) {
    $noticeMessage = '';
    if (function_exists( 'is_wpe' )) {
        if (is_wpe()) {
            // If we're on the WP Engine server
            $server = $_SERVER['SERVER_NAME'];

            if ($server == 'cfinfrafunddev.wpengine.com') {
                // Dev
                $noticeMessage = 'DEVELOPMENT';
            } elseif ($server == 'cfinfrafundqa.wpengine.com') {
                // QA
                $noticeMessage = 'STAGING';
            } else {
                // Production
                $noticeMessage = 'PRODUCTION';
            }
        } else {
            // Local
            $noticeMessage = 'LOCAL';
        }
    }

    $wp_admin_bar->add_node([
        'id'        => 'environment',
        'title'     => $noticeMessage,
    ]);
}
add_action('admin_bar_menu', 'admin_environment', 1);

/*** Admin Color Schemes ***/
// Change the color of the admin bar on the front end if it is displayed
function change_bar_color() {
    $barColor = 'DarkMagenta';
    
    if (function_exists( 'is_wpe' )) {
        if (is_wpe()) {
            // If we're on the WP Engine server
            $server = $_SERVER['SERVER_NAME'];

            if ($server == 'cfinfrafunddev.wpengine.com') {
                // Dev
                $barColor = 'DeepSkyBlue';
            } elseif ($server == 'cfinfrafundqa.wpengine.com') {
                // QA
                $barColor = 'PaleVioletRed';
            } else {
                // Production
                $barColor = 'FireBrick';
            }
        }
    }

    echo '<style> #wpadminbar{ background: ' . $barColor . ' !important; } #wp-admin-bar-environment .ab-empty-item {font-weight: bold;}</style>';
}
add_action('wp_head', 'change_bar_color');
add_action('admin_head', 'change_bar_color');

// /**
//  * Add styles/scripts to login page
//  *
//  * @link https://codex.wordpress.org/Customizing_the_Login_Form
//  * @return void
//  */
// function my_login_stylesheet() {
//     wp_enqueue_style('custom-login', get_stylesheet_directory_uri() . '/resources/assets/styles/login.css');
//     // wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/style-login.js' );
// }
// add_action('login_enqueue_scripts', 'my_login_stylesheet');

// /**
//  * Update login logo url
//  *
//  * @link https://codex.wordpress.org/Customizing_the_Login_Form
//  * @return void
//  */
// function my_login_logo_url() {
//     return home_url();
// }
// add_filter('login_headerurl', 'my_login_logo_url');
