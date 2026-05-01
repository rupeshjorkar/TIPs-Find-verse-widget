<?php
/**
 * Plugin Name:       TIPs: Find Verse Widget
 * Description:       This plugin enables three search boxes on the site and allows customers to search TIPs verse data directly within your website. This plugin enables seamless integration of TIPs content, allowing customers to easily access and view specific verses without leaving your site.
 * Author:            Rupesh Jorkar (RJ)
 * Version:           2.1
 * Requires at least: 5.2
 * Tested up to:      6.5
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tips-find-verse-widget
 */


// If this file is called directly, abort.

if (! defined( 'ABSPATH' ) ) {
	die( "Please don't try to access this file directly." );
}
if ( ! defined( 'PLUGIN_NAME' ) ) {
	define( 'PLUGIN_NAME', 'tips-find-ferse-within-site' );
}
if ( ! defined( 'TIPS_SEARCH_WIDGET_VERSION' ) ) {
	define( 'TIPS_SEARCH_WIDGET_VERSION', '2.1' );
}

if ( ! defined( 'TIPS_SEARCH_WIDGET_PLUGIN_URL' ) ) {
    define( 'TIPS_SEARCH_WIDGET_PLUGIN_URL', __FILE__ );
}

if ( ! defined( 'TIPS_SEARCH_WIDGET_DIR' ) ) {
    define( 'TIPS_SEARCH_WIDGET_DIR', plugin_dir_path( TIPS_SEARCH_WIDGET_PLUGIN_URL ) );
}

if ( ! defined( 'TIPS_SEARCH_WIDGET_URL' ) ) {
	define( 'TIPS_SEARCH_WIDGET_URL', plugin_dir_url( __FILE__ )  );
}

if ( ! defined( 'TIPS_SEARCH_WIDGET_BASENAME' ) ) {

	define( 'TIPS_SEARCH_WIDGET_BASENAME', plugin_basename( TIPS_SEARCH_WIDGET_PLUGIN_URL ) );

}
if ( ! defined( 'TIPS_SEARCH_WIDGET_TEXT_DOMAIN' ) ) {
	define( 'TIPS_SEARCH_WIDGET_TEXT_DOMAIN', 'tips-find-ferse-within-site' );
}

if ( ! defined( 'TIPS_SEARCH_WIDGET_SLUG' ) ) {
	define( 'TIPS_SEARCH_WIDGET_SLUG', 'tips-find-ferse-within-site' );
}
if ( ! defined( 'TIPS_SEARCH_WIDGET_API_URL' ) ) {
	define( 'TIPS_SEARCH_WIDGET_API_URL', 'https://tips.translation.bible/' );
}


// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-tips-search-widget-activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tips-search-widget-common.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tips-search-widget-api-common.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tips-search-widget-class.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-tips-find-settings.php';

// Activation hook
register_activation_hook(__FILE__, array('Tips_Search_Widget_Activation', 'activate'));

// Initialize classes

$tips_data_management_active = new Tips_Search_Widget_Activation();
$tips_common = new Tips_Common();
$tips_api_common = new Tips_API_Common();


$git_access_token = Tips_API_Common::fetch_Tips_resource_git_access_token();
if (!empty($git_access_token)) {
	$git_access_token = isset($git_access_token['git_access_token']) ? $git_access_token['git_access_token'] : '';
}

require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/rupeshjorkar/TIPs-Find-verse-widget',
	__FILE__,
	'tips-find-verse-widget'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication($git_access_token);