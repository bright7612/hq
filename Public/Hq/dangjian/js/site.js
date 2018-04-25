$(function () {

    $('#lessonList').on('click', '.btn', function () {
        var imgUrl = $(this).attr('data-url');
        if (imgUrl) {
            $('#bg').find('#ewm').attr('src', imgUrl)
                .end().fadeIn(200);
        }
    });

    $('#close').on('click', function () {
        $(this).parent().parent().parent().hide();
    });

});
