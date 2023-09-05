<?php
function register_team_post_type() {
    $singular = 'Team Member';
    $plural = $singular . 's';

    $labels = [
        'name'                  => $plural,
        'singular_name'         => $singular,
        'archives'              => '' . $singular . ' Archives',
        'attributes'            => '' . $singular . ' Attributes',
        'all_items'             => 'All ' . $plural . '',
        'add_new_item'          => 'Add New ' . $plural . '',
        'add_new'               => 'Add New',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into ' . $singular . '',
        'uploaded_to_this_item' => 'Uploaded to this ' . $singular . '',
        'items_list'            => '' . $plural . ' list',
        'items_list_navigation' => '' . $plural . ' list navigation',
        'filter_items_list'     => 'Filter ' . $singular . ' list',
    ];
    $args = [
        'label'                 => $plural,
        'labels'                => $labels,
        'supports'              => [
            'title',
            'custom-fields',
            'revisions',
        ],
        'taxonomies'            => [],
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_in_rest'          => true,
        'menu_icon'             => 'dashicons-groups',
    ];
    register_post_type('team', $args);
}

add_action('init', 'register_team_post_type', 0);
