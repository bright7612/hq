require.config({
    paths: {
        jquery: '../lib/jquery/jquery.min',
        betterScroll: '../lib/better-scroll/bscroll.min'
    },
    shim: {
        'template': {
            exports: 'template'
        }
    }
});

requirejs(['jquery', 'betterScroll', 'template', 'url'], function ($, BScroll, template, url) {
    $(function () {

        var dataParam = {
            search: '',
            preSearch: ''
        }

        var scroll = new BScroll(document.getElementById('wrapper'), {click: true});

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

                if (dataParam.search == dataParam.preSearch) {
                   return;
                }
                dataParam.preSearch = $(this).val();
                // dataParam.html = true;
                // dataParam.page = 1;
                // if(!$(this).val()) {
                //     dataParam.level = true;
                // }
                getData();
                // setTimeout(function () {
                //     if (dataParam.html) {
                //         dataParam.html = false;
                //     }
                // }, 500);
            }
        });

        function getData () {
            $.ajax({
                type: 'GET',
                url: url.telListURL,
                data: {search: dataParam.search},
                cache: false,
                success: function (data) {
                    console.log(data);
                    $('#scroll').html(template('tel', data));
                    setTimeout(function () {
                        scroll.refresh();
                        scroll.scrollTo(0, 0);
                    }, 20);
                }
            });
        }

    });
});
