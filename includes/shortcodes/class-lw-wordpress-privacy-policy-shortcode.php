<?php

function LwWordpressPrivacyPolicyShortcode($atts){

	$locale = LwWordpressLanguageTools::getInstance()->getCurrentLanguageCode();
	$locale = substr( $locale, 0, 2 );
	/*
		$params = shortcode_atts(array(
			'lang' => $locale
		), $atts);

		$locale = $params['lang'];
	*/

	$apiData = (new LwWordpressApiAction())->getOrLoadApiData();

	try {
		if ( $apiData == null ||
		     $apiData->services == null ||
		     $apiData->services->dpstatement == null ||
		     $apiData->services->dpstatement->{$locale} == null ) {
			return __( 'The pricacy policy for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
		}

		return apply_filters('the_content', $apiData->services->dpstatement->{$locale});
	} catch (Exception  $e)
	{
		return __( 'The pricacy policy for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
	}
}

add_shortcode('lw-privacypolicy', 'LwWordpressPrivacyPolicyShortcode');
