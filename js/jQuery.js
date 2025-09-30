$(function(){

    $('#navigation a').click(function(){

        $('#navigation .active').removeClass('active'); // remove the class from the currently selected
        $(this).addClass('active'); // add the class to the newly clicked link

    });

});
