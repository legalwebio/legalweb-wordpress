<?php

function LwWordpressContractTermsShortcode($atts){

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
	        isset($apiData->services->contractterms) == false ||
	        isset($apiData->services->contractterms->{$locale}) == false ) {
		   return __( 'The contract terms for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
	   }

	   return apply_filters( 'the_content', $apiData->services->contractterms->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The contract terms for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
   }
}

add_shortcode('lw-contractterms', 'LwWordpressContractTermsShortcode');
