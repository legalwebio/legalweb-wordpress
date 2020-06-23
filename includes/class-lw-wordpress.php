<?php


class LwWordpress
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Singleton
     *
     * @since    1.0.0
     * @access   protected
     * @var      object    $instance    The singleton instance
     */
    protected static $instance = null;

    protected function __construct(){
        $this->version = lw_wordpress_VERSION;
        $this->loadDependencies();

        if (is_admin()) {
            $this->defineAdminHooks();
        } else {
            $this->definePublicHooks();
        }
    }

    protected function __clone(){}

    public static function instance(){
        if(!isset(static::$instance)){
            static::$instance = new static;
        }

        return static::$instance;
    }

    public static function version(){
        return (new self)->version;
    }

    private function loadDependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lw-wordpress-loader.php';
        $this->loader = new LwWordpressLoader();

        $load = array(
            //======================================================================
            // Libraries
            //======================================================================


            LwWordpress::pluginDir('includes/class-lw-wordpress-constants.php'),
	        LwWordpress::pluginDir('includes/helpers.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-installer.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-database-api.php'),
	        LwWordpress::pluginDir('admin/class-lw-wordpress-admin.php'),
	        LwWordpress::pluginDir('admin/class-lw-wordpress-admin-tab.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-ajax-action.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-settings.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-create-page-action.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-api-action.php'),
	        LwWordpress::pluginDir('includes/class-lw-wordpress-cron.php'),
	        LwWordpress::pluginDir('includes/cron/class-lw-wordpress-api-cron.php'),

	        LwWordpress::pluginDir('includes/shortcodes/class-lw-wordpress-imprint-shortcode.php'),
	        LwWordpress::pluginDir('includes/shortcodes/class-lw-wordpress-privacy-policy-shortcode.php'),

	        LwWordpress::pluginDir('public/class-lw-wordpress-public.php'),

	        //======================================================================
	        // Admin Pages
	        //======================================================================

	        // Common Settings
	        LwWordpress::pluginDir('admin/tabs/common-settings/class-lw-wordpress-common-settings-tab.php'),
	        LwWordpress::pluginDir('admin/tabs/common-settings/class-lw-wordpress-common-settings-action.php'),


	        // SHORTCODES
	       // LwWordpress::pluginDir('public/shortcodes/shortcode-user-wordpress-mgmt.php'),
        );

        foreach($load as $path){
            require_once $path;
        }

        do_action('lw_wordpress_booted');
    }

    private function defineAdminHooks()
    {
        $admin = new LwWordpressAdmin();
        $this->loader->add_action('init', $admin, 'adminInit', 10);
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $admin, 'menuItem');
	    $this->loader->add_action('admin_notices', $admin, 'showAdminNotices');

	    $this->loader->add_action('current_screen', $admin, 'doSystemCheck');

    }

    private function definePublicHooks()
    {
        $public = new LwWordpressPublic();
        $this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles');
        $this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts');

	    $this->loader->add_action('wp_footer', $public, 'writeFooterScripts', 1000);
	    $this->loader->add_action('wp_head', $public, 'writeHeaderScripts');
	    $this->loader->add_action('wp_body_open', $public, 'writeBodyStartScripts');

	    $this->loader->add_action( 'rest_api_init',$public , 'registerLwCallbackEndpoint');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run(){
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_lw_wordpress() {
        return lw_aff_NAME;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return   Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    public static function pluginDir($append = ''){
        return plugin_dir_path(dirname(__FILE__)) . $append;
    }

    public static function pluginURI($append = ''){
        return plugin_dir_url(dirname(__FILE__)) . $append;
    }
}