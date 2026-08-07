<?php

class TipsFindSettings {

    function __construct() {
        $this->init();
    }

    public function init() {
        add_action( 'admin_menu',            array( $this, 'add_tips_find_verse_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_plugin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
        add_action( 'admin_post_tips_save_activation',   array( $this, 'handle_activation_submit' ) );
        add_action( 'admin_post_tips_deactivate_plugin', array( $this, 'handle_deactivation' ) );
    }

    // =========================================================================
    // Activation / Validation
    // =========================================================================

    /**
     * Calls Tips_API_Common::fetch_validate_activation_from_api() then verifies
     * nonce, HMAC signature, and expiry locally.
     *
     * Returns full $body array on success, false on failure.
     * (Changed from bool so handle_activation_submit() can access api_secret)
     *
     * @param string $activation_key
     * @return array|false
     */
    private function validate_with_api( $activation_key ) {

        // ── Delegate HTTP fetch to shared class ───────────────────────────
        $body = Tips_API_Common::fetch_validate_activation_from_api( $activation_key );

        // ── Guard: WP_Error means HTTP request failed ─────────────────────
        if ( is_wp_error( $body ) ) {
            error_log( 'TIPs activation error: ' . $body->get_error_message() );
            return false;
        }

        // ── Guard: unexpected non-array response ──────────────────────────
        if ( ! is_array( $body ) ) {
            error_log( 'TIPs activation error: unexpected response type - ' . gettype( $body ) );
            return false;
        }

        if ( empty( $body['valid'] ) ) {
            return false;
        }

        // ── Verify nonce echoed back matches what we sent ─────────────────
        if ( empty( $body['nonce'] ) || empty( $body['_sent_nonce'] ) ) {
            return false;
        }
        if ( $body['nonce'] !== $body['_sent_nonce'] ) {
            return false;
        }

        // ── Verify HMAC signature ─────────────────────────────────────────
        if ( empty( $body['signature'] ) || empty( $body['api_secret'] ) ) {
            return false;
        }

        $payload_to_verify = [
            'valid'      => $body['valid'],
            'site_url'   => $body['site_url'],
            'nonce'      => $body['nonce'],
            'expires_at' => $body['expires_at'],
        ];

        // Use api_secret from response to verify — it hasn't been saved yet on first activation
        $expected_signature = hash_hmac(
            'sha256',
            wp_json_encode( $payload_to_verify ),
            $body['api_secret']
        );

        if ( ! hash_equals( $expected_signature, $body['signature'] ) ) {
            return false;
        }

        // ── Check expires_at not past ─────────────────────────────────────
        if ( empty( $body['expires_at'] ) || time() > (int) $body['expires_at'] ) {
            return false;
        }

        // Return full body so caller can save api_secret
        return $body;
    }

    /**
     * get_api_secret() reads from wp_options (saved after first activation).
     * Used for any future re-verification flows.
     */
    private function get_api_secret() {
        return $this->decrypt( get_option( 'tips_api_secret', '' ) );
    }

    /**
     * True when a previously validated key is stored and fingerprint matches.
     * No static credentials — fingerprint ties key to this site's home_url().
     */
    private function is_activated() {
        $stored_key         = $this->decrypt( get_option( 'tips_activation_key', '' ) );
        $stored_fingerprint = get_option( 'tips_activation_fingerprint', '' );

        if ( empty( $stored_key ) || empty( $stored_fingerprint ) ) {
            return false;
        }

        return hash_equals( $this->generate_fingerprint( $stored_key ), $stored_fingerprint );
    }

    /**
     * Fingerprint ties the stored key to this specific site.
     * Changing home_url() or AUTH_KEY invalidates it automatically.
     */
    private function generate_fingerprint( $activation_key ) {
        return hash_hmac( 'sha256', $activation_key . home_url(), AUTH_KEY );
    }

    // ── Handle activation form POST ──────────────────────────────────────────

    public function handle_activation_submit() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'tips-find-ferse-within-site' ) );
        }
    
        check_admin_referer( 'tips_activation_nonce' );
    
        $key = sanitize_text_field( wp_unslash( $_POST['tips_activation_key'] ?? '' ) );
    
        if ( empty( $key ) ) {
            wp_redirect( add_query_arg(
                [ 'page' => 'tips-find-ferse-within-site', 'activation' => 'invalid' ],
                admin_url( 'admin.php' )
            ) );
            exit;
        }
    
        $result = $this->validate_with_api( $key );
    
        if ( $result !== false ) {
            update_option( 'tips_activation_key', $this->encrypt( $key ) );
            update_option( 'tips_api_secret',     $this->encrypt( $result['api_secret'] ) );
            update_option( 'tips_activation_fingerprint', $this->generate_fingerprint( $key ) );
            update_option( 'tips_is_activated', 'yes' );
    
            // ── NEW: clear any stale token then fetch a fresh one ─────────────
            Tips_Session_Manager::clear_token();
            Tips_Session_Manager::get_token(); // fetches + caches for 23h
    
            wp_redirect( add_query_arg(
                [ 'page' => 'tips-find-ferse-within-site', 'activation' => 'success' ],
                admin_url( 'admin.php' )
            ) );
        } else {
            wp_redirect( add_query_arg(
                [ 'page' => 'tips-find-ferse-within-site', 'activation' => 'invalid' ],
                admin_url( 'admin.php' )
            ) );
        }
        exit;
    }

    // ── Handle deactivation link ─────────────────────────────────────────────

    public function handle_deactivation() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'tips-find-ferse-within-site' ) );
        }
    
        check_admin_referer( 'tips_deactivate_nonce' );
    
        delete_option( 'tips_activation_key' );
        delete_option( 'tips_activation_fingerprint' );
        delete_option( 'tips_is_activated' );
        delete_option( 'tips_api_secret' );
    
        // ── NEW: clear cached session token ───────────────────────────────────
        Tips_Session_Manager::clear_token();
    
        wp_redirect( add_query_arg(
            [ 'page' => 'tips-find-ferse-within-site' ],
            admin_url( 'admin.php' )
        ) );
        exit;
    }

    // =========================================================================
    // Enqueue
    // =========================================================================

    public function enqueue_plugin_styles( $hook_suffix ) {
        if (
            $hook_suffix === 'toplevel_page_tips-find-ferse-within-site' ||
            $hook_suffix === 'tips-configuration_page_tips-find-ferse-within-site-instruction'
        ) {
            wp_enqueue_style(
                'tips-find-ferse-within-site-css',
                plugin_dir_url( __FILE__ ) . '../assets/css/tips-find-ferse-within-site.css'
            );
        }
    }

    public function enqueue_color_picker( $hook_suffix ) {
        if ( $hook_suffix === 'toplevel_page_tips-find-ferse-within-site' && $this->is_activated() ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script(
                'tips-find-ferse-within-site-script',
                plugin_dir_url( __FILE__ ) . '../assets/js/tips-find-ferse-within-site.js',
                array( 'wp-color-picker' ),
                false,
                true
            );
        }
    }

    // =========================================================================
    // Menu
    // =========================================================================

    public function add_tips_find_verse_menu() {
        add_menu_page(
            __( 'TIPs Configuration', 'tips-find-ferse-within-site' ),
            __( 'TIPs Configuration', 'tips-find-ferse-within-site' ),
            'manage_options',
            'tips-find-ferse-within-site',
            array( $this, 'tips_configuration_page' ),
            'dashicons-admin-tools',
            25
        );
        add_submenu_page(
            'tips-find-ferse-within-site',
            __( 'Instruction', 'tips-find-ferse-within-site' ),
            __( 'Instruction', 'tips-find-ferse-within-site' ),
            'manage_options',
            'tips-find-ferse-within-site-instruction',
            array( $this, 'tips_instruction_page' )
        );
    }

    // =========================================================================
    // Router — configuration page
    // =========================================================================

    public function tips_configuration_page() {
        if ( ! $this->is_activated() ) {
            $this->render_activation_form();
        } else {
            $this->render_settings_form();
        }
    }

    // =========================================================================
    // STEP 1 — Activation form
    // =========================================================================

    private function render_activation_form() {
        $status    = isset( $_GET['activation'] ) ? sanitize_text_field( wp_unslash( $_GET['activation'] ) ) : '';
        $saved_key = esc_attr( $this->decrypt( get_option( 'tips_activation_key', '' ) ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'TIPs Configuration', 'tips-find-ferse-within-site' ); ?></h1>

            <?php if ( $status === 'invalid' ) : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php esc_html_e( 'Activation failed. The key you entered is invalid or not approved for this domain. Please check your credentials and try again.', 'tips-find-ferse-within-site' ); ?></p>
                </div>
            <?php endif; ?>

            <div style="max-width:500px; background:#fff; padding:28px 32px; margin-top:20px; border:1px solid #c3c4c7; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.08);">

                <h2 style="margin-top:0; padding-bottom:12px; border-bottom:1px solid #eee;">
                    <?php esc_html_e( 'Activate Plugin', 'tips-find-ferse-within-site' ); ?>
                </h2>
                <p style="color:#646970; margin-bottom:24px;">
                    <?php esc_html_e( 'Enter your activation key to unlock the plugin settings. Your site URL will be detected automatically.', 'tips-find-ferse-within-site' ); ?>
                </p>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'tips_activation_nonce' ); ?>
                    <input type="hidden" name="action" value="tips_save_activation">

                    <table class="form-table" style="margin-top:0;">
                        <tr>
                            <th scope="row" style="padding-left:0;">
                                <label for="tips_activation_key">
                                    <?php esc_html_e( 'Activation Key', 'tips-find-ferse-within-site' ); ?>
                                </label>
                            </th>
                            <td style="padding-left:0;">
                                <input
                                    type="text"
                                    id="tips_activation_key"
                                    name="tips_activation_key"
                                    class="regular-text"
                                    value="<?php echo $saved_key; ?>"
                                    placeholder="XXX-XXXX-XXXX-XXXX"
                                    required
                                    style="width:100%;"
                                />
                                <p class="description">
                                    <?php
                                    printf(
                                        esc_html__( 'Your site URL (%s) will be sent automatically.', 'tips-find-ferse-within-site' ),
                                        '<strong>' . esc_html( home_url() ) . '</strong>'
                                    );
                                    ?>
                                </p>
                            </td>
                        </tr>
                    </table>

                    <p style="margin-top:8px;">
                        <button type="submit" class="button button-primary button-large">
                            <?php esc_html_e( 'Activate Plugin', 'tips-find-ferse-within-site' ); ?>
                        </button>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    // =========================================================================
    // STEP 2 — Settings form (only shown after activation)
    // =========================================================================

    private function render_settings_form() {
        $color        = get_option( 'tips_find_verse_color',        '#31bbd8' );
        $button_text  = get_option( 'tips_find_verse_button_text',  'Find verse' );
        $place_order  = get_option( 'tips_find_verse_place_order',  'Tips: Find verse' );

        $color2       = get_option( 'tips_find_sec_verse_color',        '#31bbd8' );
        $button_text2 = get_option( 'tips_find_sec_verse_button_text',  'Search text...' );
        $place_order2 = get_option( 'tips_find_sec_verse_place_order',  'Search' );

        $color3       = get_option( 'tips_find_thir_verse_color',        '#31bbd8' );
        $button_text3 = get_option( 'tips_find_thir_verse_button_text',  'Pick category' );
        $place_order3 = get_option( 'tips_find_thir_verse_place_order',  'Select' );

        $enable_first_search   = get_option( 'tips_enable_first_search',   'off' );
        $enable_secound_search = get_option( 'tips_enable_secound_search', 'off' );
        $enable_third_search   = get_option( 'tips_enable_third_search',   'off' );
        $enable_ajax           = get_option( 'tips_enable_ajax',           'off' );

        $tips_enable_greek_translation   = get_option( 'tips_enable_greek_translation',   'off' );
        $tips_enable_english_translation = get_option( 'tips_enable_english_translation', 'off' );

        $activated_url = home_url();

        $deactivate_url = wp_nonce_url(
            add_query_arg(
                [ 'page' => 'tips-find-ferse-within-site' ],
                admin_url( 'admin-post.php' )
            ) . '&action=tips_deactivate_plugin',
            'tips_deactivate_nonce'
        );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'TIPs Configuration', 'tips-find-ferse-within-site' ); ?></h1>

            <?php if ( isset( $_GET['settings-updated'] ) && sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) : ?>
                <div id="message" class="updated notice is-dismissible">
                    <p><?php esc_html_e( 'Settings saved successfully.', 'tips-find-ferse-within-site' ); ?></p>
                </div>
            <?php endif; ?>

            <?php if ( isset( $_GET['activation'] ) && sanitize_text_field( wp_unslash( $_GET['activation'] ) ) === 'success' ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( 'Plugin activated successfully! You can now configure the settings below.', 'tips-find-ferse-within-site' ); ?></p>
                </div>
            <?php endif; ?>

            <!-- Activated badge + deactivate link -->
            <div style="display:flex; align-items:center; gap:16px; margin:12px 0 20px; padding:10px 16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px; max-width:680px;">
                <span style="color:#166534; font-weight:600;">
                    ✓ <?php esc_html_e( 'Activated', 'tips-find-ferse-within-site' ); ?>
                </span>
                <span style="color:#374151; font-size:13px;">
                    <?php echo esc_html( $activated_url ); ?>
                </span>
                <a href="<?php echo esc_url( $deactivate_url ); ?>"
                   style="margin-left:auto; color:#b32d2e; font-size:12px;"
                   onclick="return confirm('<?php esc_attr_e( 'Deactivate plugin? You will need to re-enter your activation key.', 'tips-find-ferse-within-site' ); ?>');">
                    <?php esc_html_e( 'Deactivate', 'tips-find-ferse-within-site' ); ?>
                </a>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'tips-find-ferse-within-site-settings' );
                do_settings_sections( 'tips-find-ferse-within-site-settings' );
                ?>
                <table class="form-table">

                    <!-- ── Toggle section ─────────────────────────────────── -->
                    <tr class="heading_tr"><th>Enable/Disable Translation Options, Search Features, and Category Filters</th></tr>

                    <!-- Greek Translation -->
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Verse Greek Translation', 'tips-find-ferse-within-site' ); ?></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_greek_translation" class="onoffswitch-checkbox" id="switch-greek" value="on" <?php checked( $tips_enable_greek_translation, 'on' ); ?>>
                                <label class="onoffswitch-label" for="switch-greek">
                                    <span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- English Translation -->
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Verse English Translation', 'tips-find-ferse-within-site' ); ?></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_english_translation" class="onoffswitch-checkbox" id="switch-english" value="on" <?php checked( $tips_enable_english_translation, 'on' ); ?>>
                                <label class="onoffswitch-label" for="switch-english">
                                    <span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- Find Verse Search Box -->
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Find Verse Search Box', 'tips-find-ferse-within-site' ); ?></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_first_search" class="onoffswitch-checkbox" id="switch-first" value="on" <?php checked( $enable_first_search, 'on' ); ?>>
                                <label class="onoffswitch-label" for="switch-first">
                                    <span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- Story Search Box -->
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Story Search Box', 'tips-find-ferse-within-site' ); ?></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_secound_search" class="onoffswitch-checkbox" id="switch-second" value="on" <?php checked( $enable_secound_search, 'on' ); ?>>
                                <label class="onoffswitch-label" for="switch-second">
                                    <span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- Category Search Box -->
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Category Search Box', 'tips-find-ferse-within-site' ); ?></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_third_search" class="onoffswitch-checkbox" id="switch-third" value="on" <?php checked( $enable_third_search, 'on' ); ?>>
                                <label class="onoffswitch-label" for="switch-third">
                                    <span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- AJAX -->
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Display Result With AJAX', 'tips-find-ferse-within-site' ); ?></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="tips_enable_ajax" class="onoffswitch-checkbox" id="ajax" value="on" <?php checked( $enable_ajax, 'on' ); ?>>
                                <label class="onoffswitch-label" for="ajax">
                                    <span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- ── Find Verse Search Box settings ────────────────── -->
                    <?php if ( $enable_first_search === 'on' ) : ?>
                    <tr class="heading_tr"><th>Find Verse Search Box Settings</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Button Background Color', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_verse_color" value="<?php echo esc_attr( $color ); ?>" class="tips-color-picker" data-default-color="#31bbd8"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Button Text', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_verse_button_text" value="<?php echo esc_attr( $button_text ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Search Placeholder Text', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_verse_place_order" value="<?php echo esc_attr( $place_order ); ?>" /></td>
                    </tr>
                    <?php endif; ?>

                    <!-- ── Story Search Box settings ─────────────────────── -->
                    <?php if ( $enable_secound_search === 'on' ) : ?>
                    <tr class="heading_tr"><th>Search Box Settings</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Button Background Color', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_sec_verse_color" value="<?php echo esc_attr( $color2 ); ?>" class="tips-color-picker" data-default-color="#31bbd8"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Button Text', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_sec_verse_button_text" value="<?php echo esc_attr( $button_text2 ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Search Placeholder Text', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_sec_verse_place_order" value="<?php echo esc_attr( $place_order2 ); ?>" /></td>
                    </tr>
                    <?php endif; ?>

                    <!-- ── Category Search Box settings ──────────────────── -->
                    <?php if ( $enable_third_search === 'on' ) : ?>
                    <tr class="heading_tr"><th>Category Search Box Settings</th></tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Button Background Color', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_thir_verse_color" value="<?php echo esc_attr( $color3 ); ?>" class="tips-color-picker" data-default-color="#31bbd8"/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Button Text', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_thir_verse_button_text" value="<?php echo esc_attr( $button_text3 ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="inner_option"><?php esc_html_e( 'Search Placeholder Text', 'tips-find-ferse-within-site' ); ?></th>
                        <td><input type="text" name="tips_find_thir_verse_place_order" value="<?php echo esc_attr( $place_order3 ); ?>" /></td>
                    </tr>
                    <?php endif; ?>

                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    // =========================================================================
    // Instruction page
    // =========================================================================

    public function tips_instruction_page() {
        ?>
        <div class="wrap tips-instruction-page">
            <h1><?php esc_html_e( 'TIPs: Find the Verse Widget plugin instructions for displaying TIPs data on the customer site.', 'tips-find-ferse-within-site' ); ?></h1>

            <h2><?php esc_html_e( 'How to Install the Plugin?', 'tips-find-ferse-within-site' ); ?></h2>
            <ol>
                <li><?php esc_html_e( 'Download the plugin ZIP file.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'Log in to your WordPress admin dashboard.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'Navigate to Plugins > Add New > Upload Plugin.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'Click on "Choose File" and select the downloaded ZIP file.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'Click on "Install Now" and then "Activate Plugin" once the installation is complete.', 'tips-find-ferse-within-site' ); ?></li>
            </ol>

            <h2><?php esc_html_e( 'How to Change the Configurations?', 'tips-find-ferse-within-site' ); ?></h2>
            <ol>
                <li><?php esc_html_e( 'Go to the "TIPs Configuration" menu in the WordPress admin dashboard.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'On the configuration page, you can change settings such as Enable/Disable Translation Options, Search Features, and Category Filters.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'On the configuration page, you can enable the Find Verse feature, search box, and category search box. You can also change settings such as the button background color, button text, and placeholder text.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'Make the desired changes in the input fields.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'Click on "Save Changes" to update the settings.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'You will see a success message confirming the settings were saved.', 'tips-find-ferse-within-site' ); ?></li>
            </ol>

            <h2><?php esc_html_e( 'How to Use the Plugin?', 'tips-find-ferse-within-site' ); ?></h2>
            <ol>
                <li><?php esc_html_e( 'Use the shortcode [tips_find_verse] to display the search widget.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'You can place this shortcode in any post, page, or widget area.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'To add the shortcode to a post or page, simply paste it into the content editor.', 'tips-find-ferse-within-site' ); ?></li>
                <li><?php esc_html_e( 'To add it to a widget area, go to Appearance > Widgets and add a Text widget, then paste the shortcode inside.', 'tips-find-ferse-within-site' ); ?></li>
            </ol>

            <h2><?php esc_html_e( 'Support', 'tips-find-ferse-within-site' ); ?></h2>
            <p>
                <?php esc_html_e( 'If you have any questions or need support, please email us at ', 'tips-find-ferse-within-site' ); ?>
                <a href="mailto:jzetzsche@biblesocieties.org">jzetzsche@biblesocieties.org</a>.
            </p>
            <a href="https://demo-tips.translation.bible/PDF/Tips_Find_Verse_Customer_Plugin_Instructions.pdf" class="button" target="_blank">
                <?php esc_html_e( 'Download PDF for More Information', 'tips-find-ferse-within-site' ); ?>
            </a>
        </div>
        <?php
    }

    // =========================================================================
    // Encryption helpers (AES-256-CBC via AUTH_KEY as secret)
    // =========================================================================

    private function encrypt( $value ) {
        $secret = substr( hash( 'sha256', AUTH_KEY ), 0, 32 );
        $iv     = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'AES-256-CBC' ) );
        $enc    = openssl_encrypt( $value, 'AES-256-CBC', $secret, 0, $iv );
        return base64_encode( $iv . '::' . $enc );
    }

    private function decrypt( $stored ) {
        $secret = substr( hash( 'sha256', AUTH_KEY ), 0, 32 );
        $parts  = explode( '::', base64_decode( $stored ), 2 );
        if ( count( $parts ) !== 2 ) return '';
        return openssl_decrypt( $parts[1], 'AES-256-CBC', $secret, 0, $parts[0] );
    }
}

// =============================================================================
// Register settings — always runs on admin_init (NOT inside debug block)
// =============================================================================
add_action( 'admin_init', function () {

    $enable_first_search   = get_option( 'tips_enable_first_search',   'off' );
    $enable_secound_search = get_option( 'tips_enable_secound_search', 'off' );
    $enable_third_search   = get_option( 'tips_enable_third_search',   'off' );

    if ( $enable_first_search === 'on' ) {
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_verse_color',       'sanitize_hex_color' );
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_verse_button_text', 'sanitize_text_field' );
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_verse_place_order', 'sanitize_text_field' );
    }
    if ( $enable_secound_search === 'on' ) {
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_sec_verse_color',       'sanitize_hex_color' );
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_sec_verse_button_text', 'sanitize_text_field' );
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_sec_verse_place_order', 'sanitize_text_field' );
    }
    if ( $enable_third_search === 'on' ) {
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_thir_verse_color',       'sanitize_hex_color' );
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_thir_verse_button_text', 'sanitize_text_field' );
        register_setting( 'tips-find-ferse-within-site-settings', 'tips_find_thir_verse_place_order', 'sanitize_text_field' );
    }

    register_setting( 'tips-find-ferse-within-site-settings', 'tips_enable_first_search',        'sanitize_text_field' );
    register_setting( 'tips-find-ferse-within-site-settings', 'tips_enable_secound_search',      'sanitize_text_field' );
    register_setting( 'tips-find-ferse-within-site-settings', 'tips_enable_third_search',        'sanitize_text_field' );
    register_setting( 'tips-find-ferse-within-site-settings', 'tips_enable_greek_translation',   'sanitize_text_field' );
    register_setting( 'tips-find-ferse-within-site-settings', 'tips_enable_english_translation', 'sanitize_text_field' );
    register_setting( 'tips-find-ferse-within-site-settings', 'tips_enable_ajax',                'sanitize_text_field' );
} );

new TipsFindSettings();