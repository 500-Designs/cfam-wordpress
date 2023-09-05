<?php
if (version_compare('7.3', phpversion(), '>=')) {
    die('You must be using PHP 7.3 or greater.');
}

if (version_compare($GLOBALS['wp_version'], '5.0.0', '<')) {
    die('WP theme only works in WordPress 5.0.0 or later');
}

if (!class_exists('Timber')) {
    add_action(
        'admin_notices',
        function () {
            echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
        }
    );
    return;
}

class Lucera extends Timber\Site {
    public $config;
    public $templates = [];
    public $custom_post_types = [];
    public $post_types = [];
    public $modular_post_types = [];
    public $registered_blocks = [];

    public function __construct() {
        $this->config = include __DIR__ . '/config.php';
        Timber::$dirname = $this->config['twig_directories'];
        $this->load_dependencies();
        $this->setup();
        $this->register_post_types();
        $this->register_taxonomies();
        $this->set_templates();
        add_filter('timber_context', [$this, 'add_to_context']);
        add_filter('allowed_block_types_all', [$this, 'lucera_allowed_block_types_all']);
        add_filter('timber_context', [$this, 'options_timber_context']);
        add_filter('render_block', [$this, 'wrap_blocks'], 10, 2);
    }

    public function setup() {
        $import = [
            'setup',
            'admin',
            'helper',
        ];

        $this->autoloader(false, $import);
    }

    // To use any options fields site wide

    public function options_timber_context($context) {
        $context['options'] = get_fields('option');
        return $context;
    }

    /**
     * Easily include files into the project by looping through
     * an array of file names
     *
     * @param string $path Path starting from the root of the theme
     * @param array $files List of files to include without the extension
     * @return void
     */
    private function autoloader($path = '', $files) {
        foreach ($files as $file) {
            $file = '/app/' . $path . '/' . $file . '.php';
            require get_parent_theme_file_path($file);
        }
    }

    /**
     * Register packages to the theme
     *
     */
    public function load_dependencies() {
        $packages = [
            'acf/index',
        ];
        $this->autoloader('plugins', $packages);

        // Initialize
        new Lucera\ACF();
    }

    public function register_post_types() {
        $this->custom_post_types = [
            // 'literature',
            // 'form',
            // 'sec-filing',
            // 'investment'
        ];

        //Non public post types
        $this->modular_post_types = [
            'team',
        ];

        $this->post_types = array_merge(['post'], $this->custom_post_types);

        $this->autoloader('post-types', array_merge($this->custom_post_types, $this->modular_post_types));
    }

    public function register_taxonomies() {
        // Reference: https://developer.wordpress.org/reference/functions/register_taxonomy

        // Helper function to create custom taxonomies
        function create_taxonomy($name, $singular) {
            $plural = $singular . 's';

            $labels = [
                'name'               => $plural,
                'singular_name'      => $singular,
                'menu_name'          => $plural,
                'all_items'          => 'All ' . $plural,
                'add_new'            => 'Add New',
                'add_new_item'       => 'Add New ' . $singular,
                'edit'               => 'Edit',
                'edit_item'          => 'Edit ' . $singular,
                'new_item'           => 'New ' . $singular,
                'view'               => 'View ' . $singular,
                'view_item'          => 'View ' . $singular,
                'search_items'       => 'Search ' . $plural,
                'not_found'          => 'No ' . $plural . ' Found',
                'not_found_in_trash' => 'No ' . $plural . ' Found in Trash',
                'parent'             => 'Parent ' . $singular,
            ];

            $args = array(
                'labels'                => $labels,
                'show_ui'               => true,
                'show_in_rest'          => true,
                'show_admin_column'     => true,
                'show_tagcloud'         => true,
                'query_var'             => true,
                'hierarchical'          => false,
                'public'                => false,
            );
            register_taxonomy($name, 'investment', $args);
        }

        // Actually create the taxonomies
        function custom_taxonomies() {
            create_taxonomy('investmenttype', 'Investment Type');
        }
        add_action('init', 'custom_taxonomies', 0);
    }

