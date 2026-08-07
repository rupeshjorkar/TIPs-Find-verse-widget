<?php

/**
 * Tips_Session_Manager
 *
 * Runs on the CUSTOMER site.
 *
 * Handles getting a session token from the main server and caching it locally
 * in a WP transient. All 25-30 API calls on the customer side use this class
 * to get the token — the main server is only contacted once every 23 hours.
 *
 * ── Usage in any customer-side API call ───────────────────────────────────────
 *
 *   $token = Tips_Session_Manager::get_token();
 *
 *   if ( is_wp_error( $token ) ) {
 *       // plugin not activated, or server unreachable
 *       return $token;
 *   }
 *
 *   // Use $token in your wp_remote_get / wp_remote_post headers:
 *   $response = wp_remote_get( $url, array(
 *       'headers' => Tips_Session_Manager::auth_headers(),
 *   ));
 */

class Tips_Session_Manager {

    private static $transient_key  = 'tips_session_token';
    private static $token_lifetime = 23 * HOUR_IN_SECONDS; // 1h buffer before server-side 24h expiry
    private static $server_url     = TIPS_SEARCH_WIDGET_API_URL . 'wp-json/v1/bible/get_session_token';

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Returns a valid session token string.
     * Fetches from main server only when the transient is missing or expired.
     *
     * @return string|WP_Error
     */
    public static function get_token() {

        // ── Return cached token if still valid ────────────────────────────
        $cached = get_transient( self::$transient_key );
        if ( ! empty( $cached ) ) {
            return $cached;
        }

        // ── Fetch a fresh token from main server ──────────────────────────
        return self::refresh_token();
    }

    /**
     * Returns the auth headers array ready to pass to wp_remote_get/post.
     * Use this in every API call so you never have to repeat the headers.
     *
     * @return array|WP_Error   Headers array on success, WP_Error if not activated.
     */
    public static function auth_headers() {
        $token = self::get_token();

        if ( is_wp_error( $token ) ) {
            return $token;
        }

        return array(
            'X-TIPS-Session-Token' => $token,
            'X-TIPS-Site-URL'      => self::normalize_url( home_url() ),
            'url'                  => self::normalize_url( home_url() ),   
        );
    }

    /**
     * Force-clears the cached token (e.g. on deactivation or after a 401 response).
     */
    public static function clear_token() {
        delete_transient( self::$transient_key );
    }

    // =========================================================================
    // Token fetch (contacts main server)
    // =========================================================================

    /**
     * Fetches a new session token from the main server.
     * Called automatically by get_token() when cache is empty/expired.
     *
     * @return string|WP_Error
     */
    private static function refresh_token() {

        // ── Read stored activation credentials ────────────────────────────
        $activation_key = self::decrypt( get_option( 'tips_activation_key', '' ) );
        $api_secret     = self::decrypt( get_option( 'tips_api_secret',     '' ) );

        if ( empty( $activation_key ) || empty( $api_secret ) ) {
            return new WP_Error( 'not_activated', 'Please enter your activation key.' );
        }

        // ── Build signed request ──────────────────────────────────────────
        $site_url  = self::normalize_url( home_url() );
        $timestamp = time();
        $canonical = $activation_key . "\n" . $site_url . "\n" . $timestamp;
        $signature = hash_hmac( 'sha256', $canonical, $api_secret );

        // ── POST to main server ───────────────────────────────────────────
        $response = wp_remote_post(
            self::$server_url,
            array(
                'timeout'     => 15,
                'redirection' => 0,
                'body'        => array(
                    'activation_key' => $activation_key,
                    'site_url'       => home_url(),
                    'timestamp'      => $timestamp,
                    'signature'      => $signature,
                ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        $body      = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $http_code !== 200 || empty( $body['session_token'] ) ) {
            $message = $body['message'] ?? 'Failed to get session token from server.';
            return new WP_Error( 'session_fetch_failed', $message, array( 'status' => $http_code ) );
        }

        // ── Cache for 23h and return ──────────────────────────────────────
        set_transient( self::$transient_key, $body['session_token'], self::$token_lifetime );

        return $body['session_token'];
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private static function normalize_url( $url ) {
        $url = strtolower( trim( (string) $url ) );
        $url = preg_replace( '#^https?://#', '', $url );
        $url = preg_replace( '#^www\.#', '', $url );
        return rtrim( $url, '/' );
    }

    private static function decrypt( $stored ) {
        if ( empty( $stored ) ) return '';
        $secret = substr( hash( 'sha256', AUTH_KEY ), 0, 32 );
        $parts  = explode( '::', base64_decode( $stored ), 2 );
        if ( count( $parts ) !== 2 ) return '';
        return (string) openssl_decrypt( $parts[1], 'AES-256-CBC', $secret, 0, $parts[0] );
    }
}