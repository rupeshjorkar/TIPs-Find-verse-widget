<?php
/**
 * Template Name: VerseStoryDetailsPage
 * Description: Verse Story Details will be displayed
 */
$terms = $_SERVER['QUERY_STRING'];
$data = [];
if($terms) :
    $data = Tips_API_Common::fetch_verse_story_details_from_api($terms);
endif;    
$url = site_url();
?>
<div class="container">
    <?php if (is_wp_error($data)) : ?>
        <section class="error-section">
            <div class='error'>
                <p><?php echo esc_html($data->get_error_message()); ?></p>
            </div>
        </section>
    <?php elseif (is_array($data) && !empty($data)) : ?>
        <section class="book-stories-section story_detail">
            <?php foreach ($data as $verse) : ?>
                <article class="book-story">
                    <?php if (isset($verse['slug'])) : ?>
                        <?php
                        if (isset($verse["title"]["rendered"])) : ?>
                            
                                <h1>
                                    <?php
                                    echo wp_kses_post(sanitize_text_field($verse['title']['rendered']));
                                    echo '<span class="term with-original" data-original="' . esc_attr($verse['title']['hover_title']) . '">';
                                    $text = str_replace(', ', ',&nbsp;', $verse['title']['hover_title']);
                                    echo esc_html(sanitize_text_field($text));
                                    echo '</span>';
                                    ?>
                                </h1>
                           
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
                                        <?php echo wp_kses_post(mb_convert_case($key, MB_CASE_TITLE, "UTF-8") . "&nbsp;&nbsp;"); ?>

                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                            <?php if (isset($verse['taxonomies_list']['Verse'])) : ?>
                                <p><b><?php esc_html_e('VERSES:', 'tips-find-ferse-within-site'); ?></b>
                                    <?php foreach ($verse['taxonomies_list']['Verse'] as $key => $val) : ?>
                                        <?php echo "<a href='" . esc_url( $val ) . "'>" . esc_html( $key ) . "</a>"; ?>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                            <?php if (isset($verse['taxonomies_list']['Source'])) : ?>
                                <p><b><?php esc_html_e('SOURCES:', 'tips-find-ferse-within-site'); ?>
                                <?php
                                    $sources = $verse['taxonomies_list']['Source'];
                                    $last_index = array_key_last($sources);
                                    foreach ($sources as $key => $val) {
                                        echo "<a href='" . esc_url($val) . "'>" . esc_html($key) . "</a>";
                                        if ($key !== $last_index) {
                                            echo ' : ';
                                        }
                                    }
                                ?>
                                </p>
                            <?php endif; ?>
                            
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
                     
            <?php
            if ( ! empty( $data[0]['prev_story'] ) ) :
                ?>
                <div class="prev-page">
                    <span aria-hidden="true" class="nav-subtitle">
                        <?php echo esc_html__( 'Previous', 'tips-find-ferse-within-site' ); ?>
                    </span>
                    <a href="<?php echo esc_url( $data[0]['prev_story']['link'] ); ?>">
                        <img src="<?php echo esc_url( $data[0]['prev_story']['image'] ); ?>" alt="">
                        <?php echo esc_html( $data[0]['prev_story']['title'] ); ?>
                    </a>
                </div>
            <?php endif; ?>
             <?php
            if ( ! empty( $data[0]['next_story'] ) ) :
                ?>
                <div class="next-page">
                    <span aria-hidden="true" class="nav-subtitle">
                        <?php echo esc_html__( 'Next', 'tips-find-ferse-within-site' ); ?>
                    </span>
                    <a href="<?php echo esc_url( $data[0]['next_story']['link'] ); ?>">
                        <?php echo esc_html( $data[0]['next_story']['title'] ); ?>
                        <img src="<?php echo esc_url( $data[0]['next_story']['image'] ); ?>" alt="">
                    </a>
                </div>
            <?php endif; ?>
        </section>
    <?php else : ?>
        <section class="no-stories-section">
            <div class="entry-content">
                <p><?php esc_html_e('No Sources Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
    <?php endif; ?>
</div>