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

requirejs(['jquery', 'betterScroll', 'template', 'util', 'url'], function ($, BScroll, template, util, url) {
    $(function () {

        template.helper('formatDate', util.dateFormat);

        var origin = util.getUrl('origin');
        var dataParam = {
            url: '', // 数据地址
            id: '' // ID
        }
        dataParam.id = util.getUrl('id');

        if (origin === 'qyfc') {
            dataParam.url = url.qyfcDetailURL;
        } else if (origin === 'qyxx') {
            dataParam.url = url.qyxxDetailURL;
        } else if (origin === 'shyw') {
            dataParam.url = url.shywDetailURL;
        } else if (origin === 'bsym') {
            dataParam.url = url.bsymDetailURL;
        }

        var itemScroll = new BScroll(document.getElementById('contentWrapper'), {
            click: true,
            probeType: 2
        })

        $.ajax({
            type: 'GET',
            url: dataParam.url,
            data: {
                'detailId': dataParam.id
            },
            cache: false,
            success: function (data) {
                console.log(data);
                $('#header').html(template('infor', data));
                $('#content').html(data.xxfb.content);
                itemScroll.refresh();
            }
        });
    });
});
