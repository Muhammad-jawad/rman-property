(function( $ ) {
	'use strict';

    jQuery(document).ready(function($) {
        // Attach a click event to your button
        $('#fetchProperties').on('click', function() {
            // Send the AJAX request to your callback function
            $.ajax({
                url: LocalizedData.ajaxurl, // This variable is automatically defined by WordPress and points to the admin-ajax.php file
                type: 'POST',
                data: {
                    action: 'rman_ajax_action',
                },
                dataType: 'json',
                success: function(response) {
                    // Handle the response from your PHP method
                    let responseDiv = document.querySelector("#responseMessage");
                    
                    responseDiv.innerHTML = response.message;

                    responseDiv.style.display = 'block';   
                    // console.log(response.message);
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.log('AJAX error:', error);
                }
            });
        });

    });
    // Initialize the color picker for the color fields
    $(document).ready(function () {
        $('.color-field').wpColorPicker();
    });

    
})( jQuery );
