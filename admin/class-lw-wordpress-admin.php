<?php


class LwWordpressAdmin
{
    public $tabs = array();
	public $userTabs = array();

    /**
     * Initialize the class and set its properties
     */
    public function __construct(){
        $this->tabs = array_merge(array(
            'common-settings' 			=> new LwWordpressCommonSettingsTab
        ));
    }

    public function menuItem()
    {
        global $submenu;

        $user = wp_get_current_user();
        $allowed_roles = array('administrator');
	    $menu_slug = 'lw-wordpress';

        if( array_intersect($allowed_roles, $user->roles ) || is_super_admin() ) {


            $svg = 'data:image/svg+xml;base64,'. base64_encode(file_get_contents(LwWordpress::pluginDir('public/images/legalwebio-logo-icon-white.svg')));
            add_menu_page('LegalWeb Wordpress', 'LegalWeb Wordpress',  'manage_options', 'lw-wordpress', array($this, 'adminPage'), $svg, null);

            add_submenu_page($menu_slug, __('Common','lw-wordpress'), __('Common','lw-wordpress'),  'manage_options', 'lw-wordpress', array($this, 'adminPage'));


            $first = true;
            foreach($this->tabs as $t):
                if ($first === true) {
                    $first = false;
                    continue;
                }
                if(!$t->isHidden()):
                    add_submenu_page($menu_slug, __($t->title,'lw-wordpress'), __($t->title,'lw-wordpress'), 'manage_options', 'admin.php?page=lw-wordpress&tab='.$t->slug);

                endif;
            endforeach;

	        foreach($this->userTabs as $t) {
		        //add_users_page($t->title, $t->title, 'read', 'lw-user-wordpress-settings', array($this, 'userSettingsPage'));
		        if(!$t->isHidden()) {
			        add_submenu_page( $menu_slug, $t->title, $t->title, 'read', 'admin.php?page=lw-wordpress&tab=' . $t->slug );
		        }
	        }

            $index = 6 + count($this->tabs) + count($this->userTabs);


            $submenu[$menu_slug][$index++] = array(__('About legal web','lw-wordpress'), 'manage_options', 'https://legalweb.io');
        }



    }

    public function adminPage(){
        $tabs = array_merge($this->tabs, $this->userTabs);

        if(isset($_GET['tab'])){
            $tab = sanitize_text_field($_GET['tab']);
        }else{
            $tab = 'common-settings';
        }

        include LwWordpress::pluginDir('admin/base.php');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(){
        wp_enqueue_style(lw_wordpress_NAME.'-bootstrap', plugin_dir_url(__FILE__). 'css/bootstrap.min.css', array(), lw_wordpress_VERSION, 'all' );
        wp_enqueue_style(lw_wordpress_NAME, plugin_dir_url(__FILE__). 'css/lw-wordpress-admin.css', array(), lw_wordpress_VERSION, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts(){

        wp_enqueue_script(lw_wordpress_NAME, plugin_dir_url(__FILE__). 'js/lw-wordpress-admin.js', array('jquery'), lw_wordpress_VERSION, false );
        wp_enqueue_script(lw_wordpress_NAME.'-bootstrap', plugin_dir_url(__FILE__). 'js/bootstrap.min.js', array('jquery'), lw_wordpress_VERSION, false );

    }

    public function doSystemCheck()
    {
	    $statusSystemCheck = [];
	    /*
	    $statusSystemCheck[] = LwWordpressDatabaseApi::getInstance()->checkTableCommissionLog();
	    $statusSystemCheck[] = LwWordpressDatabaseApi::getInstance()->checkTableVisitLog();
	    if (LwWordpressSettings::get('migrate_sumo_on_start') == '1')
	    {
		    $statusSystemCheck[] = LwWordpressDatabaseApi::getInstance()->migrateSumoUsers();

	    }
	    */
    }

	function showAdminNotices() {

		$locale = LwWordpressLanguageTools::getInstance()->getCurrentLanguageCode();
		$locale = substr( $locale, 0, 2 );

		$apiData = (new LwWordpressApiAction())->getOrLoadApiData();

		try {
			if ( $apiData != null &&
			     $apiData->notices != null &&
			     $apiData->notices->notice != null &&
			     $apiData->notices->notice->{$locale} != null ) {



			}
		} catch (Exception  $e)
		{

		}
	}

}