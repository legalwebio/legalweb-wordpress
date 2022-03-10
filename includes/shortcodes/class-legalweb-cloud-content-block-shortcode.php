<?php

function LegalWebCloudContentBlockShortcode($atts, $content){

	$params = shortcode_atts( array (
		'type' => '',
		'shortcode' => ''
	), $atts );

    // check if we have the embedding type/slug/name
	if (empty($params['type'])) return $content;
	$slug = strtolower($params['type']);

	// check if we have to do an shortcode in our shortcode
	$shortcode = $params['shortcode'];
	if (empty($shortcode) == false) $content = do_shortcode("[" . $shortcode ."]");

	$apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();

	// write popup scripts
	if ( $apiData != null &&
	     isset($apiData->services) &&
	     isset($apiData->services->dppopupjs) &&
	     isset($apiData->services->dppopupconfig->spDsgvoGeneralConfig) &&
	     isset($apiData->services->dppopupconfig->spDsgvoIntegrationConfig)) {

		$allConfiguredEmbeddings = [];
		$found = false;
		foreach ($apiData->services->dppopupconfig->spDsgvoIntegrationConfig as $integration) {

			if ($integration->category != LegalWebCloudConstants::CATEGORY_SLUG_EMBEDDINGS) continue;
			$allConfiguredEmbeddings[$integration->slug] = $integration;
			if ($integration->slug == $slug) $found = true;
		}

		if ($found == false) return $content;

		// get the embedding config from our config tree
		$integrationToBlock = $allConfiguredEmbeddings[$slug];
		if ($integrationToBlock == null) return $content;

		// if its allowed by cookie nothing is to do here. otherwise replace iframes, show image, add optin handler
		if (LegalWebCloudEmbeddingsManager::getInstance()->checkIfIntegrationIsAllowed($slug) == true) return $content;

		$originalContentBase64Encoded = base64_encode(($content)); //htmlentities
		$lang = LegalWebCloudLanguageTools::getInstance()->getCurrentLanguageCode();
		$lang = substr( $lang, 0, 2 );
		$placeholderHtml = base64_decode($integrationToBlock->placeholders->{$lang});
		$placeholderHtml = str_replace("{encodedContent}", $originalContentBase64Encoded, $placeholderHtml);
		$content = $placeholderHtml;
		$customCssClasses = ''; //SPDSGVOSettings::get('embed_placeholder_custom_css_classes');

		//$content = '<div class="sp-dsgvo sp-dsgvo-embedding-container sp-dsgvo-embedding-' . $integrationToBlock->slug . ' '. $customCssClasses .'">' . $placeholderHtml . '<div class="sp-dsgvo-hidden-embedding-content sp-dsgvo-hidden-embedding-content-' . $integrationToBlock->slug . '" data-sp-dsgvo-embedding-slug="' . $integrationToBlock->slug . '">' . $originalContentBase64Encoded . '</div></div>';


		return $content;

	} else {
		return $content;
	}
}

add_shortcode('lw_content_block', 'LegalWebCloudContentBlockShortcode');
