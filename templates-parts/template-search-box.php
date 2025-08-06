<?php
/**
 * Template Name: Chapters Template
 * Description: ALl chapters of specific book will be displayed
 */

global $template_data;
$book_chapter_numbers = $template_data['api_response'];
$book_code = $template_data['book_code'];

?>

<div class="container test">
    <?php if (is_wp_error($book_chapter_numbers)) { ?>
        <section class="error-section">
            <div class='error'>
                <p><?php echo esc_html( $book_chapter_numbers->get_error_message() ); ?></p>
            </div>
        </section>
    <?php } elseif (empty($book_chapter_numbers['chapter_numbers'])) {
    ?>
        <section class="error-section">
            <div class='error'>
                <p><?php esc_html_e('No Chapters Found', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
    <?php
    } elseif (is_array($book_chapter_numbers) && !empty($book_chapter_numbers)) { ?>       
                    <ul id="chapters-list-ul" class="chapters chapters-Gen">
                    <?php foreach ($book_chapter_numbers['chapter_numbers'] as $index => $chapter_no) { ?>
                        <li id="chapter-<?php echo esc_attr( $chapter_no ); ?>" data-book="<?php echo esc_attr( $book_code ); ?>" class="chapter" data-id="<?php echo esc_attr( $chapter_no ); ?>">
                            <?php echo wp_kses_post( "<a class='chapter-slide' href='#' data-id='" . esc_attr( $chapter_no ) . "'>" . sanitize_text_field( $chapter_no ) . "</a>" ); ?>
                        </li>
                    <?php }; ?>
                    </ul>
                
    <?php } else { ?>
        <section class="no-chapters-section">
            <div class="entry-content">
                <p><?php esc_html_e('No Chapters Found.', 'tips-find-ferse-within-site'); ?></p>
            </div>
        </section>
    <?php } ?>
</div>