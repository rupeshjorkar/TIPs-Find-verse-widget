<?php
/**
 * Template Name: Find Verse
 * Description: Tree View will be displayed
 */

// echo '<div class="find_verse_section">'.do_shortcode('[tips_find_verse]').'</div>';


if ( isset( $_GET['Id'] ) ) {
    $search = sanitize_text_field( wp_unslash( $_GET['Id'] ) ); 
    $page = (get_query_var('pages')) ? sanitize_text_field(get_query_var('pages')): 1;

    if ( $search ) {
        $data = Tips_API_Common::fetch_search_stories_from_api($search,$page);
        $url = site_url();
    }
}
?>
<div class="container entry-content">
    <?php if (isset($_GET['Id']) && is_array($data) && !empty($data)) : ?>
        <section class="book-stories-section">
            <?php foreach ($data['storyData'] as $verse) : ?>
                <article class="book-story">
                    <?php if (isset($verse['slug'])) : ?>
                        <?php
                         if (isset($verse["title"]["rendered"])) : ?>
                            <a href="<?php echo esc_url($verse["title"]["title_link"]); ?>" alt="">
                                <h2 class="sss">
                                    <?php
                                    echo esc_html( sanitize_text_field( $verse['title']['rendered'] ) );
                                    echo '<span class="term with-original" data-original="' . esc_attr($verse['title']['hover_title']) . '">';
                                    echo esc_html( sanitize_text_field( $verse['title']['hover_title'] ) );
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
                            <a href="<?php echo esc_url( $url . $verse['geographical_link']['link'] ); ?>">
                                <?php echo esc_html( $verse['geographical_link']['title'] ); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($verse["content"]["rendered"])) : ?>
                        <div class="entry-content">
                            <p><?php echo $verse["content"]["rendered"]; ?></p>
                            <!--<p><?php echo wp_kses_post($verse["content"]["rendered"]); ?></p>-->
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
                                        echo wp_kses_post($htmlpage);
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
                <p><?php esc_html_e('No Stories Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
    <?php endif; ?>
</div>
