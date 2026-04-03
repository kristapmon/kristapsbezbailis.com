jQuery(function ($) {

    $('#navigation a').click(function () {

        $('#navigation .active').removeClass('active');
        $(this).addClass('active');

    });

});
