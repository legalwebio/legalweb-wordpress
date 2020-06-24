<?php

function LwWordpressContractWithdrawalServiceShortcode($atts){

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
	        isset($apiData->services->contractwithdrawalservice) == false ||
	        isset($apiData->services->contractwithdrawalservice->{$locale}) == false ) {
		   return __( 'The contract withdrawal service for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
	   }

	   return apply_filters( 'the_content', $apiData->services->contractwithdrawalservice->{$locale} );
   } catch (Exception $e)
   {
	   return __( 'The contract withdrawal service for the selected language ' . $locale . ' could not be found.', 'lw-wordpress' );
   }
}

add_shortcode('lw-contractwithdrawalservice', 'LwWordpressContractWithdrawalServiceShortcode');
