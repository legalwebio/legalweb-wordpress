<?php

function LegalWebCloudContractWithdrawalShortcode($atts){

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
	        isset($apiData->services->contractwithdrawal) == false ||
	        isset($apiData->services->contractwithdrawal->{$locale}) == false  ) {
		   return __( 'The contract withdrawal for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
	   }

	   return apply_filters( 'the_content', $apiData->services->contractwithdrawal->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The contract withdrawal for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
   }
}

add_shortcode('legalweb-contractwithdrawal', 'LegalWebCloudContractWithdrawalShortcode');
