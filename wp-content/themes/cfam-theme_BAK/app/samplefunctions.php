<?php

/*
 * Sample functions you may need for the project
 *
 * To use, copy/paste into setup.php
 */

// Add custom image sizes
if ( function_exists( 'add_image_size' ) ) {
    add_image_size( 'leadership_profile', 360, 300, true );
    add_image_size( 'leadership_profile_lazy', 36, 30, true );
}

// Adds support for editor font sizes.
add_theme_support( 'editor-font-sizes', array(
    array(
        'name'      => __( 'medium', 'lucera-bootstrap' ),
        'shortName' => __( 'Medium', 'lucera-bootstrap' ),
        'size'      => 24,
        'slug'      => 'medium'
    ),
    array(
        'name'      => __( 'large', 'lucera-bootstrap' ),
        'shortName' => __( 'Large', 'lucera-bootstrap' ),
        'size'      => 46,
        'slug'      => 'large'
    ),
    array(
        'name'      => __( 'huge', 'lucera-bootstrap' ),
        'shortName' => __( 'Huge', 'lucera-bootstrap' ),
        'size'      => 65,
        'slug'      => 'huge'
    )
) );

// Customizes color palette in editor
function wpdc_add_custom_gutenberg_color_palette() {
    add_theme_support( 'editor-gradient-presets', [] );
    add_theme_support( 'disable-custom-gradients' );
    add_theme_support(
        'editor-color-palette',
        [
            [
                'name'  => esc_html__( 'Black', 'wpdc' ),
                'slug'  => 'black',
                'color' => '#000',
            ],
            [
                'name'  => esc_html__( 'White', 'wpdc' ),
                'slug'  => 'white',
                'color' => '#fff',
            ],
            [
                'name'  => esc_html__( 'Gray', 'wpdc' ),
                'slug'  => 'gray',
                'color' => '#64666b',
            ],
            [
                'name'  => esc_html__( 'Space', 'wpdc' ),
                'slug'  => 'space',
                'color' => '#000B1d',
            ],
            [
                'name'  => esc_html__( 'Navy', 'wpdc' ),
                'slug'  => 'navy',
                'color' => '#00498d',
            ],
            [
                'name'  => esc_html__( 'Blue', 'wpdc' ),
                'slug'  => 'blue',
                'color' => '#3169a6',
            ],
            [
                'name'  => esc_html__( 'Sky', 'wpdc' ),
                'slug'  => 'sky',
                'color' => '#e1e7f3',
            ],
        ]
    );
}
add_action( 'after_setup_theme', 'wpdc_add_custom_gutenberg_color_palette' );

/**
 * Register extra styles for blocks
 */
register_block_style(
    'core/paragraph',
    array(
        'name'         => 'space-above',
        'label'        => 'Space Above',
        'style_handle' => 'lucera-styles',
    )
);

/*
 * Adds custom formats to TinyMCE
 */

// Callback function to insert 'styleselect' into the $buttons array
function my_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
// Register our callback to the appropriate filter
add_filter('mce_buttons_2', 'my_mce_buttons_2');

// Callback function to filter the MCE settings
function my_mce_before_init_insert_formats( $init_array ) {
    // Define the style_formats array
    $style_formats = array(
        // Each array child is a format with it's own settings
        array(
            'title' => 'Red Underline',
            'inline' => 'span',
            'classes' => 'underline-red-10',
            'wrapper' => true,

        ),
        array(
            'title' => 'Red Text',
            'inline' => 'span',
            'classes' => 'text-red-alt',
            'wrapper' => true,
        ),
    );
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = wp_json_encode( $style_formats );

    return $init_array;
}

// Attach callback to 'tiny_mce_before_init'
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );


// Lighthouse reports a "does not use passive listeners" performance issue on comment replies.
//    (For future reference: https://dtlytics.com/2020/11/does-not-use-passive-listeners-solved/ )
// We don't need comments on this site, so let's deregister the script and remove comments support entirely.
function wp_dereg_script_comment_reply(){ wp_deregister_script( 'comment-reply' ); }
add_action('init','wp_dereg_script_comment_reply');

add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});
