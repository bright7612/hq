$(function () {
    // 百度地图API功能
    var map = new BMap.Map("allmap");    // 创建Map实例
    var point = new BMap.Point(121.730079, 31.242628); // 地图中心点，陆家嘴街道办
    map.centerAndZoom(point, 15);  // 初始化地图,设置中心点坐标和地图级别
    map.setCurrentCity("上海");          // 设置地图显示的城市
    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放

    var navigationControl = new BMap.NavigationControl({
        // 靠左上角位置
        anchor: BMAP_ANCHOR_BOTTOM_LEFT,
        // LARGE类型
        type: BMAP_NAVIGATION_CONTROL_LARGE,
    });
    // map.addControl(navigationControl);
    map.addControl(new BMap.NavigationControl(navigationControl));
    // map.addControl(new BMap.ScaleControl());
    // map.addControl(new BMap.OverviewMapControl());
    // map.addControl(new BMap.MapTypeControl());

    var myIcon = null; // 创建我的自定义覆盖物
    var curMarker = null;

    var dataParam = {
        url: '/Hq/Hq/map', // 地图数据接口
        mkUrl: '', // 覆盖物路径
        category: '', // 接口数据参数
    }

    $('.subtab').on('click', function () {
        var category = $(this).attr('data-category');
        if (category === dataParam.category) {
            return false;
        }

        dataParam.mkUrl = '/Public/Hq/dangjian/img/icon/icon_' + category + '.png';
        $('#inforIcon').css({
            'background': 'url(' + dataParam.mkUrl + ') no-repeat',
            'backgroundSize': '100%, 100%'
        });
        dataParam.category = category;
        $(this).siblings().removeClass('active')
            .end().addClass('active');

        $('#inforBox').hide();
        map.clearOverlays();
        getData().then(function (data) {
            if (data.status === 1) {
                render(data.result);
            }
        })
    });

    $('#close').on('click', function (event) {
        event.stopPropagation();
        $(this).parent().hide();
    });

    $('.subtab:eq(0)').click();

    /*
     * 创建覆盖物
     * */
    function addMarker(point, data){
        myIcon = new BMap.Icon(dataParam.mkUrl, new BMap.Size(54, 61));
        // 创建标注对象并添加到地图
        var marker = new BMap.Marker(point, {icon: myIcon});
        marker.data = data;
        map.addOverlay(marker);

        // 监听覆盖物点击
        marker.addEventListener("click", function () {
            attribute(marker);
        });
    }


    /*
     * 覆盖物点击回调
     * */
    function attribute(marker){
        var p = marker.getPosition();  //获取marker的位置
        map.panTo(p);

        if (curMarker) {
            curMarker.setIcon(new BMap.Icon(dataParam.mkUrl, new BMap.Size(54, 61)));
        }
        curMarker = marker;
        marker.setIcon(new BMap.Icon(dataParam.mkUrl, new BMap.Size(65, 72)));

        $('#inforBox').fadeIn(500)
            .find('#inforTitle').text(marker.data.title)
            .end().find('#inforAddress').text(marker.data.address);

    }


    /*
     * 获取列表数据
     * */
    function getData () {
        var def = $.Deferred();
        $.ajax({
            type: 'POST',
            url: dataParam.url,
            dataType: 'json',
            data: {
                category_id: dataParam.category
            },
            cache: false,
            success: function (data) {
                def.resolve(data);
            }
        });
        return def;
    }


    /*
    * 渲染遮盖物
    * */
    function render (result) {
        // 地图页数据渲染
        var dataResult = result;
        // console.log(dataResult);
        for (var i = 0; i < dataResult.length; i++) {
            var point = new BMap.Point(dataResult[i].lng, dataResult[i].lat);
            addMarker(point, dataResult[i]);
        }
    }
});