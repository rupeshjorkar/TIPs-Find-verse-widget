<?php
/**
 */

class Tips_React_Proxy_API {

    const NAMESPACE = 'tips-proxy/v1';

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {

        register_rest_route( self::NAMESPACE, '/find-verse', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'find_verse' ),
        ) );

        register_rest_route( self::NAMESPACE, '/verse-story', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'verse_story' ),
        ) );

        register_rest_route( self::NAMESPACE, '/story', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'story_detail' ),
        ) );

        register_rest_route( self::NAMESPACE, '/search', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'search_story' ),
        ) );

        register_rest_route( self::NAMESPACE, '/source-story', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'source_story' ),
        ) );

        register_rest_route( self::NAMESPACE, '/categories', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'category_list' ),
        ) );

        register_rest_route( self::NAMESPACE, '/category-story', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'category_story' ),
        ) );

        register_rest_route( self::NAMESPACE, '/tree-view', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'tree_view' ),
        ) );

        register_rest_route( self::NAMESPACE, '/language-story', array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => array( $this, 'language_story' ),
        ) );
    }

    // =========================================================================
    // Callbacks — thin wrappers around Tips_API_Common (session logic reused)
    // =========================================================================

    public function find_verse( WP_REST_Request $request ) {
        $verse = sanitize_text_field( $request->get_param( 'verse' ) );
        return $this->respond( Tips_API_Common::find_verse_from_api( $verse ) );
    }

    public function verse_story( WP_REST_Request $request ) {
        $verse_id = sanitize_text_field( $request->get_param( 'verseId' ) );
        $page     = (int) $request->get_param( 'paged' );
        return $this->respond( Tips_API_Common::fetch_verse_stories_from_api( $verse_id, $page ) );
    }

    public function story_detail( WP_REST_Request $request ) {
        $story_id = sanitize_text_field( $request->get_param( 'storyId' ) );
        return $this->respond( Tips_API_Common::fetch_verse_story_details_from_api( $story_id ) );
    }

    public function search_story( WP_REST_Request $request ) {
        $id   = sanitize_text_field( $request->get_param( 'Id' ) );
        $page = (int) $request->get_param( 'paged' );
        return $this->respond( Tips_API_Common::fetch_search_stories_from_api( $id, $page ) );
    }

    public function source_story( WP_REST_Request $request ) {
        $source_id = sanitize_text_field( $request->get_param( 'sourceId' ) );
        $page      = (int) $request->get_param( 'paged' );
        return $this->respond( Tips_API_Common::fetch_source_details_from_api( $source_id, $page ) );
    }

    public function category_list( WP_REST_Request $request ) {
        return $this->respond( Tips_API_Common::pick_categorys_api() );
    }

    public function category_story( WP_REST_Request $request ) {
        $category_id = sanitize_text_field( $request->get_param( 'categoryId' ) );
        $page        = (int) $request->get_param( 'paged' );
        return $this->respond( Tips_API_Common::fetch_category_stories_from_api( $category_id, $page ) );
    }

    public function tree_view( WP_REST_Request $request ) {
        $term_id = sanitize_text_field( $request->get_param( 'termId' ) );
        return $this->respond( Tips_API_Common::fetch_tree_view_data( $term_id ) );
    }

    public function language_story( WP_REST_Request $request ) {
        $language_id = sanitize_text_field( $request->get_param( 'languageId' ) );
        $page        = (int) $request->get_param( 'paged' );
        return $this->respond( Tips_API_Common::fetch_language_stories_from_api( $language_id, $page ) );
    }

    // =========================================================================
    // Helper — normalize Tips_API_Common output into a REST response
    // =========================================================================

    private function respond( $result ) {
        if ( is_wp_error( $result ) ) {
            // Covers "not_activated" and any session/API failure.
            return new WP_REST_Response(
                array( 'error' => $result->get_error_message() ),
                $result->get_error_code() === 'not_activated' ? 403 : 502
            );
        }

        // verify_response() sometimes returns a raw string body — pass through as-is.
        return new WP_REST_Response( $result, 200 );
    }
}