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
    });

})(jQuery);
