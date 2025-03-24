<?php


class LegalWebCloud
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
        $this->version = legalweb_cloud_VERSION;
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
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-legalweb-cloud-loader.php';
        $this->loader = new LegalWebCloudLoader();

        $load = array(
            //======================================================================
            // Libraries
            //======================================================================


            LegalWebCloud::pluginDir('includes/class-legalweb-cloud-constants.php'),
	        LegalWebCloud::pluginDir('includes/helpers.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-installer.php'),
	        LegalWebCloud::pluginDir('admin/class-legalweb-cloud-admin.php'),
	        LegalWebCloud::pluginDir('admin/class-legalweb-cloud-admin-tab.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-ajax-action.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-settings.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-create-page-action.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-api-action.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-notice-action.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-cron.php'),
	        LegalWebCloud::pluginDir('includes/class-legalweb-cloud-embeddings-manager.php'),
	        LegalWebCloud::pluginDir('includes/cron/class-legalweb-cloud-api-cron.php'),

	        // SHORTCODES
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-imprint-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-privacy-policy-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-cookie-popup-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-contract-withdrawal-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-contract-withdrawal-service-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-contract-withdrawal-digital-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-contract-terms-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-seal-shortcode.php'),
	        LegalWebCloud::pluginDir('includes/shortcodes/class-legalweb-cloud-content-block-shortcode.php'),

	        LegalWebCloud::pluginDir('public/class-legalweb-cloud-public.php'),

	        //======================================================================
	        // Admin Pages
	        //======================================================================

	        // Common Settings
	        LegalWebCloud::pluginDir('admin/tabs/common-settings/class-legalweb-cloud-common-settings-tab.php'),
	        LegalWebCloud::pluginDir('admin/tabs/common-settings/class-legalweb-cloud-common-settings-action.php'),

        );

        foreach($load as $path){
            require_once $path;
        }

        do_action('legalweb_cloud_booted');
    }

    private function defineAdminHooks()
    {
        $admin = new LegalWebCloudAdmin();
        $this->loader->add_action('init', $admin, 'adminInit', 10);
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $admin, 'menuItem');
	    $this->loader->add_action('admin_notices', $admin, 'showAdminNotices');

	    $this->loader->add_action('current_screen', $admin, 'doSystemCheck');

    }

    private function definePublicHooks()
    {
        $public = new LegalWebCloudPublic();
        $this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles');
        $this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts');

	    $this->loader->add_action('wp_footer', $public, 'writeFooterScripts', 1000);
	    $this->loader->add_action('wp_head', $public, 'writeHeaderScripts');
	    $this->loader->add_action('wp_body_open', $public, 'writeBodyStartScripts');

	    $this->loader->add_action( 'rest_api_init',$public , 'registerLwCallbackEndpoint');

	    $this->loader->add_filter('the_content', LegalWebCloudEmbeddingsManager::getInstance(), 'findAndProcessIframes', 50, 1);
	    $this->loader->add_filter('widget_text_content', LegalWebCloudEmbeddingsManager::getInstance(), 'findAndProcessIframes', 50, 1);
	    $this->loader->add_filter('widget_custom_html_content', LegalWebCloudEmbeddingsManager::getInstance(), 'findAndProcessIframes', 50, 1);
	    $this->loader->add_filter('embed_oembed_html', LegalWebCloudEmbeddingsManager::getInstance(), 'findAndProcessOembeds', 50, 2);

		// consent mode api
	    $this->loader->add_filter( 'wp_get_consent_type', $public, 'lwSetConsenttype' , 10, 1 );
	    $this->loader->add_filter( 'wp_consent_categories', $public,'lwSetWpConsentCategories' , 10, 1 );
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
    public function get_legalweb_cloud() {
        return legalweb_cloud_NAME;
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