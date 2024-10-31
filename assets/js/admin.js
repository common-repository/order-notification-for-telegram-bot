onftb = {
    init: function() {
        jQuery('#onftb_send_test_message').click(this.send_test_message);
    },
    block: function () {
        jQuery( '#mainform' ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    },
    unblock: function() {
        jQuery( '#mainform' ).unblock();
    },
    send_test_message:function () {
        onftb.block();
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'onftb_send_test_message',
            },
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                alert(data.message);
                onftb.unblock();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus + ', ' + errorThrown);
                onftb.unblock();
            }
        });
    }
};
onftb.init();