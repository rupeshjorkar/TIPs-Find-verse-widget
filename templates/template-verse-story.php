<?php

/**
 * Template Name: VerseStoryPage
 * Description: Verse Stories will be listed
 */

$terms = isset($_GET['verseId']) ? sanitize_text_field($_GET['verseId']) : '';
$page = (get_query_var('pages')) ? sanitize_text_field(get_query_var('pages')): 1;
$data = [];
if($terms) :
    $data = Tips_API_Common::fetch_verse_stories_from_api($terms,$page);
    $tips_enable_greek_translation = get_option('tips_enable_greek_translation', 'off');
    $tips_enable_english_translation = get_option('tips_enable_english_translation', 'off');
endif; 
$url = site_url();
?>
<div class="container entry-content inner-page-design">
    <?php if (isset($_GET['verseId']) && is_array($data) && !empty($data)) : ?>
        <?php if (isset($data["VerseData"]["book_name"])) : ?>
            <div class="page_main_title">
                <h1 class="page-main-title">
                    Verse: <?php echo esc_html($data["VerseData"]["book_name"]) . ' ' . esc_html($data["VerseData"]["chapter_no"]) . ':' . esc_html($data["VerseData"]["verse_no"]); ?>
                </h1>
            </div>
        <?php endif; ?> 
        <?php if ($tips_enable_greek_translation === 'on') : ?>
            <div class="verse-text-original">
                <?php if (isset($data["VerseData"]["verse_gree"])) : ?>
                    <p><?php echo esc_html($data["VerseData"]["verse_gree"]); ?></p> 
                <?php endif; ?>     
            </div> 
        <?php endif; ?> 
        <?php if ($tips_enable_english_translation === 'on') : ?>
            <div class="verse-text-new">
                <?php if (isset($data["VerseData"]["verse_english"])) : ?>
                    <?php echo wp_kses_post($data["VerseData"]["verse_english"]); ?>
                <?php endif; ?>  
            </div>    
        <?php endif; ?> 
        <div class="next-previous">
            <?php if (isset($data["VerseData"]["previous_verse"])) : 
                    $page_slug = 'tip_verse';
                    $page = get_page_by_path( $page_slug );
                    $previous_verse = sanitize_text_field( $data["VerseData"]["previous_verse"]); 
                    if ( $page instanceof WP_Post ) {
                        $page_link = esc_url( get_permalink( $page->ID ) ); 
                    } else {
                        $page_link = '';
                    }
                ?>
                <span class="previous">
                    <a href="<?php echo esc_url( add_query_arg( 'verseId', $previous_verse, $page_link ) ); ?>"> 
                        « <?php echo esc_html( $data["VerseData"]["previous_verse_name"] ); ?> 
                    </a>
                </span>
            <?php endif; ?>  
             <?php if (isset($data["VerseData"]["next_verse"])) : 
                $next_verse = sanitize_text_field( $data["VerseData"]["next_verse"]); 
                 $page_slug = 'tip_verse';
                    $page = get_page_by_path( $page_slug );
                    if ( $page instanceof WP_Post ) {
                        $page_link = esc_url( get_permalink( $page->ID ) ); 
                    } else {
                        $page_link = '';
                    }
                ?>
                <span class="seprater"></span><span class="next">
                    <a href="<?php echo esc_url( add_query_arg( 'verseId', $next_verse, $page_link ) ); ?>"> 
                         <?php echo esc_html( $data["VerseData"]["next_verse_name"] ); ?> »
                    </a>
                </span>   
            <?php endif; ?>           
        </div>
        <section class="book-stories-section">
            <?php foreach ($data as $verse) : ?>
                <article class="book-story">
                    <?php if (isset($verse['slug'])) : ?>
                        <?php
                         if (isset($verse["title"]["rendered"])) : ?>
                            <a href="<?php echo esc_url($verse["title"]["title_link"]); ?>" alt="">
                                <h2>
                                    <?php
                                    echo wp_kses_post(sanitize_text_field($verse['title']['rendered']));
                                    echo '<span class="term with-original" data-original="' . esc_attr($verse['title']['hover_title']) . '">';
                                    echo wp_kses_post(sanitize_text_field($verse['title']['hover_title']));
                                    echo '</span>';
                                    ?>
                                </h2>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($verse['geographical_link']['title'])) : ?>
                        <p class="tree-link">
                            <?php
                            $query_params = parse_url($verse['geographical_link']['link'], PHP_URL_QUERY);
                            parse_str($query_params, $params);
                            ?>
                             <a href="<?php echo esc_url($url . $verse['geographical_link']['link']); ?>">
                                <?php echo esc_html($verse['geographical_link']['title']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($verse["content"]["rendered"])) : ?>
                        <div class="entry-content">
                            <p><?php echo $verse["content"]["rendered"]; ?></p>
                        </div>
                    <?php endif; ?>
					 <?php if (isset($verse["translation_details"])) : ?>
                        <div class="language-content">
                            <?php echo wp_kses_post($verse["translation_details"]); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($verse['taxonomies_list'])) : ?>
                        <div class="entry-meta">
                            <?php if (isset($verse['taxonomies_list']['Language'])) : ?>
                                <p><b><?php esc_html_e('LANGUAGES:', 'tips-find-ferse-within-site'); ?></b>
                                    <?php foreach ($verse['taxonomies_list']['Language'] as $key => $val) : ?>
                                        <?php echo wp_kses_post(strtoupper($key) . "&nbsp;&nbsp;"); ?>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
             <?php if (isset($data['pagination'])) : 
                foreach ($data['pagination'] as $pages) :
                    if (isset($pages["total_pages"])) : 
                         $total_pages = $pages["total_pages"];
                         if ($total_pages > 1) {
                             $image_url = $pages["image_url"];
                             $base = $pages["base"];
                             if (isset($_GET['pages'])) {
                                $current_page = $_GET['pages'];
                             }
                             else{
                                  $current_page = max(1, get_query_var('paged', 1));
                             }
                             $htmlpage = Tips_API_Common::custom_paginate_links($current_page, $total_pages, $url, $image_url,$base);
                             if ($total_pages > 1) {
                                    echo "<div class='pagination'>";
                                        echo wp_kses_post($htmlpage); // Allows safe HTML tags
                                    echo "</div>";
                                }
                         }
                    endif;
                endforeach;
            endif; ?>
        </section>
    <?php else : ?>
        <section class="no-stories-section">
            <div class="entry-content">
                <p><?php esc_html_e('No Sources Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
    <?php endif; ?>
</div>