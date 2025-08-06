<?php

/**
 * Template Name: SourceDetailsPage
 * Description: Source Details will be displayed
 */

if (isset($_GET['sourceId'])) {
    $terms = sanitize_text_field($_GET['sourceId']);
    $terms = sanitize_text_field($_GET['sourceId']);
    $page = (get_query_var('pages')) ? sanitize_text_field(get_query_var('pages')): 1;
    $data = [];
    if($terms) :
        $data = Tips_API_Common::fetch_source_details_from_api($terms,$page);
    endif;   
    $url = site_url();
?>
<div class="entry-content container">
    <?php if (isset($_GET['sourceId']) && is_array($data) && !empty($data)) : ?>
        <section class="book-stories-section">
            <?php if (isset($data["sourceData"]["description"])) : ?>
                <div class="source_desc">
                    <h1 class="source-title">Source: &nbsp <?php echo wp_kses_post( $data['sourceData']['title'] ); ?></h1>
                    <p><?php echo wp_kses_post( $data['sourceData']['description'] ); ?></p>
                </div>
            <?php endif; ?>  
            <?php foreach ($data['storyData'] as $detail) : ?>
                <article class="book-story">
                    <?php if (isset($detail["title"]["rendered"])) : ?>
                        <a href="<?php echo esc_url($detail["title"]["title_link"]); ?>">
                            <h2>
                                <?php
                                echo wp_kses_post(sanitize_text_field($detail['title']['rendered']));
                                echo '<span class="term with-original" data-original="' . esc_attr($detail['title']['hover_title']) . '">';
                                echo esc_html(sanitize_text_field($detail['title']['hover_title']));
                                echo '</span>';
                                ?>
                            </h2>
                        </a>
                    <?php endif; ?>

                    <?php if (isset($detail['geographical_link']['title'])) : ?>
                        <p class="tree-link">
                            <?php
                            $query_params = parse_url($detail['geographical_link']['link'], PHP_URL_QUERY);
                            parse_str($query_params, $params);
                            ?>
                            <a href="<?php echo esc_url($url . $detail['geographical_link']['link']); ?>">
                                <?php echo esc_html($detail['geographical_link']['title']); ?>
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (isset($detail["content"]["rendered"])) : ?>
                        <p><?php echo $detail["content"]["rendered"]; ?></p>
                        <!--<p><?php echo wp_kses_post($detail["content"]["rendered"]); ?></p>-->
                    <?php endif; ?>
					<?php if (isset($detail["translation_details"])) : ?>
                        <div class="language-content">
                            <?php echo wp_kses_post($detail["translation_details"]); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($detail['taxonomies_list'])) : ?>
                        <div class="entry-meta">
                            <?php if (isset($detail['taxonomies_list']['Language'])) : ?>
                                <p><b><?php esc_html_e('LANGUAGES:', 'tips-find-ferse-within-site'); ?></b>
                                    <?php foreach ($detail['taxonomies_list']['Language'] as $key => $val) : ?>
                                        <?php echo wp_kses_post(strtoupper($key)) . "&nbsp;&nbsp;"; ?>
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
        <section class="no-sources-section">
            <div class="entry-content">
                <p><?php esc_html_e('No Sources Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php } 
else{?>
<div class="entry-content">
     <section class="no-sources-section">
            <div class="entry-content">
                 <p><?php esc_html_e('No Sources Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
</div>
<?php }
?>
