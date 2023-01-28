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
	    /*
		wp_enqueue_script(legalweb_cloud_NAME, plugin_dir_url(__FILE__) . 'js/legalweb-cloud-public.js', array(
			'jquery'
		), legalweb_cloud_VERSION, false);
		*/
    }

	public function writeHeaderScripts()
    {
	    if ( LegalWebCloudSettings::get( 'popup_enabled' ) != '1') return;
	    $user = wp_get_current_user();
	    $allowed_roles = array( 'editor', 'administrator', 'author', 'contributor' );
	   // if (is_admin() || current_user_can( 'administrator' )) return;
	    if (is_admin() || ($user != null && array_intersect( $allowed_roles, $user->roles ))) return;
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
		if (is_admin() || current_user_can( 'administrator' )) return;

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
}