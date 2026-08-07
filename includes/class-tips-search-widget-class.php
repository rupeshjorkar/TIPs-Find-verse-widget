<?php
class Find_Verse_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'find_verse_widget', 
            'Find Verse Widget', 
            array( 'description' => __( 'Finding a verse widget', 'tips-find-ferse-within-site' ) ) 
        );
    }

    public function widget( $args, $instance ) {
        echo esc_html($args['before_widget']);
        echo '<p>Finding a verse widget</p>';  // Custom message
        echo do_shortcode('[tips_find_verse]'); 
        echo esc_html($args['after_widget']);
    }

    public function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
}

function find_verse_widget_init() {
    register_widget( 'Find_Verse_Widget' );
}
add_action( 'widgets_init', 'find_verse_widget_init' );