    /**
     * Add variables to global context
     *
     * @param array $context
     * @return void
     */
    public function add_to_context($context) {
        $context['app'] = $this;
        $context['WP_DEBUG'] = defined('WP_DEBUG') ? WP_DEBUG : false;

        // Theme options
        $context['lucera_theme'] = get_fields('options');

        script_console_log("get_fields('options') = ");

        if (is_array($context['lucera_theme'])) {
            // Menus
            // Defined inside register_nav_menus in setup.php
            $context['main_menu'] = new TimberMenu('main');
            $context['top_header_menu'] = new TimberMenu('top_header');
            $context['footer1_menu'] = new TimberMenu('footer1');
            $context['footer2_menu'] = new TimberMenu('footer2');
            $context['footer3_menu'] = new TimberMenu('footer3');
            $context['footer4_menu'] = new TimberMenu('footer4');

            // Custom logo
            $context['header_logo'] = wp_get_attachment_image($context['lucera_theme']['header_logo'], 'full', false, array("alt" => "Cantor Fitzgerald"));
            $context['footer_logo'] = wp_get_attachment_image($context['lucera_theme']['footer_logo'], 'full', false, array("alt" => "Cantor Fitzgerald"));
            $context['logo_dark'] = wp_get_attachment_image($context['lucera_theme']['logo_dark'], 'full', false, array("alt" => "Cantor Fitzgerald"));
            $context['logo_light'] = wp_get_attachment_image($context['lucera_theme']['logo_light'], 'full', false, array("alt" => "Cantor Fitzgerald"));

            //Custom Scripts
            $context['ga_tracking_script'] = $context['lucera_theme']['ga_tracking_script'];
        }
        return $context;
    }

    /**
     * Whitelist Blocks
     *
     * Select blocks that should be available for the project.
     * Available Blocks
     * Enable default Gutenberg blocks in register-blocks.php
     * Reference: https://wordpress.stackexchange.com/a/326963
     *
     * @return void
     */
    public function lucera_allowed_block_types_all() {
        $all_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
        $allowed_blocks = [
            'core/columns',
            'core/embeds',
            'core/group',
            'core/heading',
            'core/html',
            'core/image',
            'core/list',
            'core/paragraph',
            'core/quote',
            'core/shortcode',
        ];

        foreach ($all_blocks  as $name => $value) {
            $block_name = explode('/', $name, 2);
            $is_core_block = $block_name[0] == 'core' ? true : false;

            if ($is_core_block && in_array($block_name, $allowed_blocks)) {
                $allowed_blocks[] = $name;
            } elseif (!$is_core_block) {
                $allowed_blocks[] = $name;
            }
        }
        $this->registered_blocks = $allowed_blocks;
        return $this->registered_blocks;
    }

    /**
     * Manually set custom theme templates so we can avoid the default
     * WP setting that looks for templates only 1 level deep.
     *
     * @return void
     */
    public function set_templates() {
        // Keep Alphabetized
        $this->templates = [
            'Homepage'          => 'resources/views/templates/homepage/index.php',
            // 'Contact Us Page'          => 'resources/views/templates/contact-us/index.php',
            // 'Manager Page'          => 'resources/views/templates/manager/index.php',
            // 'Infrastructure Page'          => 'resources/views/templates/infrastructure/index.php',
            // 'Literature Page'          => 'resources/views/templates/literature/index.php',
            // 'Portfolio Page'          => 'resources/views/templates/portfolio/index.php',
            'Performance Page'          => 'resources/views/templates/performance/index.php',
            // 'Sample template'        => 'views/templates/path/to/template/template.php',
        ];
        add_filter('theme_page_templates', [$this, 'load_templates'], 10, 4);
    }

    public function wrap_blocks($block_content, $block) {
        if (strpos($block['blockName'], 'core/') !== false) {
            // Adds classes and attributes for core blocks
            $blockName = str_replace('core/', '', $block['blockName']);

            switch ($blockName) {
                case 'group':
                    $blockClasses = 'core-block';
                    break;
                case 'columns':
                case 'column':
                    $blockClasses = 'core-block container';
                    break;
                default;
                    $blockClasses = 'core-block container rich-text';
                    break;
            }

            $block_content = '<div class="' . $blockClasses . '" data-module="' . $blockName . '">' . $block_content . '</div>';
        }
        return $block_content;
    }

    /**
     * Add Custom Template
     *
     * @param [type] $post_templates
     * @return void
     */
    public function load_templates($post_templates, $wp_theme, $post, $post_type) {
        foreach ($this->templates as $name => $path) {
            $post_templates[$path] = __($name);
        }
        return $post_templates;
    }
}

new Lucera();
