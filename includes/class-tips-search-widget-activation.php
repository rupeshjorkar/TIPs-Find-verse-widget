<?php
class Tips_Search_Widget_Activation
{
    public function __construct()
    {
        // add_action('admin_menu', array($this, 'tips_data_management_create_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_script'));
        add_filter('query_vars',  array($this, 'add_custom_query_vars'));
        add_shortcode('tips_find_verse', array($this, 'tips_find_verse_fun')); // Short code
    }
    public function enqueue_custom_script()
    {
        wp_enqueue_script( 'chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.js', [], '1.0', false );
		wp_enqueue_script( 'chartjs-plugin-datalabels', TIPS_SEARCH_WIDGET_URL . 'js/chartjs-plugin-datalabels.js', ['chartjs'], '1.0', false );		
		wp_enqueue_script( 'chartjs-chart-graph', TIPS_SEARCH_WIDGET_URL . 'js/index.umd.js', ['chartjs', 'chartjs-plugin-datalabels'], '1.0', false );	
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
        wp_enqueue_script( 'select2-js', TIPS_SEARCH_WIDGET_URL . 'js/select2_custom.js', ['select2'], '1.0', false ); 
        wp_localize_script( 'select2-js', 'select2', array( 'category_url' => get_permalink( get_page_by_path( 'tips-category' ) ) ) );
    }

