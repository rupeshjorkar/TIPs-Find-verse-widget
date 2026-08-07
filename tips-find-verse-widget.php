<?php
/**
 * Plugin Name:       TIPs: Find Verse Widget
 * Description:       This plugin enables three search boxes on the site and allows customers to search TIPs verse data directly within your website. This plugin enables seamless integration of TIPs content, allowing customers to easily access and view specific verses without leaving your site.
 * Author:            Rupesh Jorkar (RJ)
 * Version:           2.4
 * Requires at least: 5.2
 * Tested up to:      6.5
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tips-find-verse-widget
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die( "Please don't try to access this file directly." );
}

if ( ! defined( 'PLUGIN_NAME' ) ) {
    define( 'PLUGIN_NAME', 'tips-find-ferse-within-site' );
}
if ( ! defined( 'TIPS_SEARCH_WIDGET_VERSION' ) ) {
    define( 'TIPS_SEARCH_WIDGET_VERSION', '2.4' );
}
if ( ! defined( 'TIPS_SEARCH_WIDGET_PLUGIN_URL' ) ) {
    define( 'TIPS_SEARCH_WIDGET_PLUGIN_URL', __FILE__ );
}
if ( ! defined( 'TIPS_SEARCH_WIDGET_DIR' ) ) {
    define( 'TIPS_SEARCH_WIDGET_DIR', plugin_dir_path( TIPS_SEARCH_WIDGET_PLUGIN_URL ) );
}
if ( ! defined( 'TIPS_SEARCH_WIDGET_URL' ) ) {
    define( 'TIPS_SEARCH_WIDGET_URL', plugin_dir_url( __FILE__ ) );
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
if ( ! defined( 'TIPS_API_SECRET' ) ) {
    define( 'TIPS_API_SECRET', '9sV4jM2wPqL7xX1nZ8bT3yR5vF6mC9dG4hB1kP2lO0qJ3xV6zN9mC1bV0xZ2aK4' );
}

// Include necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-search-widget-activation.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-search-widget-common.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-search-widget-api-common.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-search-widget-class.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-find-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-session-manager.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tips-react-proxy-api.php';

new Tips_React_Proxy_API();

// Activation hook
register_activation_hook( __FILE__, array( 'Tips_Search_Widget_Activation', 'activate' ) );

// Initialize classes
$tips_data_management_active = new Tips_Search_Widget_Activation();
$tips_common                 = new Tips_Common();
$tips_api_common             = new Tips_API_Common();

// ── Fetch GitHub access token with WP_Error guard ────────────────────────────
$git_access_token = Tips_API_Common::fetch_Tips_resource_git_access_token();

if ( is_wp_error( $git_access_token ) ) {
    // HTTP request failed — log it and continue without authentication.
    // The update checker will still work; it just won't have a token.
    error_log( 'TIPs plugin: could not fetch git access token — ' . $git_access_token->get_error_message() );
    $git_access_token = '';
} elseif ( is_array( $git_access_token ) ) {
    $git_access_token = isset( $git_access_token['git_access_token'] ) ? $git_access_token['git_access_token'] : '';
} else {
    // Unexpected return type (null, string, etc.)
    error_log( 'TIPs plugin: unexpected type returned from fetch_Tips_resource_git_access_token — ' . gettype( $git_access_token ) );
    $git_access_token = '';
}

// ── Plugin update checker ─────────────────────────────────────────────────────
require plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/rupeshjorkar/TIPs-Find-verse-widget',
    __FILE__,
    'tips-find-verse-widget'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );

// Only set authentication if we actually got a token.
if ( ! empty( $git_access_token ) ) {
    $myUpdateChecker->setAuthentication( $git_access_token );
}
