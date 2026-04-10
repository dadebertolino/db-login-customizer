<?php
if (!defined('ABSPATH')) exit;
if (!current_user_can('manage_options')) return;

$plugin = DB_Login_Customizer::instance();
$defaults = $plugin->get_defaults();

// Get current values
$opts = [];
foreach ($defaults as $key => $default) {
    $opts[$key] = get_option($key, $default);
}

// Logo preview
$logo_preview = '';
if ($opts['dblc_logo_id']) {
    $logo_preview = wp_get_attachment_image_url($opts['dblc_logo_id'], 'medium');
}
$bg_preview = '';
if ($opts['dblc_bg_image_id']) {
    $bg_preview = wp_get_attachment_image_url($opts['dblc_bg_image_id'], 'thumbnail');
}
?>
<div class="wrap dblc-wrap">
    <h1><?php _e('DB Login Customizer', 'db-login-customizer'); ?></h1>
    <p class="description"><?php _e('Personalizza la pagina di login di WordPress con il tuo brand.', 'db-login-customizer'); ?></p>

    <div class="dblc-layout">
        <div class="dblc-main">
            <form method="post" action="options.php" id="dblc-settings-form">
                <?php settings_fields('dblc_settings'); ?>

                <!-- Logo -->
                <div class="dblc-section">
                    <h2><?php _e('Logo', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_logo_id"><?php _e('Logo personalizzato', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <div class="dblc-media-field" data-target="dblc_logo_id">
                                    <input type="hidden" name="dblc_logo_id" id="dblc_logo_id" value="<?php echo esc_attr($opts['dblc_logo_id']); ?>">
                                    <div class="dblc-media-preview" id="dblc_logo_id_preview">
                                        <?php if ($logo_preview): ?>
                                            <img src="<?php echo esc_url($logo_preview); ?>" alt="">
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="button dblc-upload-btn" data-target="dblc_logo_id" data-title="<?php esc_attr_e('Seleziona logo', 'db-login-customizer'); ?>">
                                        <?php _e('Seleziona logo', 'db-login-customizer'); ?>
                                    </button>
                                    <button type="button" class="button dblc-remove-btn" data-target="dblc_logo_id" <?php echo $opts['dblc_logo_id'] ? '' : 'style="display:none"'; ?>>
                                        <?php _e('Rimuovi', 'db-login-customizer'); ?>
                                    </button>
                                </div>
                                <p class="description"><?php _e('Sostituisce il logo WordPress nella pagina di login.', 'db-login-customizer'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_logo_width"><?php _e('Larghezza logo (px)', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="dblc_logo_width" id="dblc_logo_width" value="<?php echo esc_attr($opts['dblc_logo_width']); ?>" min="50" max="500" step="10" class="small-text">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Sfondo -->
                <div class="dblc-section">
                    <h2><?php _e('Sfondo', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_bg_color"><?php _e('Colore di sfondo', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_bg_color" id="dblc_bg_color" value="<?php echo esc_attr($opts['dblc_bg_color']); ?>" class="dblc-color-picker">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_bg_image_id"><?php _e('Immagine di sfondo', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <div class="dblc-media-field" data-target="dblc_bg_image_id">
                                    <input type="hidden" name="dblc_bg_image_id" id="dblc_bg_image_id" value="<?php echo esc_attr($opts['dblc_bg_image_id']); ?>">
                                    <div class="dblc-media-preview" id="dblc_bg_image_id_preview">
                                        <?php if ($bg_preview): ?>
                                            <img src="<?php echo esc_url($bg_preview); ?>" alt="">
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="button dblc-upload-btn" data-target="dblc_bg_image_id" data-title="<?php esc_attr_e('Seleziona sfondo', 'db-login-customizer'); ?>">
                                        <?php _e('Seleziona immagine', 'db-login-customizer'); ?>
                                    </button>
                                    <button type="button" class="button dblc-remove-btn" data-target="dblc_bg_image_id" <?php echo $opts['dblc_bg_image_id'] ? '' : 'style="display:none"'; ?>>
                                        <?php _e('Rimuovi', 'db-login-customizer'); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_bg_size"><?php _e('Dimensione sfondo', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <select name="dblc_bg_size" id="dblc_bg_size">
                                    <option value="cover" <?php selected($opts['dblc_bg_size'], 'cover'); ?>>Cover</option>
                                    <option value="contain" <?php selected($opts['dblc_bg_size'], 'contain'); ?>>Contain</option>
                                    <option value="auto" <?php selected($opts['dblc_bg_size'], 'auto'); ?>>Auto</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Form -->
                <div class="dblc-section">
                    <h2><?php _e('Form di login', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_form_bg_color"><?php _e('Sfondo form', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_form_bg_color" id="dblc_form_bg_color" value="<?php echo esc_attr($opts['dblc_form_bg_color']); ?>" class="dblc-color-picker">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_form_border_radius"><?php _e('Border radius form (px)', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="dblc_form_border_radius" id="dblc_form_border_radius" value="<?php echo esc_attr($opts['dblc_form_border_radius']); ?>" min="0" max="50" class="small-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Ombra form', 'db-login-customizer'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="dblc_form_shadow" value="1" <?php checked($opts['dblc_form_shadow'], 1); ?>>
                                    <?php _e('Mostra ombra sotto il form', 'db-login-customizer'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Colori -->
                <div class="dblc-section">
                    <h2><?php _e('Colori', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_label_color"><?php _e('Colore etichette', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_label_color" id="dblc_label_color" value="<?php echo esc_attr($opts['dblc_label_color']); ?>" class="dblc-color-picker">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_btn_bg_color"><?php _e('Colore bottone', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_btn_bg_color" id="dblc_btn_bg_color" value="<?php echo esc_attr($opts['dblc_btn_bg_color']); ?>" class="dblc-color-picker">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_btn_text_color"><?php _e('Colore testo bottone', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_btn_text_color" id="dblc_btn_text_color" value="<?php echo esc_attr($opts['dblc_btn_text_color']); ?>" class="dblc-color-picker">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_btn_border_radius"><?php _e('Border radius bottone (px)', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="number" name="dblc_btn_border_radius" id="dblc_btn_border_radius" value="<?php echo esc_attr($opts['dblc_btn_border_radius']); ?>" min="0" max="50" class="small-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_link_color"><?php _e('Colore link', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_link_color" id="dblc_link_color" value="<?php echo esc_attr($opts['dblc_link_color']); ?>" class="dblc-color-picker">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Opzioni -->
                <div class="dblc-section">
                    <h2><?php _e('Opzioni', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_login_message"><?php _e('Messaggio personalizzato', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="dblc_login_message" id="dblc_login_message" value="<?php echo esc_attr($opts['dblc_login_message']); ?>" class="regular-text" placeholder="<?php esc_attr_e('Es: Benvenuto nel pannello di gestione', 'db-login-customizer'); ?>">
                                <p class="description"><?php _e('Mostrato sopra il form di login.', 'db-login-customizer'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Nascondi elementi', 'db-login-customizer'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" name="dblc_hide_language_switcher" value="1" <?php checked($opts['dblc_hide_language_switcher'], 1); ?>>
                                        <?php _e('Nascondi selettore lingua', 'db-login-customizer'); ?>
                                    </label>
                                    <br>
                                    <label>
                                        <input type="checkbox" name="dblc_hide_back_to_blog" value="1" <?php checked($opts['dblc_hide_back_to_blog'], 1); ?>>
                                        <?php _e('Nascondi "← Torna a [sito]"', 'db-login-customizer'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Redirect -->
                <div class="dblc-section">
                    <h2><?php _e('Redirect', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_login_redirect"><?php _e('Dopo il login', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="url" name="dblc_login_redirect" id="dblc_login_redirect" value="<?php echo esc_url($opts['dblc_login_redirect']); ?>" class="regular-text" placeholder="<?php echo esc_attr(admin_url()); ?>">
                                <p class="description"><?php _e('Lascia vuoto per il comportamento predefinito (Dashboard).', 'db-login-customizer'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="dblc_logout_redirect"><?php _e('Dopo il logout', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <input type="url" name="dblc_logout_redirect" id="dblc_logout_redirect" value="<?php echo esc_url($opts['dblc_logout_redirect']); ?>" class="regular-text" placeholder="<?php echo esc_attr(home_url('/')); ?>">
                                <p class="description"><?php _e('Lascia vuoto per il comportamento predefinito (pagina di login).', 'db-login-customizer'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- CSS Custom -->
                <div class="dblc-section">
                    <h2><?php _e('CSS personalizzato', 'db-login-customizer'); ?></h2>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="dblc_custom_css"><?php _e('CSS aggiuntivo', 'db-login-customizer'); ?></label>
                            </th>
                            <td>
                                <textarea name="dblc_custom_css" id="dblc_custom_css" rows="6" class="large-text code" placeholder="<?php esc_attr_e('body.login { ... }', 'db-login-customizer'); ?>"><?php echo esc_textarea($opts['dblc_custom_css']); ?></textarea>
                                <p class="description"><?php _e('CSS applicato alla pagina wp-login.php. Usa i selettori standard di WordPress.', 'db-login-customizer'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(__('Salva impostazioni', 'db-login-customizer')); ?>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="dblc-sidebar">
            <div class="dblc-sidebar-box">
                <h3><?php _e('Anteprima', 'db-login-customizer'); ?></h3>
                <p class="description"><?php _e('Apri la pagina di login in una nuova scheda per vedere le modifiche.', 'db-login-customizer'); ?></p>
                <a href="<?php echo esc_url(wp_login_url()); ?>" target="_blank" rel="noopener" class="button button-secondary" style="width:100%;text-align:center;">
                    <?php _e('Apri pagina di login ↗', 'db-login-customizer'); ?>
                    <span class="screen-reader-text"><?php _e('(si apre in una nuova finestra)', 'db-login-customizer'); ?></span>
                </a>
            </div>
            <div class="dblc-sidebar-box">
                <h3><?php _e('Selettori CSS utili', 'db-login-customizer'); ?></h3>
                <code style="display:block;font-size:11px;line-height:1.6;white-space:pre;">body.login
#login h1 a
#loginform
.login label
#wp-submit
#nav a
#backtoblog a
.login .message
.login #login_error</code>
            </div>
            <div class="dblc-sidebar-box">
                <h3><?php _e('Info', 'db-login-customizer'); ?></h3>
                <p><strong>DB Login Customizer</strong> v<?php echo esc_html(DBLC_VERSION); ?></p>
                <p><?php _e('Autore:', 'db-login-customizer'); ?> <a href="https://www.davidebertolino.it" target="_blank" rel="noopener">Davide Bertolino<span class="screen-reader-text"> <?php _e('(si apre in una nuova finestra)', 'db-login-customizer'); ?></span></a></p>
            </div>
        </div>
    </div>
</div>
