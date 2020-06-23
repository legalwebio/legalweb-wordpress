<?php

class LwWordpressApiAction extends LwWordpressAjaxAction {

	protected $action = 'admin-api-action';

	protected function run() {
		$this->requireAdmin();

		$this->refreshApiData();

		$this->returnBack();
	}

	public function refreshApiData()
	{
		LwWordpressSettings::set( 'api_data_last_refresh_date', date("D M d, Y G:i"));

		$licenceKey = LwWordpressSettings::get( 'license_number' );

		if ( empty( $licenceKey ) == false ) {
			$licenceKey = trim( $licenceKey );
		}


		$siteUrl = get_site_url();
		$homeUrl = get_home_url();


		$url = LwWordpressConstants::LW_API_BASE_URL;

		$headers = array(
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Callback'     => $homeUrl.'wp-json/'.lw_wordpress_NAME.'/v1/callback',
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
				if (LwWordpressSettings::get('api_data_version') !=  $requestData->lw_api->version ||
				    LwWordpressSettings::get('api_data_guid') !=  $licenceKey
				) // only store if version differs
				{
					LwWordpressSettings::set( 'api_data', $request->body );
					LwWordpressSettings::set( 'api_data_date', date( "D M d, Y G:i" ) );
					LwWordpressSettings::set( 'api_data_version', $requestData->lw_api->version );
					LwWordpressSettings::set( 'api_data_guid', $licenceKey );

					return $requestData;
				} else
				{
					$apiDataString = LwWordpressSettings::get( 'api_data');
					return json_decode($apiDataString);
				}
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
		$apiDataString = LwWordpressSettings::get( 'api_data');
		if (empty($apiDataString)) return $this->$this->refreshApiData();

		// deserialize stored api data and return it if it succeed
		$apiData = json_decode($apiDataString);
		if ($apiData != null) return $apiData;

		// try to refetch if derserialization has failed
		return $this->$this->refreshApiData();

	}
}

LwWordpressApiAction::listen();
