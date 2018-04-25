require.config({
    paths: {
        jquery: "../lib/jquery/jquery.min",
        betterScroll: "../lib/better-scroll/bscroll.min"
    },
    shim: {
        'template':{
            exports: 'template'
        },
    }
});

require(['jquery', 'betterScroll', 'template', 'util', 'url'], function ($, BScroll, template, util, url) {
    $(function () {

        var origin = $('body').attr('data-type');

        template.helper('formatDate', util.dateFormat);

        var itemScroll = new BScroll(document.getElementById('contentWrapper'), { // new事件滚动实例
            click: true,
            probeType: 2
        })
        var wrapperHeight = $('#contentWrapper').height(); // 事件列表容器高度
        var scrollHeight; // 事件列表高度
        var scrollDistance; // 滚动差值

        var dataParam = {
            url: '', // 数据地址
            page: 1, // 分页数
            search: '', // 搜索名称
            html: true, // 渲染方式
            level: false, // 优先级
            posY: '', // 滚动区域Y轴位置
            flag: true, // 定义节流阀
            length: '' // 返回的数据长度
        };

        if (origin === 'qyfc') {
            dataParam.url = url.qyfcListURL;
        } else if (origin === 'qyxx') {
            dataParam.url = url.qyxxListURL;
        } else if (origin === 'shyw') {
            dataParam.url = url.shywListURL;
        } else if (origin === 'bsym') {
            dataParam.url = url.bsymListURL;
        }
        getData();

        // 滚动监听
        itemScroll.on('scroll', function (pos) {
            dataParam.posY = pos.y;
            // console.log(pos.x + '~' + pos.y);
            if (dataParam.length == 10) {
                if (dataParam.posY < scrollDistance) {
                    if (dataParam.flag) {
                        dataParam.page++;
                        $('#loadtext').text('正在加载中...');
                        setTimeout(function () {
                            getData();
                            $('#loadtext').text('');
                            itemScroll.refresh();
                            dataParam.flag = true;
                        }, 500);
                    }
                    dataParam.flag = false;
                }
            }
        });

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
                    $('#image').show();
                    dataParam.level = true;
                } else {
                    $('#image').hide();
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

        // 监听每一个事件item点击
        $('#contentWrapper').on('click', '.item', function () {
            var detailId = $(this).attr('data-id');
            window.location.href = './yshDetail.html' + '?id=' + detailId + '&origin=' + origin;
        });

        /*
         * 事件列表数据获取
         * */
        function getData() {
            $.ajax({
                type: 'GET',
                url: dataParam.url,
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
                        $('#listWrapper').html(items);
                        dataParam.level = false;
                        console.log('level又变为fasle');
                    } else {
                        if (dataParam.search && dataParam.html) {
                            console.log('html');
                            $('#listWrapper').html(items);
                            itemScroll.scrollTo(0, 0);
                        } else {
                            console.log('append');
                            $('#listWrapper').append(items);
                        }
                    }
                    itemScroll.refresh();

                    dataParam.length = data.result.length;
                    if(dataParam.length < 10) {
                        $('#loadtext').text('没有更多数据了...');
                        console.log($('.item').length);
                    }
                    scrollHeight = $('#scrollwrapper').height();
                    scrollDistance = wrapperHeight - scrollHeight;
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

    });
});