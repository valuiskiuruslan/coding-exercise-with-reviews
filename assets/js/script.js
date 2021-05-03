;(function ($) {
    $(function() {
        $(".svg-star-rating").starRating({
            starShape: 'rounded',
            readOnly: true,
            useFullStars: true,
            emptyColor: 'transparent',
            activeColor: '#d8a92b',
            strokeColor: '#d8a92b',
            strokeWidth: 40,
            starSize: 20,
        });
    });
})(jQuery);