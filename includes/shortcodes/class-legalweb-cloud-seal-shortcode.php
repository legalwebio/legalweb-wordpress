<?php

function LegalWebCloudSealShortcode($atts){

	$locale = LegalWebCloudLanguageTools::getInstance()->getCurrentLanguageCode();
	$locale = substr( $locale, 0, 2 );
	/*
    $params = shortcode_atts(array(
        'style' => '',
        'class' => '',
    ), $atts);

    $locale = $params['lang'];
*/

   $apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();
   $customContainerClass = LegalWebCloudSettings::get('seal-container-css');
   $customContainerStyle = LegalWebCloudSettings::get('seal-container-style');
   $customImgStyle = LegalWebCloudSettings::get('seal-img-style');

   try {
	   if ( $apiData == null ||
	        isset($apiData->services) == false ||
	        isset($apiData->services->guetesiegel) == false ||
	        isset($apiData->services->guetesiegel->{$locale}) == false ) {
		   return __( 'The guetesiegel for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
	   }

	   $sealHtml = $apiData->services->guetesiegel->{$locale};

	   $containerCustomization = 'id="legalweb-seal-container"';
	   if (empty($customContainerClass) == false)
	   {
		   $containerCustomization .=' class="'.$customContainerClass.'"';
	   }
	   if (empty($customContainerStyle) == false)
	   {
		   $containerCustomization .=' style="'.$customContainerStyle.'"';
	   }
	   if (empty($containerCustomization) == false) {
		   $sealHtml = str_replace('id="legalweb-seal-container"', $containerCustomization, $sealHtml);
	   }

	   $imgCustomization = 'id="legalweb-seal-img"';
	   if (empty($customImgStyle) == false)
	   {
		   $imgCustomization .=' style="'.$customImgStyle.'"';
	   }

	   if (empty($imgCustomization) == false) {
		   $sealHtml = str_replace('id="legalweb-seal-img"',$imgCustomization, $sealHtml);
	   }

	   return apply_filters( 'the_content', $sealHtml );
   } catch (Exception $e)
   {
	   return __( 'The guetesiegel for the selected language ' . $locale . ' could not be found.', 'legalweb-cloud' );
   }
}

add_shortcode('legalweb-seal', 'LegalWebCloudSealShortcode');
