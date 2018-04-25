$(function () {
    // 获取党支部数据
    $.getJSON('/index.php?s=/home/api/dzb', function (data) {
        if (data.status == 1) {
            var dzbShowData = template('showData', data);
            $('#dzbShowList').html(dzbShowData);
            var dzbAllData = template('dzbList', data);
            $('#dzbAll').html(dzbAllData);
            var data = data.result;
            $('.data-wrapper').find('#dzbNum b').text(data.dzbNum)
                .end().find('#totalPeople b').text(data.people);
        }
    });

    var dzbId = null;
    $('body').on('click', '#first.big .link', function () {
        // 点击一级列表的党支部
        dzbId = $(this).attr('data-id');
        if (dzbId > 0) {
            $('#dzbName').text($(this).text());
            dzbClick();
        } else {
            // 出现更多详情
            $('#shield').find('#moreTitle').text('党支部名称').end().fadeIn(500).find('#dzbAll').show();
        }
    }).on('click', '#first.small', function () {
        // 点击缩小后的一级列表
        $(this).removeClass('small').addClass('big')
            .css({'transform': 'translate(0px, 0px) scale(1, 1)'});
        $('#second').fadeOut(1000);
        $('#basePeople').fadeOut(1000);
    }).on('click', '#shield .dzb-list', function () {
        // 点击更多的党支部
        $('#dzbName').text($(this).text());
        $('#shield').fadeOut(500).find('.more-content').children().hide();
        dzbId = $(this).attr('data-id');
        dzbClick();
    }).on('click', '#second .link', function () {
        var dyId = $(this).attr('data-id');
        var username = $(this).text();
        if (dyId > 0) {
            // alert(username);
            window.location.href = 'http://139.224.54.226:8080/djk/jsmind?username='+username;
        } else {
            // 出现更多详情
            $('#shield').find('#moreTitle').text('党员姓名').end().fadeIn(500).find('#dyAll').show();
        }
    }).on('click', '#shield .dy-list', function () {
        var username = $(this).text();
        window.location.href = 'http://139.224.54.226:8080/djk/jsmind?username='+username;
        $('#shield').fadeOut(500).find('.more-content').children().hide();
    });

    $('#close').on('click', function () {
        $('#shield').fadeOut(500).find('.more-content').children().hide();
    });


    /*
        点击每个党支部后的通用动画
     */
    function dzbClick() {
        $('#first').removeClass('big').addClass('small')
            .css({'transform': 'translate(-640px, -160px) scale(0.5, 0.5)'});
        // 获取所点击党支部的数据
        $.getJSON('/index.php?s=/home/api/dy',{dzbId: dzbId}, function (data) {
            if (data.status == 1) {
                var dyShowData = template('showData', data);
                $('#dyShowList').html(dyShowData);
                var dyAllData = template('dyList', data);
                $('#dyAll').html(dyAllData);
                var data = data.result;
                $('#basePeople b').text(data.people);
            }
        });
        $('#second').fadeIn(1000);
        $('#basePeople').fadeIn(1000);
    }
});
