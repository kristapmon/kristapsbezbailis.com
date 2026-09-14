/**
 * Social Sharing meta box — media picker, size warning, character counters.
 */
(function($) {
    'use strict';

    function imageWarning(width, height) {
        var messages = [];
        width = parseInt(width, 10) || 0;
        height = parseInt(height, 10) || 0;
        if (!width || !height) {
            return '';
        }
        if (width < 1200 || height < 630) {
            messages.push('Smaller than 1200×630');
        }
        var ratio = width / height;
        if (ratio < 1.8 || ratio > 2.0) {
            messages.push('Aspect ratio is not ~1.91:1');
        }
        return messages.join('. ');
    }

    function setWarning($el, text) {
        if (text) {
            $el.text(text).show();
        } else {
            $el.text('').hide();
        }
    }

    function updateCounter($field) {
        var limit = parseInt($field.data('limit'), 10) || 0;
        var $counter = $('.theme-og-counter[data-for="' + $field.attr('id') + '"]');
        var len = ($field.val() || '').length;
        $counter.text(len + ' / ' + limit);
        $counter.toggleClass('is-over', limit > 0 && len > limit);
    }

    $(document).ready(function() {
        var mediaUploader;

        $('.theme-og-count').each(function() {
            updateCounter($(this));
        }).on('input', function() {
            updateCounter($(this));
        });

        var $dims = $('#theme_og_image_dimensions');
        setWarning($('#theme_og_image_warning'), imageWarning($dims.data('width'), $dims.data('height')));

        $('#theme_og_image_select').on('click', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Select social sharing image',
                button: {
                    text: 'Use this image'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.medium
                    ? attachment.sizes.medium.url
                    : attachment.url;

                $('#theme_og_image_id').val(attachment.id);
                $('#theme_og_image_preview').html('<img src="' + url + '" alt="">');
                $('#theme_og_image_dimensions').text(attachment.width + ' × ' + attachment.height + ' px');
                $('#theme_og_image_remove').show();
                setWarning($('#theme_og_image_warning'), imageWarning(attachment.width, attachment.height));
            });

            mediaUploader.open();
        });

        $('#theme_og_image_remove').on('click', function(e) {
            e.preventDefault();
            $('#theme_og_image_id').val('');
            $('#theme_og_image_preview').html('');
            $('#theme_og_image_dimensions').text('');
            setWarning($('#theme_og_image_warning'), '');
            $(this).hide();
        });
    });

})(jQuery);
