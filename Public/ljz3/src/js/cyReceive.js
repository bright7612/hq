require.config({
    paths : {
        jquery: "../lib/jquery/jquery.min",
        betterScroll: "../lib/better-scroll/bscroll.min"
    },
    shim: {
        'template':{
            exports: 'template'
        },
    }
});

requirejs(['jquery', 'betterScroll', 'template', 'util'], function ($, BScroll, template, util) {

    $(function () {

        template.helper('dateCompare', util.dateResidue);

        // 定义时间过滤器
        template.helper('formatDate', util.dateFormat);

        var itemScroll = null; // 事件滚动实例
        var wrapperHeight = $('#contentWrapper').height(); // 事件列表容器高度
        var scrollHeight; // 事件列表高度
        var scrollDistance; // 滚动差值

        var dataParam = {
            page: 1, // 分页数
            search: '', // 搜索名称
            html: true, // 渲染方式
            level: false // 优先级
        }
        var posY; // 滚动区域Y轴位置
        var flag = true; // 定义节流阀

        itemScroll = new BScroll(document.getElementById('contentWrapper'), { // new事件滚动实例
            click: true,
            probeType: 2
        })

        getData();

        /*
         搜索按钮事件监听
         * */
        $('#search').on('focus', function () {
            // 获取焦点
            $(this).css({'textAlign': 'left'})
                .nextAll('#sou1').hide()
                .end().nextAll('#sou2').show();
        }).on('blur', function () {
            // 失去焦点
            var text = $(this).val();
            if (text == '') {
                $(this).css({'textAlign': 'center'})
                    .nextAll('#sou2').hide()
                    .end().nextAll('#sou1').show();
            }
        }).on('keypress', function (e) {
            // 点击搜索按钮
            var keycode = e.keyCode;
            if(keycode == '13') {
                e.preventDefault();
                dataParam.search = $(this).val();
                dataParam.html = true;
                dataParam.page = 1;
                if(!$(this).val()) {
                    dataParam.level = true;
                }
                //请求搜索接口
                getData();
                setTimeout(function () {
                    if (dataParam.html) {
                        dataParam.html = false;
                    }
                }, 500);
            }
        });

        $('#contentWrapper').on('click', '.incident-item', function () {
            var detailId = $(this).attr('data-id');
            window.location.href = './cyDetail.html' + '?id=' + detailId;
        });

        itemScroll.on('scroll', function (pos) {
            posY = pos.y;
            // console.log(pos.x + '~' + pos.y);
            if (posY < scrollDistance) {
                if (flag) {
                    dataParam.page++;
                    $('#loadtext').text('正在加载中...');
                    setTimeout(function () {
                        getData();
                        $('#loadtext').text('');
                        itemScroll.refresh();
                        flag = true;
                    }, 500);
                }
                flag = false;
            }
        });

        function getData() {
            $.ajax({
                type: 'GET',
                url: 'http://fdj.cmlzjz.com/wxInterface/receiveEventRegister',
                data: {
                    'page': dataParam.page,
                    'search': dataParam.search
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    var items = template('items', data);

                    if (dataParam.level) {
                        console.log('level-html');
                        $('#incidentList').html(items);
                        dataParam.level = false;
                        console.log('level又变为fasle');
                    } else {
                        if (dataParam.search && dataParam.html) {
                            console.log('html');
                            $('#incidentList').html(items);
                            itemScroll.scrollTo(0, 0);
                        } else {
                            console.log('append');
                            $('#incidentList').append(items);
                        }
                    }
                    itemScroll.refresh();

                    if(data.result.length < 10) {
                        $('#loadtext').text('没有更多数据了...');
                        console.log($('.incident-item').length);
                        return false;
                    } else {
                        scrollHeight = $('#scrollwrapper').height();
                        scrollDistance = wrapperHeight - scrollHeight;
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    });
});