<?php
class Tips_API_Common
{
    // =========================================================================
    // Shared helper — activation check + session headers + GET request
    // =========================================================================
    private static function remote_get_with_session($url, $timeout = 60)
    {
        $headers = Tips_Session_Manager::auth_headers();

        if ( is_wp_error( $headers ) ) {
            if ( $headers->get_error_code() === 'not_activated' ) {
                return Tips_Session_Manager::auth_headers();
            }
            // any other session/token error — return as WP_Error, handled by verify_response()/caller
            return $headers;
        }

        $response = wp_remote_get( $url, [
            "timeout" => $timeout,
            "headers" => $headers,
        ] );

        // ── If server rejected the token (expired/invalid), clear cache and retry once ──
        if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 401 ) {
            Tips_Session_Manager::clear_token();
            $headers = Tips_Session_Manager::auth_headers();

            if ( is_wp_error( $headers ) ) {
                if ( $headers->get_error_code() === 'not_activated' ) {
                    return Tips_Session_Manager::auth_headers();
                }
                return $headers;
            }

            $response = wp_remote_get( $url, [
                "timeout" => $timeout,
                "headers" => $headers,
            ] );
        }

        return $response;
    }

    // =========================================================================
    // API methods — now using remote_get_with_session()
    // =========================================================================

    public static function fetch_verse_stories_from_api($terms, $page)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/tip_verse?verseId=" . $terms . "&paged=" . $page
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_source_details_from_api($terms, $page)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/tip_source?sourceId=" . $terms . "&paged=" . $page
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_chapter_list_by_book_code($book_code)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/chapternumbers?bookId=" . $book_code
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_verse_story_details_from_api($story_id)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/story?storyId=" . $story_id
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_tree_view_data($term_id)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/tree-view?termId=" . $term_id
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function find_verse_from_api($search)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/find-verse?verse=" . $search
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_search_stories_from_api($Id, $page)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/search/?Id=" . $Id . "&paged=" . $page
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function pick_category_api()
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/pick_category/"
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }
    public static function pick_categorys_api()
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/pick_categories/"
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_category_stories_from_api($Id, $page)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/category_story/?categoryId=" . $Id . "&paged=" . $page
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    public static function fetch_language_stories_from_api($terms, $page)
    {
        $response = self::remote_get_with_session(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/tip_language?languageId=" . $terms . "&paged=" . $page
        );
        if ( is_string( $response ) ) {
            return $response;
        }
        return self::verify_response($response);
    }

    // =========================================================================
    // Left UNCHANGED — pre-activation / registration endpoints
    // (these can't require a session token: activation hasn't happened yet)
    // =========================================================================

    public static function tips_site_registration_api() {
        $site_domain = home_url();
        $site_title  = get_bloginfo('name');

        $response = wp_remote_post(
            TIPS_SEARCH_WIDGET_API_URL . 'wp-json/v1/bible/site_register',
            [
                'timeout' => 60,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'X-Site-Domain' => $site_domain,
                    'X-Site-Title'  => $site_title,
                ],
                'body' => json_encode([])
            ]
        );

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        if (!method_exists(__CLASS__, 'verify_response')) {
            return ['error' => 'verify_response() method is missing.'];
        }

        return self::verify_response($response);
    }

    public static function fetch_Tips_resource_git_access_token()
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/git_access_token",
            [
                "timeout" => 60,
                "headers" => [
                    "url" => home_url()
                ],
            ]
        );
        if (!method_exists(__CLASS__, 'verify_response')) {
            return ['error' => 'verify_response() method is missing.'];
        }
        $result = self::verify_response($response);
        return $result;
    }

    public static function fetch_validate_activation_from_api( $activation_key ) {
        $nonce = wp_create_nonce( 'tips_activation_nonce' );

        $response = wp_remote_post(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/validate_activation",
            [
                "timeout" => 60,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                "body" => wp_json_encode( [
                    "activation_key" => $activation_key,
                    "site_url"       => home_url(),
                    "nonce"          => $nonce,
                ] ),
            ]
        );

        $result = self::verify_response( $response );

        if ( is_array( $result ) ) {
            $result['_sent_nonce'] = $nonce;
        }

        return $result;
    }

    // =========================================================================
    // Unchanged helpers
    // =========================================================================

    public static function verify_response($response)
    {
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Please enter your activation key.');
        }
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return new WP_Error('api_error', 'API returned an unsuccessful response code: ' . $response_code);
        }
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return new WP_Error('api_error', 'No data found in the API response');
        }
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (is_string($body)) {
                return $body;
            }
            return new WP_Error('api_error', 'Error decoding JSON data.');
        }
        if (empty($data)) {
            return new WP_Error('api_error', 'No data found in the API response.');
        }
        return $data;
    }

    public static function custom_paginate_links($current_page, $total_pages, $url, $image_url, $base) {
        $html = '';
        if ($total_pages > 1) {
        $html .= paginate_links(array(
            'base' => $url . '/'.$base.'/' . '%_%',
            'format' => '?pages=%#%',
            'current' => $current_page,
            'total' => $total_pages,
            'show_all' => false,
            'mid_size' => 2,
            'end_size' => 1,
            'prev_text' => '<img src="' . esc_url($image_url) . '" >',
            'next_text' => '<img src="' . esc_url($image_url) . '" >',
            'after_page_number' => ($current_page == 2) ? '</span>' : '',
        ));
    }
    return $html;
    }
}