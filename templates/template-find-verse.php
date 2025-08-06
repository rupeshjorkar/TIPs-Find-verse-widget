<?php
/**
 * Template Name: Find Verse
 * Description: Tree View will be displayed
 */

// echo '<div class="find_verse_section">'.do_shortcode('[tips_find_verse]').'</div>';


if ( isset( $_GET['verseId'] ) ) {
    $search = sanitize_text_field( wp_unslash( $_GET['verseId'] ) ); 
    if ( $search ) {
        $data = Tips_API_Common::find_verse_from_api( $search );
    }
}
if (isset($data) && is_wp_error($data)) {
    echo 'An error occurred: ' . esc_html($data->get_error_message());
    return; 
}
if ( ! empty( $data ) && isset( $data[0]['term_slug'] ) ) {
    $term_slug = sanitize_text_field( $data[0]['term_slug'] );
    $term_link = get_term_link( $term_slug, 'tip_verse' ); 

    $page_slug = 'tip_verse';
    $page = get_page_by_path( $page_slug );

    if ( $page instanceof WP_Post ) {
        $page_link = esc_url( add_query_arg( 'verseId', $term_slug, get_permalink( $page->ID ) ) ); 
            ob_start();
            wp_redirect( $page_link );
            exit();
            ob_end_clean();
    }
} else {
    if ( isset( $data ) && is_array( $data ) ) { 
        $page_slug = 'tip_verse';
        $page = get_page_by_path( $page_slug );

        if ( $page instanceof WP_Post ) {
            $page_link = esc_url( get_permalink( $page->ID ) ); 
        } else {
            $page_link = '';
        }
        echo '<div class="container book-container">';
        foreach ( $data as $chapter ) {
            if ( isset( $chapter['chapter_reference'], $chapter['verse_slug'] ) && is_array( $chapter['verse_slug'] ) ) {
                $chapter_reference = esc_html( $chapter['chapter_reference'] ); 
                $verse_slugs = $chapter['verse_slug'];

                echo '<div class="book" id="find_verse">';
                foreach ( $verse_slugs as $verse => $slug ) {
                    $verse = esc_html( $verse ); 
                    $slug = sanitize_text_field( $slug ); 
                    if ( $page_link ) {
                        echo '<div id="' . esc_attr( $slug ) . '" class="verses">';
                        echo '<a href="' . esc_url( add_query_arg( 'verseId', $slug, $page_link ) ) . '">' . esc_html( $verse ) . '</a>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            if ( isset( $chapter['error'] ) ) {
                $error_message = esc_html( $chapter['error'] ); 
                echo '<div class="find_ver_error">' . esc_html( $error_message ) . '</div>';
            }
        }
        echo '</div>';
    }
}
?>
