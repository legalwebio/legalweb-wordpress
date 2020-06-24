<?php

function LwWordpressImprintShortcode($atts){

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
	        isset($apiData->services) == false ||
	        isset($apiData->services->imprint) == false ||
	        isset($apiData->services->imprint->{$locale}) == false ) {
		   return __( 'The imprint for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
	   }

	   return apply_filters( 'the_content', $apiData->services->imprint->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The imprint for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
   }
}

add_shortcode('lw-imprint', 'LwWordpressImprintShortcode');
