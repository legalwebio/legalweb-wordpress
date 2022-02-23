<?php


class LegalWebCloudAdmin
{
    public $tabs = array();
	public $userTabs = array();

    /**
     * Initialize the class and set its properties
     */
    public function __construct(){
        $this->tabs = array_merge(array(
            'common-settings' 			=> new LegalWebCloudCommonSettingsTab
        ));
    }

    public function menuItem()
    {
        global $submenu;

        $user = wp_get_current_user();
        $allowed_roles = array('administrator');
	    $menu_slug = 'legalweb-cloud';

        if( array_intersect($allowed_roles, $user->roles ) || is_super_admin() ) {


            $svg = 'data:image/svg+xml;base64,'. base64_encode(file_get_contents(LegalWebCloud::pluginDir('public/images/legalwebio-logo-icon-white.svg')));
            add_menu_page('LegalWeb Cloud', 'LegalWeb Cloud',  'manage_options', 'legalweb-cloud', array($this, 'adminPage'), $svg, null);

            add_submenu_page($menu_slug, __('Common','legalweb-cloud'), __('Common','legalweb-cloud'),  'manage_options', 'legalweb-cloud', array($this, 'adminPage'));


            $first = true;
            foreach($this->tabs as $t):
                if ($first === true) {
                    $first = false;
                    continue;
                }
                if(!$t->isHidden()):
                    add_submenu_page($menu_slug, __($t->title,'legalweb-cloud'), __($t->title,'legalweb-cloud'), 'manage_options', 'admin.php?page=legalweb-cloud&tab='.$t->slug);

                endif;
            endforeach;

	        foreach($this->userTabs as $t) {
		        //add_users_page($t->title, $t->title, 'read', 'lw-user-wordpress-settings', array($this, 'userSettingsPage'));
		        if(!$t->isHidden()) {
			        add_submenu_page( $menu_slug, $t->title, $t->title, 'read', 'admin.php?page=legalweb-cloud&tab=' . $t->slug );
		        }
	        }

            $index = 6 + count($this->tabs) + count($this->userTabs);


            $submenu[$menu_slug][$index++] = array(__('About legal web','legalweb-cloud'), 'manage_options', 'https://legalweb.io');
        }



    }

    public function adminPage(){
        $tabs = array_merge($this->tabs, $this->userTabs);

        if(isset($_GET['tab'])){
            $tab = sanitize_text_field($_GET['tab']);
        }else{
            $tab = 'common-settings';
        }

        include LegalWebCloud::pluginDir('admin/base.php');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(){
        wp_enqueue_style(legalweb_cloud_NAME.'-bootstrap', plugin_dir_url(__FILE__). 'css/bootstrap.min.css', array(), legalweb_cloud_VERSION, 'all' );
        wp_enqueue_style(legalweb_cloud_NAME, plugin_dir_url(__FILE__). 'css/legalweb-cloud-admin.css', array(), legalweb_cloud_VERSION, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts(){

        wp_enqueue_script(legalweb_cloud_NAME, plugin_dir_url(__FILE__). 'js/legalweb-cloud-admin.js', array('jquery'), legalweb_cloud_VERSION, false );
        wp_enqueue_script(legalweb_cloud_NAME.'-bootstrap', plugin_dir_url(__FILE__). 'js/bootstrap.min.js', array('jquery'), legalweb_cloud_VERSION, false );

	    $generalConfig = [
		    'ajaxUrl' => admin_url('admin-ajax.php')
	    ];

	    wp_localize_script(legalweb_cloud_NAME, 'args', $generalConfig);
    }

    public function doSystemCheck()
    {
	    $statusSystemCheck = [];
	    /*
	    $statusSystemCheck[] = LegalWebCloudDatabaseApi::getInstance()->checkTableCommissionLog();
	    $statusSystemCheck[] = LegalWebCloudDatabaseApi::getInstance()->checkTableVisitLog();
	    if (LegalWebCloudSettings::get('migrate_sumo_on_start') == '1')
	    {
		    $statusSystemCheck[] = LegalWebCloudDatabaseApi::getInstance()->migrateSumoUsers();

	    }
	    */
    }

	function showAdminNotices() {

		$locale = LegalWebCloudLanguageTools::getInstance()->getCurrentLanguageCode();
		$locale = substr( $locale, 0, 2 );

		$apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();

		try {
			if ( $apiData != null &&
			     isset($apiData->messages) &&
			     count($apiData->messages) > 0) {

				$dismissedApiMessages = LegalWebCloudSettings::get('dismissed_api_message_ids');
				if (is_array($dismissedApiMessages) == false) $dismissedApiMessages = [];

				//$allMessages = json_decode(json_encode( $apiData->services->$apiData->notices->messages), true);
				foreach ($apiData->messages as $key => $messageItem) {

					if (in_array($messageItem->id, $dismissedApiMessages) == false)
					{
						$class = 'notice notice-warning is-dismissible legalweb-cloud-admin-message legalweb-cloud-admin-message'.$messageItem->id;
						$message = $messageItem->msg;

						//printf( '<div class="%1$s" data-msgId="'.$messageItem->id.'"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
					}
				}

			}
		} catch (Exception  $e)
		{

		}
	}

}