    public static function activate()
    {
        self::add_pages_with_templates();
    }
    private static function add_pages_with_templates()
    {
        //source detail page : start
        $source_detail_page_title = "Source Detail";
        $source_detail_page_content = "";
        $source_detail_page_slug = "tip_source";

        // Check if the page already exists
        $tips_data_management_source_detail_page = get_page_by_path($source_detail_page_slug);

        // If the page does not exist, create it
        if (!$tips_data_management_source_detail_page) {
            $tips_data_management_source_detail_page_args = [
                "post_title" => $source_detail_page_title,
                "post_content" => $source_detail_page_content,
                "post_name" => $source_detail_page_slug,
                "post_status" => "publish",
                "post_type" => "page",
            ];

            $tips_data_management_source_detail_page_page_id = wp_insert_post(
                $tips_data_management_source_detail_page_args
            );
            update_post_meta(
                $tips_data_management_source_detail_page_page_id,
                "_wp_page_template",
                ""
            );
        }
        //source detail page : end

        //verse stories page:start 
        $verse_story_page_title = "Verse Stories";
        $verse_story_page_content = "";
        $verse_story_page_slug = "tip_verse";

        // Check if the page already exists
        $tips_auth_search_page = get_page_by_path($verse_story_page_slug);

        // If the page does not exist, create it
        if (!$tips_auth_search_page) {
            $tips_auth_search_page_page_args = [
                "post_title" => $verse_story_page_title,
                "post_content" => $verse_story_page_content,
                "post_name" => $verse_story_page_slug,
                "post_status" => "publish",
                "post_type" => "page",
            ];

            $tips_auth_search_page_id = wp_insert_post(
                $tips_auth_search_page_page_args
            );
            update_post_meta(
                $tips_auth_search_page_id,
                "_wp_page_template",
                ""
            );
        }
        //verse stories page:end

        //tree view page:start 
        $tree_view_page_title = "Tree View";
        $tree_view_page_content = "";
        $tree_view_page_slug = "tree-view";

        // Check if the page already exists
        $tips_data_management_search_page = get_page_by_path($tree_view_page_slug);

        // If the page does not exist, create it
        if (!$tips_data_management_search_page) {
            $tips_data_management_search_page_args = [
                "post_title" => $tree_view_page_title,
                "post_content" => $tree_view_page_content,
                "post_name" => $tree_view_page_slug,
                "post_status" => "publish",
                "post_type" => "page",
            ];

            $tips_data_management_search_page_id = wp_insert_post(
                $tips_data_management_search_page_args
            );
            update_post_meta(
                $tips_data_management_search_page_id,
                "_wp_page_template",
                ""
            );
        }
        //story detail page page:end

           $story_detail_page_title = "Story";
           $story_detail_page_content = "";
           $story_detail_page_slug = "detail";
   
           // Check if the page already exists
           $tips_data_management_search_page = get_page_by_path($story_detail_page_slug);
   
           // If the page does not exist, create it
           if (!$tips_data_management_search_page) {
               $tips_data_management_search_page_args = [
                   "post_title" => $story_detail_page_title,
                   "post_content" => $story_detail_page_content,
                   "post_name" => $story_detail_page_slug,
                   "post_status" => "publish",
                   "post_type" => "page",
               ];
   
               $tips_data_management_search_page_id = wp_insert_post(
                   $tips_data_management_search_page_args
               );
               update_post_meta(
                   $tips_data_management_search_page_id,
                   "_wp_page_template",
                   ""
               );
           }
           //story detail page:end

        //Find Verse page : start
        $find_verse_page_title = "Find Verse";
        $find_verse_page_content = "";
        $find_verse_page_slug = "find-verse";

        // Check if the page already exists
        $tips_data_management_find_verse_page = get_page_by_path($find_verse_page_slug);

        // If the page does not exist, create it
        if (!$tips_data_management_find_verse_page) {
            $tips_data_management_find_verse_page_args = [
                "post_title" => $find_verse_page_title,
                "post_content" => $find_verse_page_content,
                "post_name" => $find_verse_page_slug,
                "post_status" => "publish",
                "post_type" => "page",
            ];

            $tips_data_management_find_verse_page_page_id = wp_insert_post(
                $tips_data_management_find_verse_page_args
            );
            update_post_meta(
                $tips_data_management_find_verse_page_page_id,
                "_wp_page_template",
                ""
            );
        }
        //Search Story page : start
        $search_story_title = "Search Story";
        $search_story_content = "";
        $search_story_slug = "search-story";

        // Check if the page already exists
        $tips_data_management_search_story = get_page_by_path($search_story_slug);

        // If the page does not exist, create it
        if (!$tips_data_management_search_story) {
            $tips_data_management_search_story_args = [
                "post_title" => $search_story_title,
                "post_content" => $search_story_content,
                "post_name" => $search_story_slug,
                "post_status" => "publish",
                "post_type" => "page",
            ];

            $tips_data_management_search_story_page_id = wp_insert_post(
                $tips_data_management_search_story_args
            );
            update_post_meta(
                $tips_data_management_search_story_page_id,
                "_wp_page_template",
                ""
            );
        }
        //Search Story page : start
        $pick_category_title = "Tips Category";
        $pick_category_content = "";
        $pick_category_slug = "tips-category";

        // Check if the page already exists
        $tips_data_management_pick_category = get_page_by_path($pick_category_slug);

        // If the page does not exist, create it
        if (!$tips_data_management_pick_category) {
            $tips_data_management_pick_category_args = [
                "post_title" => $pick_category_title,
                "post_content" => $pick_category_content,
                "post_name" => $pick_category_slug,
                "post_status" => "publish",
                "post_type" => "page",
            ];

            $tips_data_management_pick_category_page_id = wp_insert_post(
                $tips_data_management_pick_category_args
            );
            update_post_meta(
                $tips_data_management_pick_category_page_id,
                "_wp_page_template",
                ""
            );
        }

    }
    // public static function tips_data_management_create_menu()
    // {
    //     add_menu_page("Tips", "Tips", "manage_options", "tips", array(__CLASS__, 'tips_auth_admin_page_callback'));
    // }
    // public static function tips_auth_admin_page_callback() {
    //     echo '<div class="wrap">';
    //     echo '<h1>Tips Data Management</h1>';
    //     echo '<p>Welcome to the Tips Data Management plugin settings page.</p>';
    //     echo '</div>';
    // }
    public function add_custom_query_vars($vars) {
        $vars[] = 'pages';
        $vars[] = 'name';
        return $vars;
    }
    
    public function tips_find_verse_fun() {
        ob_start();
        $plugin_file_path = plugin_dir_path(dirname(__FILE__)) . 'templates/template-search-box.php';
        if (file_exists($plugin_file_path)) {
            include($plugin_file_path);
        } else {
            echo 'Plugin template file not found.';
        }
        $content = ob_get_clean();
        return $content;
    }
}
