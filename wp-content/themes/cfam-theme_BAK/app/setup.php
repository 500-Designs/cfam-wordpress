<?php

add_action('after_setup_theme', function () {
    /*
     * Enable support for Post Thumbnails on posts and pages.
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
    */
    add_theme_support('post-thumbnails');
    add_post_type_support('page', 'excerpt');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'main'        => __('Main Navigation'),
        'top_header'  => __('Top Header Navigation'),
        'footer1'      => __('Footer Column 1 Navigation'),
        'footer2'      => __('Footer Column 2 Navigation'),
        'footer3'      => __('Footer Column 3 Navigation'),
        'footer4'      => __('Footer Column 4 Navigation'),
    ]);
});

// custom settings page, use acf to add necessary fields
if (function_exists('acf_add_options_page')) {
    // add global settings in settings menu
    acf_add_options_sub_page([
        'page_title'    => 'Theme Options',
        'menu_title'    => 'Theme Options',
        'parent_slug'   => 'options-general.php',
    ]);
}

// function get_hashed_asset( $asset ) {
//     $map = get_template_directory() . '/dist/manifest.json';
//     static $hash = null;

//     if ( null === $hash ) {
//         $hash = file_exists( $map ) ? json_decode( file_get_contents( $map ), true ) : [];
//     }

//     if ( array_key_exists( $asset, $hash ) ) {
//         return '/dist/' . $hash[ $asset ];
//     }

//     return $asset;
// }

/**
 *
 * wp_enqueue_scripts is the proper hook to use when
 * enqueuing items that are meant to appear on
 * the front end. Despite the name, it is used for
 * enqueuing both scripts and styles.
 * @method assets
 *
 */
function lucera_theme_assets() {
    // Styles
    // wp_enqueue_style('lucera-styles', get_template_directory_uri() . get_hashed_asset('main.css'), [], null, false);
    wp_enqueue_style('lucera-styles', get_template_directory_uri() . '/dist/styles/main.min.css', [], null, false);
    wp_enqueue_style('animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', [], null, false);


    // Remove WP Legacy jQuery and update the version
    // @TODO: local jquery?
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.js', [], null, false);
    wp_enqueue_script('piechart', 'https://cdnjs.cloudflare.com/ajax/libs/easy-pie-chart/2.1.6/jquery.easypiechart.min.js', [], null, false);
    wp_enqueue_script('chartsjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js', [], null, false);
    wp_enqueue_script('chartsjs-plugin-datalabels', 'https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js', [], null, false);
    // wp_enqueue_script('chartsjs-plugin-labels', 'https://unpkg.com/chart.js-plugin-labels-dv/dist/chartjs-plugin-labels.min.js', [], null, false);
    wp_enqueue_script('wowjs', 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js', [], null, false);

    // Scripts
    // wp_enqueue_script('lucera-script', get_template_directory_uri() . get_hashed_asset('main.js'), [], null, true);
    wp_enqueue_script('lucera-script', get_template_directory_uri() . '/dist/scripts/main.min.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'lucera_theme_assets');

/**
 * Custom admin styles
 *
 * @return void
 */
function admin_style() {
    // wp_enqueue_style('lucera-admin-styles', get_template_directory_uri() . get_hashed_asset('admin.css'));
    wp_enqueue_style('lucera-admin-styles', get_template_directory_uri() . '/dist/styles/admin.min.css');
}
add_action('admin_enqueue_scripts', 'admin_style');

/**
 * Registers an editor stylesheet for the theme.
 */
function wpdocs_theme_add_editor_styles() {
    // add_editor_style(get_template_directory_uri()  . get_hashed_asset('admin.css'));
    add_editor_style(get_template_directory_uri() . '/dist/styles/admin.min.css');
}
add_action('admin_init', 'wpdocs_theme_add_editor_styles');

/**
 * Change wp default page.php template name
 */
add_filter('default_page_template_title', function () {
    return __('Default', 'lucera-bootstrap');
});

/**
 * Allow WP to upload .svg files
 */

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {

    global $wp_version;
    if ($wp_version < '4.7.1') {
        return $data;
    }

    $filetype = wp_check_filetype($filename, $mimes);

    return [
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    ];
}, 10, 4);

function lucera_custom_mime_types($mimes) {
    // New allowed mime types.
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    $mimes['doc'] = 'application/msword';

    // Optional. Remove a mime type.
    unset($mimes['exe'], $mimes['dmg']);

    return $mimes;
}
add_filter('upload_mimes', 'lucera_custom_mime_types');

// Force Timber to use https on resized images if we're using SSL
add_filter('timber/image/new_url', function ($url) {
    if (is_ssl()) {
        $url = str_replace('http://', 'https://', $url);
    }
    return $url;
});

register_rest_field('team', 'metadata', array(
    'get_callback' => function ($data) {
        return get_post_meta($data['id'], '', '');
    },
));

register_rest_field(['literature', 'form'], 'file_url', array(
    'get_callback' => function ($data) {
        return get_field('file', $data['id'])['url'];
    },
));

register_rest_field(['literature', 'form'], 'featured_image_html', array(
    'get_callback' => function ($data) {
        return get_the_post_thumbnail($data['id'], 'full');
    },
));

register_rest_field('sec_filing', 'url', array(
    'get_callback' => function ($data) {
        return get_field('link', $data['id']);
    },
));


/**
 * Dynamically add options for the select field
 * Block: Tab Post List
 * Field Name: post_type
 * Field Key: field_62455b3ba0a3e
 */
function acf_load_post_type_field_choices($field) {

    // reset choices
    $field['choices'] = array();

    // get all public post types
    $post_types = get_post_types(array(
        'public' => true,
    ), 'objects');


    // if has rows
    if ($post_types) {
        foreach ($post_types as $post_type) {
            $value = $post_type->name;
            $label = $post_type->labels->singular_name;

            // append to choices
            $field['choices'][$value] = $label;
        }
    }

    // return the field
    return $field;
}
add_filter('acf/load_field/key=field_62455b3ba0a3e', 'acf_load_post_type_field_choices');

//Page Slug Body Class
function add_slug_body_class($classes) {
    global $post;
    if (isset($post)) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}
add_filter('body_class', 'add_slug_body_class');
