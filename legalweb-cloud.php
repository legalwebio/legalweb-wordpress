<?php

/**
 *
 * @link              https://www.legalweb.io
 * @since             1.0.1
 * @package           LegalWeb Cloud
 *
 * Plugin Name:   LegalWeb Cloud
 * Description:   Wordpress Plugin for GDPR/DSGVO, Imprint & Privacy Policy and other legal texts to use with the legalweb.io cloud service.
 * Version:       1.0.8
 * Author:        legalweb
 * Author URI:    https://www.legalweb.io
 * License URI:   http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:   legalweb-cloud
*/

define('legalweb_cloud_VERSION', '1.0.8');
define('legalweb_cloud_NAME', 'legalweb-cloud');
define('legalweb_cloud_PLUGIN_NAME', 'legalweb-cloud');
define('legalweb_cloud_URL', plugin_dir_url( __FILE__ ));
define('legalweb_cloud_PATH', plugin_dir_path( __FILE__ ));

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-legalweb-cloud.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_legalweb_cloud()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-legalweb-cloud-language-tools.php';

    load_plugin_textdomain('legalweb-cloud', false, basename(dirname(__FILE__)) . '/languages/');
    // Load correct DE language file if any DE language was selected
    $languageTools = LegalWebCloudLanguageTools::getInstance();
    if (in_array($languageTools->getCurrentLanguageCode(), ['de', 'de_DE', 'de_DE_formal', 'de_AT', 'de_CH', 'de_CH_informal'])) {
        // Load german language pack
        load_textdomain('legalweb-cloud', plugin_dir_path(__FILE__).'languages/legalweb-cloud-de_DE.mo');
    }


    $plugin = LegalWebCloud::instance();
	ob_start();
	add_rewrite_endpoint( 'wordpressaccount', EP_ROOT | EP_PAGES );
    $plugin->run();

}
add_action('init', 'run_legalweb_cloud');