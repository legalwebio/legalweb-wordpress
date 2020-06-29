<?php

function LegalWebCloudContractWithdrawalDigitalShortcode($atts){

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
	        isset($apiData->services->contractwithdrawaldigital) == false ||
	        isset($apiData->services->contractwithdrawaldigital->{$locale}) == false ) {
		   return __( 'The contract withdrawal digital for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
	   }

	   return apply_filters( 'the_content', $apiData->services->contractwithdrawaldigital->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The contract withdrawal digital for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
   }
}

add_shortcode('legalweb-contractwithdrawaldigital', 'LegalWebCloudContractWithdrawalDigitalShortcode');
