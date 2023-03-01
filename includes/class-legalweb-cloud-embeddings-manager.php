<?php

class LegalWebCloudEmbeddingsManager
{
	private $registeredEmbeddingApis = [];

	protected function __construct()
	{
		$apiData = (new LegalWebCloudApiAction())->getOrLoadApiData();

		// write popup scripts
		if ( $apiData != null &&
		     isset($apiData->services) &&
		     isset($apiData->services->dppopupjs) &&
		     isset($apiData->services->dppopupconfig->spDsgvoGeneralConfig) &&
		     isset($apiData->services->dppopupconfig->spDsgvoIntegrationConfig)) {

			foreach ( $apiData->services->dppopupconfig->spDsgvoIntegrationConfig as $integration ) {

				if ( $integration->category == LegalWebCloudConstants::CATEGORY_SLUG_EMBEDDINGS ) {
					$this->registeredEmbeddingApis[] = $integration;
				}
			}
		}
	}


	final public static function getInstance()
	{
		static $instances = array();

		$calledClass = get_called_class();

		if (!isset($instances[$calledClass]))
		{
			$instances[$calledClass] = new $calledClass();
		}

		return $instances[$calledClass];
	}

	public function getEmbeddingApiBySlug($slug)
	{
		foreach($this->registeredEmbeddingApis as $key => $integration)
		{
			if ($integration->slug == $slug) return $integration;
		}
		return  null;
	}

	public function findAndProcessIframes($content)
	{
		if (legalweb_disable_on_backend()) return $content;

		$content = preg_replace_callback('/(\<p\>)?(<iframe.+?(?=<\/iframe>)<\/iframe>){1}(\<\/p\>)?/i', [$this, 'processIframe'], $content);
		return $content;
	}

	public function findAndProcessOembeds($content, $url)
	{
		return $this->processContentBlocking($content, $url);
	}

	public function processIframe($matches)
	{
		$content = $matches[0];

		// Detect host
		$srcUrlOfIframe = [];

		preg_match('/src=("|\')([^"\']{1,})(\1)/i', $matches[2], $srcUrlOfIframe);

		// Skip iframes without src attribute of where src is about:blank
		if (!empty($srcUrlOfIframe[2]) && $srcUrlOfIframe[2] !== 'about:blank') {
			$content = $this->processContentBlocking($matches[0], $srcUrlOfIframe[2]);
		}

		return $content;
	}

	protected function processContentBlocking($content, $urlOfIframe = '')
	{

		if(empty($urlOfIframe)) return $content;

		$currentUrl = parse_url($urlOfIframe);

		// now find which embedding api should be called to process the content
		$found = false;
		$integrationToBlock = null;
		if (empty($currentUrl) == false)
		{
			foreach($this->registeredEmbeddingApis as $key => $integration)
			{
				if (empty($integration->hosts)) continue;

				foreach ($this->getHostsArray($integration) as $host) {

					if (strpos($currentUrl['host'].$currentUrl['path'], $host) !== false)
					{
						$found = true;
						$integrationToBlock = $integration;
						break;
					}
				}
				if ($found) break; // break the second loop too
			}
		}
		if ($found == false || $integrationToBlock == null) return $content;

		// if its allowed by cookie nothing is to do here. otherwise replace iframes, show image, add optin handler
		if ($this->checkIfIntegrationIsAllowed($integrationToBlock->slug) == true) return $content;

		$originalContentBase64Encoded = base64_encode(($content)); //htmlentities
		$lang = LegalWebCloudLanguageTools::getInstance()->getCurrentLanguageCode();
		$lang = substr( $lang, 0, 2 );
		$placeholderHtml = base64_decode($integrationToBlock->placeholders->{$lang});
		$placeholderHtml = str_replace("{encodedContent}", $originalContentBase64Encoded, $placeholderHtml);
		$content = $placeholderHtml;

		return $content;
	}

	static function getDummyPlaceholderForMutationObserver($embeddingApi)
	{
		$processedContent =  $embeddingApi->processContent('');

		$customCssClasses = SPDSGVOSettings::get('embed_placeholder_custom_css_classes');

		$content = '<div class="sp-dsgvo sp-dsgvo-embedding-container sp-dsgvo-embedding-' . $embeddingApi->slug . ' '. $customCssClasses .'">' . $processedContent . '<div class="sp-dsgvo-hidden-embedding-content sp-dsgvo-hidden-embedding-content-' . $embeddingApi->slug . '" data-sp-dsgvo-embedding-slug="' . $embeddingApi->slug . '">{encodedContent}</div></div>';

		return $content;
	}

	public final function checkIfIntegrationIsAllowed($integrationSlug)
	{

		// first check if the visitor interacted with our notice/plugin
		$cookieDecisionMade = isset($_COOKIE[LegalWebCloudConstants::COOKIE_NAME]);
		if ($cookieDecisionMade == false) return false;

		// the settings are stored in an array like  "integration-slug" => '0'
		$integrationSettings = (json_decode(stripslashes($_COOKIE[LegalWebCloudConstants::COOKIE_NAME])));
		// check if it is a class and has the property
		if ($integrationSettings instanceof stdClass  == false || !property_exists($integrationSettings, 'integrations')) return false;

		$integrationSettingsArray = (array)$integrationSettings;
		$integrationSettingsArray = legalweb_recursive_sanitize_text_field($integrationSettingsArray);

		$enabledIntegrations = $integrationSettingsArray['integrations'];
		$integrationSettings = null; // we only need here the array of enabled integrations, which we sanitze and filter in the above lines. the rest gets nulled
		if ($enabledIntegrations == false || isset($enabledIntegrations) == false) return false;

		return in_array($integrationSlug, $enabledIntegrations);
	}

	public final function getHostsArray($integrationToBlock)
	{
		return explode(';', $integrationToBlock->hosts);
	}
}