<?php

class LwApiAction extends LwWordpressAjaxAction {

	protected $action = 'admin-api-action';

	protected function run() {
		$this->requireAdmin();

		$licenceKey = LwWordpressSettings::get( 'license_number' );

		if ( empty( $licenceKey ) == false ) {
			$licenceKey = trim( $licenceKey );
		}


		$siteUrl = get_site_url();
		$homeUrl = get_home_url();


		$url = LwWordpressConstants::LW_API_BASE_URL;

		$headers = array(
			'content-type' => 'application/x-www-form-urlencoded',
			'Callback'     => $homeUrl,
			'Guid'         => $licenceKey
		);
		$options = array();
		$request = Requests::get( $url, $headers, $options );

		var_dump( $request->body );


		$this->returnBack();
	}
}

LwApiAction::listen();
