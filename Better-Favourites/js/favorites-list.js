// favorites-list.js
jQuery(document).ready(function ($) {
    $(document).on('click', '.add-to-favorites', function (e) {
        e.preventDefault();
        var post_id = $(this).data('post-id');
        var data = {
            action: 'add_to_favorites',
            post_id: post_id,
            nonce: favorites_list.nonce
        };
        $.post(favorites_list.ajax_url, data, function (response) {
            console.log('Item added to favorites');
        });
        var Modal = $("#myModal");
        // Set the CSS display property to "block" to make it visible
        Modal.css("display", "block");
        $('body').append(`<script>
      jQuery(document).ready(function($) {
        $('.clickclose').click(function() {
          // Wait for a second before reloading the page
          setTimeout(function() {
            location.reload();
          }, 5);
        });
      });
      
      jQuery(document).ready(function($) {
        $('#modal_close').click(function(e) {
          // Wait for a second before reloading the page
          setTimeout(function() {
            location.reload();
          }, 5);
        });
      });
      </script>`);
    });
});
