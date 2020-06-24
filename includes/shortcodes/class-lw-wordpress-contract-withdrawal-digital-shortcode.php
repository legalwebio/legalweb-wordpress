<?php

function LwWordpressContractWithdrawalDigitalShortcode($atts){

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
	        isset($apiData->services->contractwithdrawaldigital) == false ||
	        isset($apiData->services->contractwithdrawaldigital->{$locale}) == false ) {
		   return __( 'The contract withdrawal digital for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
	   }

	   return apply_filters( 'the_content', $apiData->services->contractwithdrawaldigital->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The contract withdrawal digital for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
   }
}

add_shortcode('lw-contractwithdrawaldigital', 'LwWordpressContractWithdrawalDigitalShortcode');
