/**
 * Theme SEO Admin Settings - Media Uploader
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
        var $counter = $('#default_og_description_counter');
        var len = ($field.val() || '').length;
        $counter.text(len + ' / ' + limit);
        $counter.toggleClass('is-over', limit > 0 && len > limit);
    }
    
    $(document).ready(function() {
        var mediaUploader;

        var $desc = $('#default_og_description');
        if ($desc.length) {
            updateCounter($desc);
            $desc.on('input', function() {
                updateCounter($desc);
            });
        }

        var $dims = $('#og_image_dimensions');
        setWarning($('#og_image_warning'), imageWarning($dims.data('width'), $dims.data('height')));
        
        // Upload OG Image button
        $('#upload_og_image_button').on('click', function(e) {
            e.preventDefault();
            
            // If the uploader object has already been created, reopen it
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Create the media uploader
            mediaUploader = wp.media({
                title: 'Select Default OG Image',
                button: {
                    text: 'Use this image'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });
            
            // When an image is selected
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                // Update the input field
                $('#default_og_image').val(attachment.url);
                $('#default_og_image_id').val(attachment.id);
                
                // Update the preview
                $('#og_image_preview').html('<img src="' + attachment.url + '" alt="OG Image Preview">');
                $('#og_image_dimensions').text(attachment.width + ' × ' + attachment.height + ' px');
                setWarning($('#og_image_warning'), imageWarning(attachment.width, attachment.height));
                
                // Show the remove button
                $('#remove_og_image_button').show();
            });
            
            // Open the uploader
            mediaUploader.open();
        });
        
        // Remove OG Image button
        $('#remove_og_image_button').on('click', function(e) {
            e.preventDefault();
            
            // Clear the input field
            $('#default_og_image').val('');
            $('#default_og_image_id').val('');
            
            // Clear the preview
            $('#og_image_preview').html('');
            $('#og_image_dimensions').text('');
            setWarning($('#og_image_warning'), '');
            
            // Hide the remove button
            $(this).hide();
        });
    });
    
})(jQuery);
