<?php


class LegalWebCloudPublic
{

	public static function startBuffer()
	{
		ob_start();
	}

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
        // at the momoment we dont need a css
        //wp_enqueue_style(legalweb_cloud_NAME, plugin_dir_url(__FILE__) . 'css/legalweb-cloud-public.css', array(), legalweb_cloud_VERSION, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
	    // at the momoment we dont need a js
	    if ($this->lwCheckIfWpConsentApiIsActive()) {
		    wp_enqueue_script(legalweb_cloud_NAME, plugin_dir_url(__FILE__) . 'js/legalweb-cloud-public.min.js', array(
			    'jquery'
		    ), legalweb_cloud_VERSION, false);
        }
    }

	public function writeHeaderScripts()
    {
	    if ( LegalWebCloudSettings::get( 'popup_enabled' ) != '1') return;
	    if ( LegalWebCloudSettings::get( 'popup_enabled_for_admin' ) != '1' && legalweb_disable_on_backend()) return;
	    if(apply_filters('legalweb_disable_header_scripts', false)) return;

	    $apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();

        // write popup scripts
		if ( $apiData != null &&
		     isset($apiData->services) &&
		     isset($apiData->services->dppopupjs) &&
		     isset($apiData->services->dppopupconfig->spDsgvoGeneralConfig) &&
		     isset($apiData->services->dppopupconfig->spDsgvoIntegrationConfig)) {
			?>
            <script>
                var spDsgvoGeneralConfig = JSON.parse('<?php echo json_encode( $apiData->services->dppopupconfig->spDsgvoGeneralConfig ); ?>');
                var spDsgvoIntegrationConfig = JSON.parse('<?php echo json_encode( $apiData->services->dppopupconfig->spDsgvoIntegrationConfig ); ?>');
				<?= $apiData->services->dppopupjs; ?>
            </script>
			<?php
		}
	}

	public function writeBodyStartScripts()
	{


	}

	public function writeFooterScripts() {

        if ( LegalWebCloudSettings::get( 'popup_enabled' ) != '1') return;
		if ( LegalWebCloudSettings::get( 'popup_enabled_for_admin' ) != '1' && legalweb_disable_on_backend()) return;
		if(apply_filters('legalweb_disable_footer_scripts', false)) return;



		$locale = LegalWebCloudLanguageTools::getInstance()->getCurrentLanguageCode();
		$locale = substr( $locale, 0, 2 );
		$apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();

		try {

		    echo '<!--noptimize-->';

			// write popup styles
			if ( $apiData != null &&
			     isset($apiData->services) &&
			     isset($apiData->services->dppopupcss)) {

				echo '<style>';
				echo $apiData->services->dppopupcss;
				echo '</style>';
			}

			// write popup html
			if ( $apiData != null &&
			     isset($apiData->services) &&
			     isset($apiData->services->dppopup) &&
			     isset($apiData->services->dppopup->{$locale})) {

				echo $apiData->services->dppopup->{$locale};
			}



			echo '<!--/noptimize-->';


		} catch (Exception $e)
		{
			return __( 'The imprint for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
		}
	}

	public function registerLwCallbackEndpoint()
    {
	    register_rest_route( legalweb_cloud_NAME.'/v1', '/callback', array(
		    'methods' => 'GET',
		    'callback' =>  array($this, 'lwWordpressCallbackUrlAction'),
			'permission_callback' => '__return_true'
	    ) );
    }

	public function lwWordpressCallbackUrlAction( $request ) {

		//$guid = $request['guid'];

        try {
	        ( new LegalWebCloudApiAction() )->refreshApiData();
	        $response = new WP_REST_Response( array(
		        'status'  => 'OK',
		        'guid'    => LegalWebCloudSettings::get( 'license_number' ),
		        'version' => LegalWebCloudSettings::get( 'api_data_version' )
	        ) );
	        $response->set_status( 200 );
        } catch (Exception $e)
        {
	        $response = new WP_REST_Response( array(
		        'status'  => 'Error',
		        'error'    => $e
	        ) );
	        $response->set_status( 500 );
        }

		return $response;
    }

	//set the consent type (optin, optout, default false)
	function lwSetConsenttype($consenttype){
		return 'optin';
	}

	function lwCheckIfWpConsentApiIsActive() {
		return function_exists( 'wp_has_consent' );
	}

	//filter consent categories types, example: remove the preferences category

	function lwSetWpConsentCategories($consentcategories){
		unset($consentcategories['preferences']);
		return $consentcategories;
	}
}