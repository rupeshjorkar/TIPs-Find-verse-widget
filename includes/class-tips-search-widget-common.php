<?php
class Tips_Common
{
    public function __construct()
    {
        add_action('init', array($this, 'initialize'));
        add_action('init', array($this, 'app_output_buffer'));
    }
    public function app_output_buffer() {
         ob_start();
    }
    public function initialize()
    {
        $this->enqueue_custom_plugin_styles_callback();
        add_filter('the_content', array(__CLASS__, 'custom_content_filter'));
        add_filter('wp_kses_allowed_html', array(__CLASS__, 'extend_wp_kses_post_allowed_tags'));
    }

    public static function enqueue_custom_plugin_styles_callback()
    {
        wp_enqueue_style('custom', TIPS_SEARCH_WIDGET_URL . 'css/custom-css.css');
    }
    public static function custom_content_filter($content)
    {
        
        if (is_page('tip_source')) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-source-details.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        } 
        elseif(is_page('detail')) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-verse-story-details.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        }  
        elseif (is_page('tip_verse')) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-verse-story.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        } elseif (is_page("tree-view")) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-tree-view.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        }
        elseif (is_page("find-verse")) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-find-verse.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        } 
        elseif (is_page("search-story")) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-search-story.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        } 
        elseif (is_page("tips-category")) {
            ob_start();
            include TIPS_SEARCH_WIDGET_DIR . "templates/template-tips-category.php";
            $dynamic_content = ob_get_clean();
            return $dynamic_content;
        } 
        else {
            return $content;
        }
    }
    public static function extend_wp_kses_post_allowed_tags($tags ){
        $tags['iframe'] = array(
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'frameborder'     => true,
            'allowfullscreen' => true,
        );
         return $tags;
    }
    public static function tip_term_get_back_translations(int $term_id): array
    {
        $translations = get_term_meta( $term_id, 'translation', true );

        if ( !$translations || $translations == '' ) return [];
    
        $term = get_term( $term_id );
        $original_terms = [];
    
        foreach ( array( 'greek', 'hebrew', 'aramaic' ) as $original_language ) {
            $original_term = get_term_meta( $term_id, '_term_' . $original_language, true );
    
            if ( $original_term ) {
                $original_terms[] = $original_term;
            }
        }
    
        $root = new StdClass();
        $root->name = $term->name;
        $root->original = implode( ', ', $original_terms );
    
        $tree = [$root];
    
        // jigisha:c-metric    
        foreach ($translations as $translation) {
            $node = new StdClass();
            $node->name = $translation['translation'];
            
            // Check if $translation['language'] is an array before using array_map
            if(isset($translation['language'])){
            if (is_array($translation['language'])) {
                $node->language = implode(', ', array_map(function ($term_id) {
                    $language_term = get_term($term_id, 'tip_language');
                    return $language_term->name;
                }, $translation['language']));
            }} else {
                $node->language = __('Unknown', 'tips-find-ferse-within-site');
            }
            $node->parent = 0;        
            $tree[] = $node;
        }
        return $tree;
    
    }
}
