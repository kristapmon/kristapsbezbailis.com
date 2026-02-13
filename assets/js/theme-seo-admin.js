/**
 * Theme SEO Admin Settings - Media Uploader
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        var mediaUploader;
        
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
                
                // Update the preview
                $('#og_image_preview').html('<img src="' + attachment.url + '" alt="OG Image Preview">');
                
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
            
            // Clear the preview
            $('#og_image_preview').html('');
            
            // Hide the remove button
            $(this).hide();
        });
    });
    
})(jQuery);
