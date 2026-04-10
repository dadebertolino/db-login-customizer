<?php
/**
 * Plugin Name: DB Login Customizer
 * Plugin URI: https://github.com/dadebertolino/db-login-customizer
 * Description: Personalizza la pagina di login e logout di WordPress con i colori e il logo del tuo sito. Niente fronzoli, niente dipendenze.
 * Version: 1.0.0
 * Author: Davide Bertolino
 * Author URI: https://www.davidebertolino.it
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: db-login-customizer
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

define('DBLC_VERSION', '1.0.0');
define('DBLC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DBLC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DBLC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class — singleton
 */
final class DB_Login_Customizer {

    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Admin settings page
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);

        // Login page hooks
        add_action('login_enqueue_scripts', [$this, 'login_styles']);
        add_filter('login_headerurl', [$this, 'login_logo_url']);
        add_filter('login_headertext', [$this, 'login_logo_title']);
        add_action('login_footer', [$this, 'login_footer_scripts']);

        // Redirect hooks
        add_filter('login_redirect', [$this, 'custom_login_redirect'], 10, 3);
        add_filter('logout_redirect', [$this, 'custom_logout_redirect'], 10, 3);

        // Login page custom messages
        add_filter('login_message', [$this, 'custom_login_message']);

        // Settings link in plugins list
        add_filter('plugin_action_links_' . DBLC_PLUGIN_BASENAME, [$this, 'settings_link']);

        // AJAX preview
        add_action('wp_ajax_dblc_preview_data', [$this, 'ajax_preview_data']);
    }

    /**
     * Default options
     */
    public function get_defaults() {
        return [
            'dblc_logo_id'           => 0,
            'dblc_logo_width'        => 200,
            'dblc_bg_color'          => '#f0f0f1',
            'dblc_bg_image_id'       => 0,
            'dblc_bg_size'           => 'cover',
            'dblc_form_bg_color'     => '#ffffff',
            'dblc_form_border_radius'=> 8,
            'dblc_form_shadow'       => 1,
            'dblc_label_color'       => '#1e1e1e',
            'dblc_btn_bg_color'      => '#2271b1',
            'dblc_btn_text_color'    => '#ffffff',
            'dblc_btn_border_radius' => 4,
            'dblc_link_color'        => '#2271b1',
            'dblc_custom_css'        => '',
            'dblc_login_redirect'    => '',
            'dblc_logout_redirect'   => '',
            'dblc_login_message'     => '',
            'dblc_hide_language_switcher' => 0,
            'dblc_hide_back_to_blog' => 0,
        ];
    }

    /**
     * Get a single option with default fallback
     */
    public function get_option($key) {
        $defaults = $this->get_defaults();
        $default = isset($defaults[$key]) ? $defaults[$key] : '';
        return get_option($key, $default);
    }

    /* ─── Admin Menu ─── */

    public function add_admin_menu() {
        add_options_page(
            __('Login Customizer', 'db-login-customizer'),
            __('Login Customizer', 'db-login-customizer'),
            'manage_options',
            'db-login-customizer',
            [$this, 'render_admin_page']
        );
    }

    public function settings_link($links) {
        $url = admin_url('options-general.php?page=db-login-customizer');
        $settings = '<a href="' . esc_url($url) . '">' . __('Impostazioni', 'db-login-customizer') . '</a>';
        array_unshift($links, $settings);
        return $links;
    }

    /* ─── Register Settings ─── */

    public function register_settings() {
        $fields = array_keys($this->get_defaults());
        foreach ($fields as $field) {
            $args = ['sanitize_callback' => [$this, 'sanitize_option']];
            register_setting('dblc_settings', $field, $args);
        }
    }

    public function sanitize_option($value) {
        $field = current_filter();
        // Determine which field is being sanitized
        if (is_numeric($value)) {
            return intval($value);
        }
        if (strpos($field ?? '', 'color') !== false) {
            return sanitize_hex_color($value) ?: '';
        }
        if ($field === 'dblc_custom_css') {
            return wp_strip_all_tags($value);
        }
        if (strpos($field ?? '', 'redirect') !== false) {
            return esc_url_raw($value);
        }
        return sanitize_text_field($value);
    }

    /* ─── Admin Scripts ─── */

    public function admin_scripts($hook) {
        if ($hook !== 'settings_page_db-login-customizer') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_enqueue_style(
            'dblc-admin',
            DBLC_PLUGIN_URL . 'assets/css/admin.css',
            [],
            DBLC_VERSION
        );

        wp_enqueue_script(
            'dblc-admin',
            DBLC_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-color-picker', 'media-upload'],
            DBLC_VERSION,
            true
        );

        wp_localize_script('dblc-admin', 'dblc', [
            'ajax_url'     => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('dblc_nonce'),
            'login_url'    => wp_login_url(),
            'select_logo'  => __('Seleziona logo', 'db-login-customizer'),
            'select_bg'    => __('Seleziona sfondo', 'db-login-customizer'),
            'use_image'    => __('Usa questa immagine', 'db-login-customizer'),
            'remove'       => __('Rimuovi', 'db-login-customizer'),
        ]);
    }

    /* ─── Admin Page ─── */

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        include DBLC_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /* ─── Login Page Styles ─── */

    public function login_styles() {
        $logo_id    = $this->get_option('dblc_logo_id');
        $logo_width = intval($this->get_option('dblc_logo_width'));
        $bg_color   = $this->get_option('dblc_bg_color');
        $bg_image_id = $this->get_option('dblc_bg_image_id');
        $bg_size    = $this->get_option('dblc_bg_size');
        $form_bg    = $this->get_option('dblc_form_bg_color');
        $form_radius = intval($this->get_option('dblc_form_border_radius'));
        $form_shadow = intval($this->get_option('dblc_form_shadow'));
        $label_color = $this->get_option('dblc_label_color');
        $btn_bg     = $this->get_option('dblc_btn_bg_color');
        $btn_text   = $this->get_option('dblc_btn_text_color');
        $btn_radius = intval($this->get_option('dblc_btn_border_radius'));
        $link_color = $this->get_option('dblc_link_color');
        $custom_css = $this->get_option('dblc_custom_css');
        $hide_lang  = intval($this->get_option('dblc_hide_language_switcher'));
        $hide_back  = intval($this->get_option('dblc_hide_back_to_blog'));

        $logo_url = '';
        if ($logo_id) {
            $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
        }

        $bg_image_url = '';
        if ($bg_image_id) {
            $bg_image_url = wp_get_attachment_image_url($bg_image_id, 'full');
        }

        $css = '';

        // Background
        $css .= "body.login { background-color: {$bg_color}; }";
        if ($bg_image_url) {
            $css .= "body.login { background-image: url('" . esc_url($bg_image_url) . "'); background-size: {$bg_size}; background-position: center center; background-repeat: no-repeat; background-attachment: fixed; }";
        }

        // Logo
        if ($logo_url) {
            $height = intval($logo_width * 0.6);
            $css .= "#login h1 a, .login h1 a { background-image: url('" . esc_url($logo_url) . "'); background-size: contain; background-repeat: no-repeat; background-position: center; width: {$logo_width}px; height: {$height}px; max-width: 100%; }";
        }

        // Form box
        $css .= "#loginform, #registerform, #lostpasswordform { background-color: {$form_bg}; border-radius: {$form_radius}px; border: none; }";
        if ($form_shadow) {
            $css .= "#loginform, #registerform, #lostpasswordform { box-shadow: 0 4px 20px rgba(0,0,0,0.1); }";
        } else {
            $css .= "#loginform, #registerform, #lostpasswordform { box-shadow: none; }";
        }

        // Labels
        $css .= ".login label { color: {$label_color}; }";

        // Button
        $css .= ".wp-core-ui .button-primary { background: {$btn_bg} !important; border-color: {$btn_bg} !important; color: {$btn_text} !important; border-radius: {$btn_radius}px !important; text-shadow: none !important; box-shadow: none !important; }";
        $css .= ".wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus { opacity: 0.9; }";

        // Links
        $css .= ".login #nav a, .login #backtoblog a, .login .privacy-policy-page-link a { color: {$link_color} !important; }";
        $css .= ".login #nav a:hover, .login #backtoblog a:hover { opacity: 0.8; }";

        // Input focus
        $css .= ".login input[type=text]:focus, .login input[type=password]:focus, .login input[type=email]:focus { border-color: {$btn_bg}; box-shadow: 0 0 0 1px {$btn_bg}; }";

        // Hide elements
        if ($hide_lang) {
            $css .= ".language-switcher { display: none !important; }";
        }
        if ($hide_back) {
            $css .= "#backtoblog { display: none !important; }";
        }

        // Focus visible
        $css .= ".login a:focus, .login input:focus, .login button:focus { outline: 3px solid {$btn_bg}; outline-offset: 2px; }";

        // Custom CSS
        if ($custom_css) {
            $css .= $custom_css;
        }

        echo '<style id="dblc-login-styles">' . $css . '</style>';
    }

    /* ─── Login Logo URL & Title ─── */

    public function login_logo_url() {
        return home_url('/');
    }

    public function login_logo_title() {
        return get_bloginfo('name');
    }

    /* ─── Login Footer Scripts ─── */

    public function login_footer_scripts() {
        // Add smooth transitions
        echo '<style>.login input, .login a, .wp-core-ui .button-primary { transition: all 0.2s ease; }</style>';
    }

    /* ─── Custom Login Message ─── */

    public function custom_login_message($message) {
        $custom = $this->get_option('dblc_login_message');
        if (!empty($custom)) {
            $message .= '<p class="message" style="margin-bottom: 10px;">' . esc_html($custom) . '</p>';
        }
        return $message;
    }

    /* ─── Redirect Hooks ─── */

    public function custom_login_redirect($redirect_to, $requested_redirect_to, $user) {
        $custom = $this->get_option('dblc_login_redirect');
        if (!empty($custom) && !is_wp_error($user)) {
            // Don't override explicit redirects (e.g. from ?redirect_to=)
            if (empty($requested_redirect_to) || $requested_redirect_to === admin_url()) {
                return esc_url($custom);
            }
        }
        return $redirect_to;
    }

    public function custom_logout_redirect($redirect_to, $requested_redirect_to, $user) {
        $custom = $this->get_option('dblc_logout_redirect');
        if (!empty($custom)) {
            return esc_url($custom);
        }
        return $redirect_to;
    }

    /* ─── AJAX Preview Data ─── */

    public function ajax_preview_data() {
        check_ajax_referer('dblc_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error();
        }
        wp_send_json_success(['login_url' => wp_login_url()]);
    }

    /* ─── Activation ─── */

    public static function activate() {
        $instance = self::instance();
        $defaults = $instance->get_defaults();
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }

    /* ─── Deactivation ─── */

    public static function deactivate() {
        // Nothing to clean — settings persist
    }

    /* ─── Uninstall ─── */

    public static function uninstall() {
        $defaults = (new self())->get_defaults();
        foreach (array_keys($defaults) as $key) {
            delete_option($key);
        }
    }
}

register_activation_hook(__FILE__, ['DB_Login_Customizer', 'activate']);
register_deactivation_hook(__FILE__, ['DB_Login_Customizer', 'deactivate']);

DB_Login_Customizer::instance();
