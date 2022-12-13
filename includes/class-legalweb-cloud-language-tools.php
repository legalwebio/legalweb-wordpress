<?php


class LegalWebCloudLanguageTools
{
    public $defaultLanguage = 'en_EN';


    private static $instance;

    public function __construct()
    {
        $this->defaultLanguage = get_option('WPLANG', 'en_EN');
    }


    public static function init()
    {
        return new self;
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    static function normalizeLocaleCode($locale)
    {
        try {
            if (substr( $locale, 0, 2 ) === 'de') $locale = 'de_DE';
            if (substr( $locale, 0, 2 ) === 'en') $locale = 'en_EN';
            if (substr( $locale, 0, 2 ) === 'fr') $locale = 'fr_FR';
            if (substr( $locale, 0, 2 ) === 'it') $locale = 'it_IT';
	        if (substr( $locale, 0, 2 ) === 'es') $locale = 'es_ES';
            if ($locale === "") $locale = "en_EN";
            return $locale;
        } catch (Exception $e) {
            return 'en_EN';
        }
    }

    public function checkIfLanguagePluginIsActive()
    {
        return $this->getTypeOfLanguagePlugin() != 'none';
    }

    public function getTypeOfLanguagePlugin()
    {
		/*
        if ((defined('ICL_LANGUAGE_CODE') || defined('POLYLANG_FILE')) == false) return 'none';
        else {
            if(function_exists('icl_get_languages')) return 'wpml';
            if(function_exists('pll_current_language')) return 'polylang';
        }
		*/
	    if (function_exists('icl_get_languages')) return 'wpml';
	    if (function_exists('pll_current_language')) return 'polylang';
	    if (class_exists( 'TRP_Translate_Press' )) return 'translatepress';

        return "none";
    }

    public function getCurrentLanguageCode()
    {
        $currentLanguage = null;

        switch ($this->getTypeOfLanguagePlugin())
        {
            case "wpml":
                $currentLanguage = apply_filters('wpml_current_language', null);
                break;
            case "polylang":
                $currentLanguage = pll_current_language();
                // get default language if we dont get a current one
                if (empty($currentLanguage)) {
                    $currentLanguage = pll_default_language();
                }
                break;
	        case "translatepress":
		        $currentLanguage = get_locale();
				break;
            case "none":
                $currentLanguage = $this->defaultLanguage;
                break;
            default:
                $currentLanguage = $this->defaultLanguage;
        }

        if (empty($currentLanguage) || $currentLanguage === 'all') {
            $currentLanguage = $this->getDefaultLanguageCode();
        }

        return $this->normalizeLocaleCode($currentLanguage);
    }

    public function getDefaultLanguageCode()
    {
        $currentLanguage = null;

        switch ($this->getTypeOfLanguagePlugin())
        {
            case "wpml":
                $currentLanguage = apply_filters('wpml_default_language', null);
                break;
            case "polylang":
                $currentLanguage = pll_default_language();
                // get default language if we dont get a current one
                if (empty($currentLanguage)) {
                    $currentLanguage = pll_default_language();
                }
                break;
            case "none":
                $currentLanguage = $this->defaultLanguage;
                break;
            default:
                $currentLanguage = $this->defaultLanguage;
        }

        if (empty($currentLanguage) || $currentLanguage === 'all') {
            $currentLanguage = $this->defaultLanguage;
        }

        return $this->normalizeLocaleCode($currentLanguage);

    }

}