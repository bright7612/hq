$(function () {
    // 获取数据
    $.getJSON('/index.php?s=/home/api/orgRank', function (data) {
        if (data.status == 1) {
            var showData = template('showData', data);
            $('#orgShowData').html(showData);
            var allData = template('allData', data);
            $('#orgAllData').html(allData);
            var data = data.result;
            $('#orgAdd b').text(data.increase);
        }
    });
    $.getJSON('/index.php?s=/home/api/peopleRank', function (data) {
        if (data.status == 1) {
            var showData = template('showData', data);
            $('#peopleShowData').html(showData);
            var allData = template('allData', data);
            $('#peopleAllData').html(allData);
            var data = data.result;
            $('#peopleAdd b').text(data.increase);
        }
    });

    $('body').on('click', '.more#orgMore', function () {
        $('#shield').fadeIn(500).find('#orgTable').show();
    }).on('click', '.more#peopleMore', function () {
        $('#shield').fadeIn(500).find('#peopleTable').show();
    }).on('click', '#close', function () {
        $('#shield').fadeOut(500).find('.more-innerbox').children().hide();
    });
});
