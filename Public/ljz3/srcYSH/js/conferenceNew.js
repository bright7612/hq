require.config({
    paths: {
        jquery: '../lib/jquery/jquery.min',
        betterScroll: '../lib/better-scroll/bscroll.min',
        layer: '../lib/layer_mobile/layer'
    },
    shim: {
        'template': {
            export: 'template'
        }
    }
});

requirejs(['jquery', 'betterScroll', 'template', 'url', 'util', 'layer'], function ($, BScroll, template, url, util, layer) {
    $(function () {

        var dataParam = {
            userid: 'WangYanYi',
            title: '',
            content: '',
            address: '',
            startTime: 0,
            endTime: 0,
            inviter: {
                idList: [],
                nameList: []
            },
            canJoin: []
        };

        var noteScroll = null;
        var nameScroll = null;

        getData();

        // 点击参会人员选择
        $('#selectName').on('click', function () {
            $('#note').fadeIn(500);
            setTimeout(function () {
                if (!noteScroll) {
                    noteScroll = new BScroll(document.getElementById('noteWrapper'), {click: true});
                } else {
                    noteScroll.refresh();
                }
            }, 20);
        });

        // 参会人员区域点击
        $('#note').on('click', '.note-item', function () {
            $(this).toggleClass('selected');

        }).on('click', '#selectOver', function () {

            // 点击参会人员选择完毕
            dataParam.inviter.idList = []; // 重置参会人ID
            dataParam.inviter.nameList = []; // 重置参会人姓名
            $('.selected').each(function () {
                dataParam.inviter.idList.push($(this).attr('data-nameid')); //
                dataParam.inviter.nameList.push($(this).children('.note-name').text()); //
            });
            console.log(dataParam.inviter.idList);
            renderName();
            $('#note').fadeOut(500);

        }).on('click', '#close', function () {

            // 点击关闭
            var temp = [];
            $('.selected').each(function (index, value) {
                temp.push($(this).attr('data-nameid'));
            });

            var tempString = temp.join('-');
            var idListString = dataParam.inviter.idList.join('-');

            if (temp.length !== dataParam.inviter.idList.length || tempString !== idListString) {
                $('.note-item').removeClass('selected');
                dataParam.inviter.idList.forEach(function (value, index) {
                    $('.note-item[data-nameid='+ value + ']').addClass('selected');
                })
            }

            $('#note').fadeOut(200);
        });

        // 会议创建提交点击
        $('#confirm').on('click', function () {
            // 会议标题输入判断
            dataParam.title = $('#title').val();
            if (dataParam.title === '') {
                alert('请输入会议标题！');
                return;
            }

            // 会议内容输入判断
            dataParam.content = $('#content').val();
            if (dataParam.content === '') {
                alert('请输入会议内容！');
                return;
            }

            // 会议开始时间输入判断
            var startTime = $('#startTime').val();
            dataParam.startTime = util.dateTime(startTime);
            if (isNaN(dataParam.startTime)) {
                alert('请输入会议开始时间！');
                return;
            }

            // 会议结束时间输入判断
            var endTime = $('#endTime').val();
            dataParam.endTime = util.dateTime(endTime);
            if (isNaN(dataParam.endTime)) {
                alert('请输入会议开始时间！');
                return;
            }

            // 会议地点输入判断
            dataParam.address = $('#address').val();
            if (dataParam.address === '') {
                alert('请输入会议地点！');
                return;
            }

            // console.log('userid:' + dataParam.userid);
            // console.log('meetingTitle:' + dataParam.title);
            // console.log('meetingContent:' + dataParam.content);
            // console.log('meetingAddress:' + dataParam.address);
            // console.log('meetingStart:' + dataParam.startTime);
            // console.log('meetingEnd:' + dataParam.endTime);
            // console.log('meetingInviter:' + dataParam.inviter.idList);

            layer.open({
                content: '您确定要创建该会议吗？',
                btn: ['确定', '取消'],
                yes: function(index){
                    dataPost(status);
                    layer.close(index);
                }
            });

        });


        /*
        * 参会人滚动长度初始化
        * */
        function widthInit() {
            var PADDING = 20;
            var MARGIN = 8;
            var BORDER = 2;
            var nameListWidth = 0;

            $('.name-item').each(function () {

                nameListWidth += ($(this).children().width() + PADDING + MARGIN + BORDER);
                // var xixi = $(this).width() + PADDING + MARGIN + BORDER;
                // alert(xixi)
            });
            $('#nameList').width(nameListWidth);
            if (!nameScroll) {
                nameScroll = new BScroll(document.getElementById('nameWrapper'), {
                    scrollX: true,
                    eventPassthrough: 'vertical'
                });
            } else {
                nameScroll.refresh();
            }
        }

        /*
        * 参会人数据渲染
        * */
        function renderName() {
            $('#nameList').html(template('nameItem', dataParam.inviter));
            setTimeout(function () {
                widthInit();
            }, 50);
        }

        /*
        * 可参会人员数据获取
        * */
        function getData() {
            $.ajax({
                type: 'GET',
                url: url.creatMURL,
                cache: false,
                success: function (data) {
                    dataParam.canJoin = data;
                    var ret = {
                        result: dataParam.canJoin
                    }
                    $('#noteScroll').html(template('noteItem', ret));
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        /*
        * 会议数据提交
        * */
        function dataPost (callback) {

            var inviter = JSON.stringify(dataParam.inviter.idList);

            $.ajax({
                type: 'POST',
                url: url.saveMURL,
                data: {
                    userid: dataParam.userid,
                    meetingTitle: dataParam.title,
                    meetingContent: dataParam.content,
                    meetingAddress: dataParam.address,
                    meetingStart: dataParam.startTime,
                    meetingEnd: dataParam.endTime,
                    meetingInviter: inviter
                },
                cache: false,
                success: function (data) {
                    callback(data);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        /*
        * 数据提交后的回调
        * */
        function status(data) {
            if (data === 1) {
                layer.open({
                    content: '创建成功',
                    skin: 'msg',
                    time: 2
                });
                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else {
                alert('创建失败');
            }
        }

    });
});
