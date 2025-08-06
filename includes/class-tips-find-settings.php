<?php

class TipsFindSettings {
    function __construct() {
        $this->init();
    }

    public function init() {
        add_action('admin_menu', array($this, 'add_tips_find_verse_menu'));
        
        add_action('admin_enqueue_scripts', array($this, 'enqueue_plugin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_color_picker'));

    }
    public function enqueue_plugin_styles($hook_suffix) {        
        if ( $hook_suffix === 'toplevel_page_tips-find-ferse-within-site' || $hook_suffix === 'tips-configuration_page_tips-find-ferse-within-site-instruction') {
            wp_enqueue_style('tips-find-ferse-within-site-css', plugin_dir_url(__FILE__) . '../assets/css/tips-find-ferse-within-site.css');
        }
    }
    public function enqueue_color_picker($hook_suffix) {
        if ($hook_suffix === 'toplevel_page_tips-find-ferse-within-site') {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('tips-find-ferse-within-site-script', plugin_dir_url(__FILE__) . '../assets/js/tips-find-ferse-within-site.js', array('wp-color-picker'), false, true);
        }
    }

    public function add_tips_find_verse_menu() {
        // Add the main menu.
        add_menu_page(
            __('TIPs Configuration', 'tips-find-ferse-within-site'),    
            __('TIPs Configuration', 'tips-find-ferse-within-site'),    
            'manage_options',                              
            'tips-find-ferse-within-site',                             
            array($this, 'tips_configuration_page'),       
            'dashicons-admin-tools',                       
            25                                             
        );
        add_submenu_page(
            'tips-find-ferse-within-site',                             
            __('Instruction', 'tips-find-ferse-within-site'),          
            __('Instruction', 'tips-find-ferse-within-site'),          
            'manage_options',                              
            'tips-find-ferse-within-site-instruction',                 
            array($this, 'tips_instruction_page')          
        );
    }
    public function tips_configuration_page() {
        $color = get_option('tips_find_verse_color', '#31bbd8'); // Default color
        $button_text = get_option('tips_find_verse_button_text', 'Find verse'); // Default button text
        $place_order = get_option('tips_find_verse_place_order', 'Tips: Find verse'); // Default empty value
        
        $color2 = get_option('tips_find_sec_verse_color', '#31bbd8'); // Default color
        $button_text2 = get_option('tips_find_sec_verse_button_text', 'Search text...'); // Default button text
        $place_order2 = get_option('tips_find_sec_verse_place_order', 'Search'); // Default empty value

        $color3 = get_option('tips_find_thir_verse_color', '#31bbd8'); // Default color
        $button_text3 = get_option('tips_find_thir_verse_button_text', 'Pick category'); // Default button text
        $place_order3 = get_option('tips_find_thir_verse_place_order', 'Select'); // Default empty value
        
        $enable_first_search = get_option('tips_enable_first_search', 'off'); // Default value is 'off'
        $enable_secound_search = get_option('tips_enable_secound_search', 'off'); // Default value is 'off'
        $enable_third_search   = get_option('tips_enable_third_search', 'off');
        
        $tips_enable_greek_translation   = get_option('tips_enable_greek_translation', 'off');
        $tips_enable_english_translation   = get_option('tips_enable_english_translation', 'off');

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('TIPs Configuration', 'tips-find-ferse-within-site'); ?></h1>
            <?php if ( isset($_GET['settings-updated']) && sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) : ?>
                <div id="message" class="updated notice is-dismissible">
                    <?php esc_html_e('Settings saved successfully.', 'tips-find-ferse-within-site'); ?>
                </div>
            <?php endif; ?>
            <form method="post" action="options.php">
                <?php 
                    settings_fields('tips-find-ferse-within-site-settings');
                    do_settings_sections('tips-find-ferse-within-site-settings'); 
                ?>

                <table class="form-table">
                     <!-- Greek Translation-->
                    <tr class="heading_tr"><th>Enable/Disable Translation Options, Search Features, and Category Filters</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Verse Greek Translation', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_greek_translation" class="onoffswitch-checkbox" id="switch-greek" value="on" <?php checked($tips_enable_greek_translation, 'on'); ?>>
                                <label class="onoffswitch-label" for="switch-greek">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <!-- English  Translation-->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Verse English Translation', 'tips-find-ferse-within-site'); ?>   
                        </th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_english_translation" class="onoffswitch-checkbox" id="switch-english" value="on" <?php checked($tips_enable_english_translation, 'on'); ?>>
                                <label class="onoffswitch-label" for="switch-english">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                     <!-- First -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Find Verse Search Box', 'tips-find-ferse-within-site'); ?> 
                        </th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_first_search" class="onoffswitch-checkbox" id="switch-first" value="on" <?php checked($enable_first_search, 'on'); ?>>
                                <label class="onoffswitch-label" for="switch-first">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <!-- Second -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Story Search Box', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_secound_search" class="onoffswitch-checkbox" id="switch-second" value="on" <?php checked($enable_secound_search, 'on'); ?>>
                                <label class="onoffswitch-label" for="switch-second">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- Third -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Category Search Box', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_third_search" class="onoffswitch-checkbox" id="switch-third" value="on" <?php checked($enable_third_search, 'on'); ?>>
                                <label class="onoffswitch-label" for="switch-third">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php if ($enable_first_search === 'on') : ?>
                    <!-- Color Picker -->
                    <tr class="heading_tr"><th>Find Verse Search Box Settings</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Button Background Color', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_verse_color" value="<?php echo esc_attr($color); ?>" class="tips-color-picker" data-default-color="#31bbd8"/>
                        </td>
                    </tr>
                    <!-- Button Text -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Button Text', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_verse_button_text" value="<?php echo esc_attr($button_text); ?>" />
                        </td>
                    </tr>
                    <!-- Place Order -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Search Placeholder Text', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_verse_place_order" value="<?php echo esc_attr($place_order); ?>" />
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($enable_secound_search === 'on') : ?>
                    <tr class="heading_tr"><th>Search Box Settings</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Button Background Color', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_sec_verse_color" value="<?php echo esc_attr($color2); ?>" class="tips-color-picker" data-default-color="#31bbd8"/>
                        </td>
                    </tr>
                    <!-- Button Text -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Button Text', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_sec_verse_button_text" value="<?php echo esc_attr($button_text2); ?>" />
                        </td>
                    </tr>
                    <!-- Place Order -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Search Placeholder Text', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_sec_verse_place_order" value="<?php echo esc_attr($place_order2); ?>" />
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($enable_third_search === 'on') : ?>
                    <tr class="heading_tr"><th>Category Search Box Settings</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Button Background Color', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_thir_verse_color" value="<?php echo esc_attr($color3); ?>" class="tips-color-picker" data-default-color="#31bbd8"/>
                        </td>
                    </tr>
                    <!-- Button Text -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Button Text', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_thir_verse_button_text" value="<?php echo esc_attr($button_text3); ?>" />
                        </td>
                    </tr>
                    <!-- Place Order -->
                    <tr valign="top">
                        <th scope="row" class="inner_option">
                            <?php esc_html_e('Search Placeholder Text', 'tips-find-ferse-within-site'); ?>
                        </th>
                        <td>
                            <input type="text" name="tips_find_thir_verse_place_order" value="<?php echo esc_attr($place_order3); ?>" />
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    public function tips_instruction_page() {
        ?>
       <div class="wrap tips-instruction-page">
            <h1><?php esc_html_e('TIPs: Find the Verse Widget plugin instructions for displaying TIPs data on the customer site.', 'tips-find-ferse-within-site'); ?></h1>

            <h2><?php esc_html_e('How to Install the Plugin?', 'tips-find-ferse-within-site'); ?></h2>
            <ol>
                <li><?php esc_html_e('Download the plugin ZIP file.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('Log in to your WordPress admin dashboard.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('Navigate to Plugins > Add New > Upload Plugin.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('Click on "Choose File" and select the downloaded ZIP file.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('Click on "Install Now" and then "Activate Plugin" once the installation is complete.', 'tips-find-ferse-within-site'); ?></li>
            </ol>

            <h2><?php esc_html_e('How to Change the Configurations?', 'tips-find-ferse-within-site'); ?></h2>
            <ol>
                <li><?php esc_html_e('Go to the "TIPs Configuration" menu in the WordPress admin dashboard.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('On the configuration page, you can change settings such as Enable/Disable Translation Options, Search Features, and Category Filters.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('On the configuration page, you can enable the Find Verse feature, search box, and category search box. You can also change settings such as the button background color, button text, and placeholder text.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('Make the desired changes in the input fields.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('Click on "Save Changes" to update the settings.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('You will see a success message confirming the settings were saved.', 'tips-find-ferse-within-site'); ?></li>
            </ol>

            <h2><?php esc_html_e('How to Use the Plugin?', 'tips-find-ferse-within-site'); ?></h2>
            <ol>
                <li><?php esc_html_e('Use the shortcode [tips_find_verse] to display the search widget.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('You can place this shortcode in any post, page, or widget area.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('To add the shortcode to a post or page, simply paste it into the content editor.', 'tips-find-ferse-within-site'); ?></li>
                <li><?php esc_html_e('To add it to a widget area, go to Appearance > Widgets and add a Text widget, then paste the shortcode inside.', 'tips-find-ferse-within-site'); ?></li>
            </ol>

            <h2><?php esc_html_e('Support', 'tips-find-ferse-within-site'); ?></h2>
            <p><?php esc_html_e('If you have any questions or need support, please email us at ', 'tips-find-ferse-within-site'); ?> 
                <a href="mailto:jzetzsche@biblesocieties.org">jzetzsche@biblesocieties.org</a>.
            </p>

            <!-- Button to download the PDF -->
            <a href="https://demo-tips.translation.bible/PDF/Tips_Find_Verse_Customer_Plugin_Instructions.pdf" class="button" target="_blank">
                <?php esc_html_e('Download PDF for More Information', 'tips-find-ferse-within-site'); ?>
            </a>
        </div>
        <?php
    }
}
// Register the settings.
add_action('admin_init', function() {
    $enable_first_search   = get_option('tips_enable_first_search', 'off');
    $enable_secound_search = get_option('tips_enable_secound_search', 'off');
    $enable_third_search   = get_option('tips_enable_third_search', 'off');

    if ($enable_first_search === 'on') {
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_verse_color', 'sanitize_hex_color');
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_verse_button_text', 'sanitize_text_field');
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_verse_place_order', 'sanitize_text_field');
    }
    if ($enable_secound_search === 'on') {
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_sec_verse_color', 'sanitize_hex_color');
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_sec_verse_button_text', 'sanitize_text_field');
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_sec_verse_place_order', 'sanitize_text_field');
    }
    if ($enable_third_search === 'on') {
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_thir_verse_color', 'sanitize_hex_color');
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_thir_verse_button_text', 'sanitize_text_field');
        register_setting('tips-find-ferse-within-site-settings', 'tips_find_thir_verse_place_order', 'sanitize_text_field');
    }

    register_setting('tips-find-ferse-within-site-settings', 'tips_enable_first_search', 'sanitize_text_field');
    register_setting('tips-find-ferse-within-site-settings', 'tips_enable_secound_search', 'sanitize_text_field');
    register_setting('tips-find-ferse-within-site-settings', 'tips_enable_third_search', 'sanitize_text_field');
    register_setting('tips-find-ferse-within-site-settings', 'tips_enable_greek_translation', 'sanitize_text_field');
    register_setting('tips-find-ferse-within-site-settings', 'tips_enable_english_translation', 'sanitize_text_field');
});


new TipsFindSettings();