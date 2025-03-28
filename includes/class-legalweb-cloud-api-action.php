<?php

class LegalWebCloudApiAction extends LegalWebCloudAjaxAction {

	protected $action = 'admin-api-action';

	protected function run() {
		$this->requireAdmin();

		$this->refreshApiData();

		$this->returnBack();
	}

	public function refreshApiData()
	{
		$tz = 'Europe/Vienna';
		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
		$dt->setTimestamp($timestamp); //adjust the object to correct timestamp
		LegalWebCloudSettings::set( 'api_data_last_refresh_date', $dt->format('D M d, Y G:i') );

		$licenceKey = LegalWebCloudSettings::get( 'license_number' );

		if ( empty( $licenceKey ) == false ) {
			$licenceKey = trim( $licenceKey );
		} else {
			return null;
		}


		$siteUrl = get_site_url();
		$homeUrl = get_home_url();


		$url = LegalWebCloudConstants::LW_API_BASE_URL .'?ts='.(new DateTime())->getTimestamp();

		$headers = array(
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Callback'     => $homeUrl.'/wp-json/'.legalweb_cloud_NAME.'/v1/callback',
			'Guid'         => $licenceKey
		);
		$options = array();
		$request = Requests::get( $url, $headers, $options );

		//var_dump( $request->body );

		if ($request->success == true)
		{
			// try to create json. if success then store
			$requestData = json_decode($request->body);

			if ($requestData != null) {
				/*
				if (LegalWebCloudSettings::get('api_data_version') !=  $requestData->lw_api->version ||
				    LegalWebCloudSettings::get('api_data_guid') !=  $licenceKey
				) // only store if version differs
				{
				*/
					LegalWebCloudSettings::set( 'api_data', $request->body );
					$timestamp = time();
					$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
					$dt->setTimestamp($requestData->services->dppopupconfig->spDsgvoGeneralConfig->cookieVersion); //adjust the object to correct timestamp
					LegalWebCloudSettings::set( 'api_data_date', $dt->format('D M d, Y G:i') );
					LegalWebCloudSettings::set( 'api_data_version', $requestData->lw_api->version );
					LegalWebCloudSettings::set( 'api_data_guid', $licenceKey );

					if (LegalWebCloudSettings::get('dont_inline_resources') == '1') {
						$upload_dir = wp_upload_dir();
						$custom_dir = $upload_dir['basedir'] . '/legalweb-cloud';
						// Falls das Verzeichnis nicht existiert, erstelle es
						if (!file_exists($custom_dir)) {
							wp_mkdir_p($custom_dir);
						}

						$js_path = $custom_dir . '/legalweb-cloud-client.js';
						$css_path =$custom_dir . '/legalweb-cloud-client.css';

						$spDsgvoGeneralConfig = "var spDsgvoGeneralConfig = JSON.parse('". json_encode( $requestData->services->dppopupconfig->spDsgvoGeneralConfig) . "');";
						$spDsgvoIntegrationConfig = "var spDsgvoIntegrationConfig = JSON.parse('". json_encode( $requestData->services->dppopupconfig->spDsgvoIntegrationConfig) . "');";
						file_put_contents($js_path, $spDsgvoGeneralConfig);
						file_put_contents($js_path, $spDsgvoIntegrationConfig, FILE_APPEND);
						file_put_contents($js_path, $requestData->services->dppopupjs, FILE_APPEND);
						file_put_contents($css_path, $requestData->services->dppopupcss);
					}

					return $requestData;
					/*
				} else
				{
					$apiDataString = LegalWebCloudSettings::get( 'api_data');
					return json_decode($apiDataString);
				}
				*/
			} else {
				$jsonError = json_last_error();
				error_log('could not deserialize api data. json_error: '.$jsonError);
			}

		} else
		{
			error_log('could not load api data from legal web api');
		}

		return null; //
	}

	public function getOrLoadApiData()
	{
		// try to load from local cache, if nothing is there refresh
		$apiDataString = LegalWebCloudSettings::get( 'api_data');
		if (empty($apiDataString)) return $this->refreshApiData();

		// deserialize stored api data and return it if it succeed
		$apiData = json_decode($apiDataString);
		if ($apiData != null) return $apiData;

		// try to refetch if derserialization has failed
		return $this->refreshApiData();

	}
}

LegalWebCloudApiAction::listen();
