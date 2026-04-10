<?php
/**
 * DB Login Customizer — Uninstall
 * Removes all plugin options from the database.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$options = [
    'dblc_logo_id',
    'dblc_logo_width',
    'dblc_bg_color',
    'dblc_bg_image_id',
    'dblc_bg_size',
    'dblc_form_bg_color',
    'dblc_form_border_radius',
    'dblc_form_shadow',
    'dblc_label_color',
    'dblc_btn_bg_color',
    'dblc_btn_text_color',
    'dblc_btn_border_radius',
    'dblc_link_color',
    'dblc_custom_css',
    'dblc_login_redirect',
    'dblc_logout_redirect',
    'dblc_login_message',
    'dblc_hide_language_switcher',
    'dblc_hide_back_to_blog',
];

foreach ($options as $option) {
    delete_option($option);
}
