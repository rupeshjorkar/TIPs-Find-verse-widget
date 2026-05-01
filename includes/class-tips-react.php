<?php

class TFVWS_Main {
    
    private $plugin_url;

    public function __construct() {
        $this->plugin_url = plugin_dir_url(__FILE__);
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('react_app', array($this, 'render_react_app'));
    }
    
    public function enqueue_scripts() {
        global $post;

        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'react_app')) {
            $this->load_prod_assets();
        }
    }

    private function load_prod_assets() {
        // Production CSS
        wp_enqueue_style(
            'react-app-css',
            $this->plugin_url . 'react/dist/assets/index.css',
            array(),
            '0.0.3'
        );
        
        // Production JS
        wp_enqueue_script(
            'react-app-js',
            $this->plugin_url . 'react/dist/assets/index.js',
            array(),
            '0.0.3',
            true
        );
    }
    
    public function render_react_app($atts) {
        $atts = shortcode_atts(['id' => 'react-root'], $atts);
        return '<div id="' . esc_attr($atts['id']) . '"></div>';
    }
}

new TFVWS_Main();
