<?php

namespace Lucera;

class ACF
{
    public function __construct()
    {
        if (!class_exists('ACF') || !class_exists('acf_options_page')) {
            add_action('admin_notices', [$this, 'acf_warning']);
        }

        add_action('acf/init', [$this, 'set_settings']);
        add_filter('acf/init', [$this, 'register_acf_blocks']);
        add_filter('acf/settings/load_json', [$this, 'json_import']);
        add_filter('acf/settings/save_json', [$this, 'json_export']);
        $field_name = 'related_posts';
        add_filter('acf/update_value/name=' . $field_name, [$this, 'bidirectional_acf_update_value'], 10, 3);
        add_filter('acf/load_field/name=gf_form', [$this, 'acf_populate_gf_forms_ids']);
        add_filter('block_categories', [$this, 'register_acf_categories'], 10, 2);
    }

    public function acf_warning()
    {
        echo '<div class="notice notice-error"><p>ACF Pro is required plugin. Please install.</p></div>';
    }

    public function set_settings()
    {
        if (function_exists('acf_set_options_page_title')) {
            acf_set_options_page_title(__('Lucera Theme'));
        }
    }

    /**
     * Register ACF Guttenberg Blocks
     *
     * @link https://timber.github.io/docs/guides/gutenberg/#how-to-use-acf-blocks-with-timber
     * @return void
     */
    public function register_acf_blocks()
    {
        if (!function_exists('acf_register_block')) {
            return;
        }
        $registered_blocks = [
            // Register the name of custom blocks here, like this:
            // 'example',
            'bar-charts',
            'line-charts',
            'hero',
            'hero-archive',
            'hero-with-form',
            'historical-performance',
            'info-with-blue-box',
            'info-with-image',
            'info-with-image-2',
            'content-with-image',
            'multi-progress-chart-list',
            'cta',
            'cta-big',
            'cta-half',
            'contact-info-list',
            'counter',
            'info-list',
            'info-list-table',
            'about-company',
            'post-grid',
            'text-grid',
            'team-grid',
            'section-wrapper',
            'tab-post-list',
            'tax-information',
            'styled-header',
            'composition',
            'diversification',
            'page-break-image',
            'gallery',
            'performance-summary',
            'performance-metrics',
            'performance-share-class',
            'infrastructure',
            'nav'
        ];

        foreach ($registered_blocks as $block) {
            $block = 'resources/views/blocks/' . $block . '/' . $block . '.php';
            include_once locate_template($block);
        }
    }

    function register_acf_categories($categories, $post)
    {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'section',
                    'title' => 'Sections',
                ),
            )
        );
    }

    /**
     * Automatically export ACF field groups on save
     *
     * @param [type] $path
     * @return void
     */
    public function json_export($path)
    {
        // update path
        $path = get_stylesheet_directory() . '/app/plugins/acf/fields';

        // return
        return $path;
    }

    /**
     * Set ACF JSON Directory to automatically import exported json files
     *
     * @param [type] $paths
     * @return void
     */
    public function json_import($paths)
    {
        // remove original path (optional)
        unset($paths[0]);

        // append path
        $paths[] = get_stylesheet_directory() . '/app/plugins/acf/fields';
        // return
        return $paths;
    }

    /**
     * Populate ACF select field options with Gravity Forms forms - automatically creates a new field group. You do not need to create any new ACF fields or block files
     */
    public function acf_populate_gf_forms_ids($field)
    {
        if (class_exists('GFFormsModel')) {
            $choices = [];

            foreach (\GFFormsModel::get_forms() as $form) {
                $choices[$form->id] = $form->title;
            }

            $field['choices'] = $choices;
        }

        return $field;
    }

    public function bidirectional_acf_update_value($value, $post_id, $field)
    {
        // vars
        $field_key = $field['key'];
        $field_name = $field['name'];
        $global_name = 'is_updating_' . $field_name;

        // bail early if this filter was triggered from the update_field() function called within the loop below
        // - this prevents an inifinte loop
        if (!empty($GLOBALS[$global_name])) {
            return $value;
        }

        // set global variable to avoid inifite loop
        // - could also remove_filter() then add_filter() again, but this is simpler
        $GLOBALS[$global_name] = 1;

        // loop over selected posts and add this $post_id
        if (is_array($value)) {
            foreach ($value as $post_id2) {
                // load existing related posts
                $value2 = get_field($field_name, $post_id2, false);

                // allow for selected posts to not contain a value
                if (empty($value2)) {
                    $value2 = [];
                }

                // bail early if the current $post_id is already found in selected post's $value2
                if (in_array($post_id, $value2)) {
                    continue;
                }

                // append the current $post_id to the selected post's 'related_posts' value
                $value2[] = $post_id;

                // update the selected post's value (use field's key for performance)
                update_field($field_key, $value2, $post_id2);
            }
        }

        // find posts which have been removed
        $old_value = get_field($field_name, $post_id, false);

        if (is_array($old_value)) {
            foreach ($old_value as $post_id2) {
                // bail early if this value has not been removed
                if (is_array($value) && in_array($post_id2, $value)) {
                    continue;
                }

                // load existing related posts
                $value2 = get_field($field_name, $post_id2, false);

                // bail early if no value
                if (empty($value2)) {
                    continue;
                }

                // find the position of $post_id within $value2 so we can remove it
                $pos = array_search($post_id, $value2);

                // remove
                unset($value2[$pos]);

                // update the un-selected post's value (use field's key for performance)
                update_field($field_key, $value2, $post_id2);
            }
        }

        // reset global varibale to allow this filter to function as per normal
        $GLOBALS[$global_name] = 0;

        // return
        return $value;
    }
}