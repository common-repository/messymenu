jQuery(document).ready(function($) {
    $("#messymenu-links-sortable").sortable({
        update: function(event, ui) {
            // Get the new order of IDs
            var order = $(this).sortable("toArray", { attribute: "data-id" });

            // Send the new order via AJAX
            $.post(ajaxurl, {
                action: 'messymenu_update_order',
                order: order, // Send the array of link IDs in the new order
                messymenu_nonce: $('#messymenu_nonce').val()
            }, function(response) {
                console.log("Order updated", response); // Debugging: check server response
            });
        }
    });
});
