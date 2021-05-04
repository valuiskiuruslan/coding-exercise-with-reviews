;(function ($) {
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    $(document).ready(function () {
        $('table.wp-list-table #the-list').sortable({
            'items': 'tr',
            'axis': 'y',
            'update': function (e, ui) {
                var post_type = cewr_drag_drop.post_type;
                var order = $('#the-list').sortable('serialize');

                var paged = getUrlParameter('paged');
                if (typeof paged === 'undefined') {
                    paged = 1;
                }

                // send the data through ajax
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: cewr_drag_drop.action,
                        post_type: post_type,
                        order: order,
                        paged: paged,
                        table_sort_nonce: cewr_drag_drop.table_sort_nonce
                    },
                    cache: false,
                    dataType: "html",
                    success: function (data) {

                    },
                    error: function (html) {

                    }
                });
            }
        });
    });
})(jQuery);