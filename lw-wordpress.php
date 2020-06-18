<?php

/**
 *
 * @link              https://www.legalweb.io
 * @since             1.0.0
 * @package           Lw Wordpress
 *
 * Plugin Name:   LegalWeb Wordpress
 * Plugin URI:    https://www.legalweb.io
 * Description:   Wordpress Plugin for DSGVO, Imprint & Pricacy Policy of legalweb.io
 * Version:       1.0.0
 * Author:        legalweb
 * Author URI:    https://www.legalweb.io
 * License:       A "Slug" license name e.g. GPL2 // todo
 * Text Domain:   lw-wordpress
*/

define('lw_aff_VERSION', '1.0.0');
define('lw_aff_NAME', 'lw-wordpress');
define('lw_aff_PLUGIN_NAME', 'lw-wordpress');
define('lw_aff_URL', plugin_dir_url( __FILE__ ));
define('lw_aff_PATH', plugin_dir_path( __FILE__ ));

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-lw-wordpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_lw_wordpress()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-lw-wordpress-language-tools.php';

    load_plugin_textdomain('lw-wordpress', false, basename(dirname(__FILE__)) . '/languages/');
    // Load correct DE language file if any DE language was selected
    $languageTools = LwWordpressLanguageTools::getInstance();
    if (in_array($languageTools->getCurrentLanguageCode(), ['de', 'de_DE', 'de_DE_formal', 'de_AT', 'de_CH', 'de_CH_informal'])) {
        // Load german language pack
        load_textdomain('lw-wordpress', plugin_dir_path(__FILE__).'languages/lw-wordpress-de_DE.mo');
    }


    $plugin = LwWordpress::instance();
	ob_start();
	add_rewrite_endpoint( 'wordpressaccount', EP_ROOT | EP_PAGES );
    $plugin->run();

}
add_action('init', 'run_lw_wordpress');