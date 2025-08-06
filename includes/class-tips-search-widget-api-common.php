<?php
class Tips_API_Common
{
    public static function fetch_verse_stories_from_api($terms,$page)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/tip_verse?verseId=".$terms."&paged=".$page,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ]
            ]
        );
        $result = self::verify_response($response); 
        return $result;
    }
    public static function fetch_source_details_from_api($terms,$page)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL."wp-json/v1/bible/tip_source?sourceId=".$terms."&paged=".$page,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ],
            ]
        );
        $result = self::verify_response($response); 
        return $result;     
    }
    public static function fetch_chapter_list_by_book_code($book_code)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/chapternumbers?bookId=".$book_code,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ],
            ]
        );
        $result = self::verify_response($response);
        return $result;
    }
    public static function fetch_verse_story_details_from_api($story_id)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL."wp-json/v1/bible/story?storyId=".$story_id,
              [
                  "timeout" => 60, // Increased timeout to 60 seconds
                  "headers" => [
                      "url" => home_url()
                  ],
              ]
          );  
        $result = self::verify_response($response);
        return $result;        
    }
    public static function fetch_tree_view_data($term_id)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/tree-view?termId=" . $term_id,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url(),
                ],
            ]
        );
        $result = self::verify_response($response);
        return $result;        
    }
    
    public static function verify_response($response)
    {
        // Check for error
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Unable to retrieve data from the API.');
        }
        // Check for successful response code
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return new WP_Error('api_error', 'API returned an unsuccessful response code: ' . $response_code);
        }
        // Retrieve the body of the response
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return new WP_Error('api_error', 'No data found in the API response');
        }
        // Decode the JSON data
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (is_string($body)) {
                return $body;
            }
            return new WP_Error('api_error', 'Error decoding JSON data.');
        }
        // Check if the data is not empty
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
            'mid_size' => 2, // Show 2 pages on either side of the current page
            'end_size' => 1, // Show the last page
            'prev_text' => '<img src="' . esc_url($image_url) . '" >',
            'next_text' => '<img src="' . esc_url($image_url) . '" >',
            'after_page_number' => ($current_page == 2) ? '</span>' : '',
        ));
    }
    return $html;
    }
    public static function find_verse_from_api($search)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/find-verse?verse=".$search,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ]
            ]
        );
        $result = self::verify_response($response); 
        return $result;
    }
    public static function fetch_search_stories_from_api($Id,$page)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/search/?Id=".$Id."&paged=".$page,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ]
            ]
        );
        $result = self::verify_response($response); 
        return $result;
    }
    public static function pick_category_api()
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/pick_category/",
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ]
            ]
        );
        $result = self::verify_response($response); 
        return $result;
    }
    public static function fetch_category_stories_from_api($Id,$page)
    {
        $response = wp_remote_get(
            TIPS_SEARCH_WIDGET_API_URL . "wp-json/v1/bible/category_story/?categoryId=".$Id."&paged=".$page,
            [
                "timeout" => 60, // Increased timeout to 60 seconds
                "headers" => [
                    "url" => home_url()
                ]
            ]
        );
        $result = self::verify_response($response); 
        return $result;
    }

}
