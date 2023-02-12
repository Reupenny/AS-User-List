// favorites-list-remove.js
jQuery(document).ready(function ($) {
    $('.remove-favorite').on('click', function () {
        var post_id = $(this).data('post-id');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: favorites_list_remove.ajax_url,
            data: {
                action: 'remove_from_favorites',
                post_id: post_id,
                nonce: favorites_list_remove.nonce
            },
            success: function (response) {
                if (response.success) {
                    alert('Item removed from favorites');
                } else {
                    alert('Item could not be removed from favorites');
                }
            }
        });
        // Refresh the page to show changes
        setTimeout(function () {
            location.reload();
        }, 350);
    });
});


