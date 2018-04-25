require.config({
    paths: {
        jquery: '../lib/jquery/jquery.min',
        betterScroll: '../lib/better-scroll/bscroll.min'
    },
    shim: {
        'template': {
            export: 'template'
        }
    }
});

requirejs(['jquery', 'betterScroll', 'template', 'url', 'util'], function ($, BScroll, template, url, util) {
    $(function () {

        // 定义时间过滤器
        template.helper('formatDate', util.dateFormat);

        var dataParam = {
            userid: 'WangYanYi',
            page: 1,
            detailId: '',
            posY: '', // 滚动区域Y轴位置
            flag: true, // 定义节流阀
            length: '' // 返回的数据长度
        };

        var scroll = new BScroll(document.getElementById('listWrapper'), {
            click: true,
            probeType: 2
        });

        var wrapperHeight = $('#listWrapper').height(); // 事件列表容器高度
        var scrollHeight; // 事件列表高度
        var scrollDistance; // 滚动差值

        getList();

        // 滚动监听
        scroll.on('scroll', function (pos) {
            dataParam.posY = pos.y;
            // console.log(pos.x + '~' + pos.y);
            if (dataParam.length == 10) {
                if (dataParam.posY < scrollDistance) {
                    if (dataParam.flag) {
                        dataParam.page++;
                        $('#loadtext').text('正在加载中...');
                        scroll.refresh();
                        setTimeout(function () {
                            getList();
                            $('#loadtext').text('');
                            scroll.refresh();
                            dataParam.flag = true;
                        }, 800);
                    }
                    dataParam.flag = false;
                }
            }
        });

        $('#conferlist').on('click', '.conference-item', function () {
            dataParam.detailId = $(this).attr('data-detailId');
            getDetail();
            $('#detail').addClass('active');
        });

        $('#close').on('click', function () {
            $(this).parent().removeClass('active');
            $('#detailScroll').html('');
        });


        function getList() {

            $.ajax({
                type: 'GET',
                url: url.sendMURL,
                data: {
                    userid: dataParam.userid,
                    page: dataParam.page
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    $('#conferlist').append(template('item', data));
                    setTimeout(function () {
                        scroll.refresh();
                        dataParam.length = data.result.length;
                        if(dataParam.length < 10) {
                            $('#loadtext').text('没有更多数据了...');
                            scroll.refresh();
                            console.log($('.conference-item').length);
                        }
                        scrollHeight = $('#scrollWrapper').height();
                        scrollDistance = wrapperHeight - scrollHeight;
                    }, 20);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        function getDetail() {

            $.ajax({
                type: 'GET',
                url: url.detailMURL,
                data: {
                    userid: dataParam.userid,
                    detailId: dataParam.detailId
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    $('#detailScroll').html(template('detailContent', data));
                    setTimeout(function () {
                        widthInit();
                    }, 20);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        /*
         * 参会人滚动长度初始化
         * */
        function widthInit() {
            var PADDING = 20;
            var MARGIN = 8;
            var BORDER = 2;
            var nameListWidth = 0;
            $('.name-item').each(function () {
                nameListWidth += ($(this).width() + PADDING + MARGIN + BORDER);
            });
            $('.name-list').width(nameListWidth);

            var nameScroll = new BScroll(document.getElementById('nameWrapper'), {
                scrollX: true,
                eventPassthrough: 'vertical'
            });
        }
    });
});
