/**
 * DB Login Customizer — Admin JS
 */
(function($) {
    'use strict';

    $(document).ready(function() {

        // Color pickers
        $('.dblc-color-picker').wpColorPicker();

        // Media upload
        $(document).on('click', '.dblc-upload-btn', function(e) {
            e.preventDefault();

            var button = $(this);
            var target = button.data('target');
            var title  = button.data('title') || dblc.select_logo;

            var frame = wp.media({
                title: title,
                button: { text: dblc.use_image },
                multiple: false,
                library: { type: 'image' }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var previewUrl = attachment.sizes && attachment.sizes.medium
                    ? attachment.sizes.medium.url
                    : attachment.url;

                $('#' + target).val(attachment.id);
                $('#' + target + '_preview').html(
                    '<img src="' + previewUrl + '" alt="">'
                );
                button.siblings('.dblc-remove-btn').show();
            });

            frame.open();
        });

        // Remove media
        $(document).on('click', '.dblc-remove-btn', function(e) {
            e.preventDefault();

            var target = $(this).data('target');
            $('#' + target).val('0');
            $('#' + target + '_preview').html('');
            $(this).hide();
        });

        // Handle unchecked checkboxes (send 0)
        $('#dblc-settings-form').on('submit', function() {
            $(this).find('input[type=checkbox]').each(function() {
                if (!this.checked) {
                    $(this).after(
                        '<input type="hidden" name="' + this.name + '" value="0">'
                    );
                }
            });
        });

        /* ─── Theme Sync ─── */

        var themeData = null;

        function detectTheme() {
            var $preview = $('#dblc-theme-preview');
            var $info = $('#dblc-theme-info');

            if (!$('#dblc_sync_theme').is(':checked')) {
                $preview.slideUp(200);
                return;
            }

            $info.html('<p><em>Rilevamento tema in corso...</em></p>');
            $preview.slideDown(200);

            $.post(dblc.ajax_url, {
                action: 'dblc_detect_theme',
                nonce: dblc.nonce
            }, function(response) {
                if (!response.success) {
                    $info.html('<p style="color:#d63638;">Errore nel rilevamento.</p>');
                    return;
                }

                themeData = response.data;
                var html = '<table class="dblc-theme-table">';
                html += '<tr><td><strong>Tema:</strong></td><td>' + esc(themeData.theme_name) + '</td></tr>';
                html += '<tr><td><strong>theme.json:</strong></td><td>' + (themeData.has_theme_json ? '✅ Sì' : '❌ No') + '</td></tr>';

                if (themeData.btn_bg_color) {
                    html += '<tr><td><strong>Colore primario:</strong></td><td>' + colorSwatch(themeData.btn_bg_color) + '</td></tr>';
                }
                if (themeData.bg_color) {
                    html += '<tr><td><strong>Sfondo:</strong></td><td>' + colorSwatch(themeData.bg_color) + '</td></tr>';
                }
                if (themeData.label_color) {
                    html += '<tr><td><strong>Testo:</strong></td><td>' + colorSwatch(themeData.label_color) + '</td></tr>';
                }
                if (themeData.link_color) {
                    html += '<tr><td><strong>Link:</strong></td><td>' + colorSwatch(themeData.link_color) + '</td></tr>';
                }
                if (themeData.btn_text_color) {
                    html += '<tr><td><strong>Testo bottone:</strong></td><td>' + colorSwatch(themeData.btn_text_color) + '</td></tr>';
                }
                if (themeData.font_family) {
                    html += '<tr><td><strong>Font:</strong></td><td><code style="font-size:12px;">' + esc(themeData.font_family) + '</code></td></tr>';
                }
                if (themeData.logo_url) {
                    html += '<tr><td><strong>Logo:</strong></td><td><img src="' + esc(themeData.logo_url) + '" style="max-height:40px;border:1px solid #ddd;border-radius:3px;"></td></tr>';
                }
                if (themeData.btn_border_radius) {
                    html += '<tr><td><strong>Border radius:</strong></td><td>' + themeData.btn_border_radius + 'px</td></tr>';
                }

                html += '</table>';

                if (!themeData.has_theme_json && !themeData.btn_bg_color && !themeData.logo_url) {
                    html += '<p style="color:#996800;">Il tema attivo non espone dati sufficienti. I valori configurati manualmente saranno utilizzati.</p>';
                }

                $info.html(html);
            }).fail(function() {
                $info.html('<p style="color:#d63638;">Errore di connessione.</p>');
            });
        }

        // Apply theme values to form fields
        $('#dblc-apply-theme').on('click', function() {
            if (!themeData) return;

            var mapping = {
                'dblc_bg_color': themeData.bg_color,
                'dblc_form_bg_color': themeData.form_bg_color,
                'dblc_label_color': themeData.label_color,
                'dblc_btn_bg_color': themeData.btn_bg_color,
                'dblc_btn_text_color': themeData.btn_text_color,
                'dblc_link_color': themeData.link_color,
            };

            for (var field in mapping) {
                if (mapping[field]) {
                    var $input = $('input[name="' + field + '"]');
                    if ($input.length) {
                        $input.val(mapping[field]);
                        try {
                            $input.wpColorPicker('color', mapping[field]);
                        } catch(e) {}
                    }
                }
            }

            if (themeData.btn_border_radius) {
                $('#dblc_btn_border_radius').val(themeData.btn_border_radius);
            }

            if (themeData.logo_id) {
                $('#dblc_logo_id').val(themeData.logo_id);
                if (themeData.logo_url) {
                    $('#dblc_logo_id_preview').html('<img src="' + esc(themeData.logo_url) + '" alt="">');
                    $('[data-target="dblc_logo_id"].dblc-remove-btn').show();
                }
            }

            // Visual feedback
            var $btn = $(this);
            var originalText = $btn.text();
            $btn.text('✓ Applicato!');
            setTimeout(function() {
                $btn.text(originalText);
            }, 2000);
        });

        // Toggle on checkbox change
        $('#dblc_sync_theme').on('change', detectTheme);

        // Auto-detect on load if checked
        if ($('#dblc_sync_theme').is(':checked')) {
            detectTheme();
        }

        // Helpers
        function esc(str) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str || ''));
            return div.innerHTML;
        }

        function colorSwatch(hex) {
            return '<span style="display:inline-block;width:16px;height:16px;border-radius:3px;background:' + esc(hex) + ';border:1px solid #ccc;vertical-align:middle;margin-right:6px;"></span><code>' + esc(hex) + '</code>';
        }
    });

})(jQuery);
