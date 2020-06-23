<?php


class LwWordpressPublic
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
        wp_enqueue_style(lw_wordpress_NAME.'_twbs4', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), lw_wordpress_VERSION, 'all');
        wp_enqueue_style(lw_wordpress_NAME, plugin_dir_url(__FILE__) . 'css/lw-wordpress-public.css', array(), lw_wordpress_VERSION, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(lw_wordpress_NAME, plugin_dir_url(__FILE__) . 'js/lw-wordpress-public.js', array(
            'jquery'
        ), lw_wordpress_VERSION, false);
	    wp_enqueue_script(lw_wordpress_NAME.'-bootstrap', plugin_dir_url(__FILE__). 'js/bootstrap.min.js', array('jquery'), lw_wordpress_VERSION, false );
    }

	public function writeHeaderScripts()
	{


	}

	public function writeBodyStartScripts()
	{


	}

	public function writeFooterScripts() {
		$locale = LwWordpressLanguageTools::getInstance()->getCurrentLanguageCode();
		$locale = substr( $locale, 0, 2 );
		$apiData = (new LwWordpressApiAction())->getOrLoadApiData();

		try {

			// write popup styles
			if ( $apiData != null &&
			     $apiData->services != null &&
				 $apiData->services->dppopupcss != null) {

				echo '<style>';
				echo $apiData->services->dppopupcss;
				echo '</style>';
			}

			// write popup html
			if ( $apiData != null &&
			     $apiData->services != null &&
			     $apiData->services->dppopup != null &&
			     $apiData->services->dppopup->{$locale} != null) {

				echo $apiData->services->dppopup->{$locale};
			}

			// write popup scripts
			if ( $apiData != null &&
			     $apiData->services != null &&
				 $apiData->services->dppopupjs != null &&
			     $apiData->services->dppopupconfig->spDsgvoGeneralConfig != null &&
			     $apiData->services->dppopupconfig->spDsgvoIntegrationConfig) {
		?>
			<script>
			    var spDsgvoGeneralConfig = JSON.parse('<?php echo json_encode($apiData->services->dppopupconfig->spDsgvoGeneralConfig); ?>');
			    var spDsgvoIntegrationConfig = JSON.parse('<?php echo json_encode($apiData->services->dppopupconfig->spDsgvoIntegrationConfig); ?>');
                <?= $apiData->services->dppopupjs; ?>
             </script>;
		<?php
            }



		} catch (Exception $e)
		{
			return __( 'The imprint for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
		}
	}

	public function registerLwCallbackEndpoint()
    {
	    register_rest_route( lw_wordpress_NAME.'/v1', '/callback', array(
		    'methods' => 'GET',
		    'callback' =>  array($this, 'lwWordpressCallbackUrlAction'),
	    ) );
    }

	public function lwWordpressCallbackUrlAction( $request ) {

		//$guid = $request['guid'];

        try {
	        ( new LwWordpressApiAction() )->refreshApiData();
	        $response = new WP_REST_Response( array(
		        'status'  => 'OK',
		        'guid'    => LwWordpressSettings::get( 'license_number' ),
		        'version' => LwWordpressSettings::get( 'api_data_version' )
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