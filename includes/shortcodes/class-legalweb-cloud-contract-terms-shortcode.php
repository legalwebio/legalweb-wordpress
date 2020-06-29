<?php

function LegalWebCloudContractTermsShortcode($atts){

    $locale = LegalWebCloudLanguageTools::getInstance()->getCurrentLanguageCode();
	$locale = substr( $locale, 0, 2 );
/*
    $params = shortcode_atts(array(
        'lang' => $locale
    ), $atts);

    $locale = $params['lang'];
*/

   $apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();

   try {
	   if ( $apiData == null ||
	        isset($apiData->services) == false ||
	        isset($apiData->services->contractterms) == false ||
	        isset($apiData->services->contractterms->{$locale}) == false ) {
		   return __( 'The contract terms for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
	   }

	   return apply_filters( 'the_content', $apiData->services->contractterms->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The contract terms for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
   }
}

add_shortcode('legalweb-contractterms', 'LegalWebCloudContractTermsShortcode');
