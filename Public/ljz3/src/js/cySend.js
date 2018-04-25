require.config({
    paths: {
        jquery: '../lib/jquery/jquery.min',
        betterScroll: '../lib/better-scroll/bscroll.min',
        webuploader: '../lib/webuploader/webuploader.html5only.min',
        layer: '../lib/layer_mobile/layer'
    },
    shim: {
        'template': {
            exports: 'template'
        }
    }
});

requirejs(['jquery', 'betterScroll', 'template', 'layer', 'webuploader', 'util'], function ($, BScroll, template, layer, WebUploader, util) {
    $(function () {

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
        itemScroll.on('scroll', function (pos) {
            posY = pos.y;
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
                url: 'http://fdj.cmlzjz.com/wxInterface/sendEventRegister',
                // url: '../json/data.json',
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

        $('#contentWrapper').on('click', '.incident-item', function () {
            var detailId = $(this).attr('data-id');
            window.location.href = './cyDetail.html' + '?id=' + detailId;
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


        $('#add').on('click', function () {
            $.ajax({
                type: 'GET',
                url: 'http://fdj.cmlzjz.com/wxInterface/editEventRegister',
                cache: false,
                success: function (data) {
                    $('#eventType').html(template('typeOption', data));
                }
            });
            $('#module').fadeIn(500);
        });

        $('#close').on('click', function () {
            $('#module').fadeOut(500);
        });

        // var padding = 20;
        // var margin = 8;
        // var border = 2;
        //
        // widthInit();
        //
        // function widthInit() {
        //     var nameListWidth = 0;
        //     $('.name-item').each(function () {
        //         nameListWidth += ($(this).width() + padding + margin + border);
        //     });
        //     $('.name-list').width(nameListWidth);
        //     if (!picScroll) {
        //         picScroll = new BScroll(document.getElementById('nameWrapper'), {
        //             scrollX: true,
        //             eventPassthrough: 'vertical'
        //         })
        //     } else {
        //         picScroll.refresh();
        //     }
        // }

        var picScroll = null;

        $('.name-list').on('click', '.delete', function () {
            $(this).parent().remove();
            // widthInit();
            picScroll.refresh()
        });

        var noteScroll = null; // 全局通讯录容器实例
        $('#addName').on('click', function () {
            $('#note').fadeIn(500);
            if (!noteScroll) {
                noteScroll = new BScroll(document.getElementById('noteLists'), {
                    click: true
                })
            } else {
                noteScroll.refresh();
            }
        });

        $('#noteLists').on('click', '.note-item', function () {
            var nameItem = '<li class="name-item fl" data-nameid="'+ $(this).attr('data-nameid') +'">' +
                '<span class="text">' + $(this).text() + '</span>' +
                '<span class="delete">' +
                '<span class="iconfont icon-guanbi">' +
                '</span>' +
                '</span>' +
                '</li>';
            $('#nameList').append(nameItem);
            // widthInit();
            // picScroll.refresh();
            $('#note').fadeOut(500);
        });


        // 默认的图片容器
        var liDefault = '<li class="uploader-item">' +
            '<img src="/Public/ljz3/src/imgs/default.jpg">' +
            '</li>';

        var $list = $('#fileList');


        // 初始化Web Uploader
        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: false,
            // swf文件路径
            // swf: '../lib/webuploader/Uploader.swf',
            // 文件接收服务端。
            server: 'http://fdj.cmlzjz.com/fileUploader/index',
            // 选择文件的按钮。可选。
            pick: '#filePicker',
            // 只允许选择图片文件。
            // accept: {
            //     title: 'Images',
            //     extensions: 'gif,jpg,jpeg,bmp,png',
            //     mimeTypes: 'image/*'
            // },
            // method:'POST'
        });
        var photoLength = null;
        var eventParam = {
            eventTitle: null,
            eventType: null,
            dateHappen: null,
            dateComplete: null,
            eventAddress: null,
            eventContent: null,
            photoSend: []
        };

        /*
         监听图片删除按钮的点击
         * */
        $('#fileList').on('click', '.photo-delete', function () {
            var photoId = $(this).parent().attr('id'); // 获取图片ID
            console.log(photoId);
            uploader.removeFile(photoId, true);

            var photoIndex = $('.photo-delete').index($(this));
            var photoLength = $('.photo-delete').length;
            console.log(photoLength);

            if (uploader.getFiles().length) {
                if (photoLength >= 3) {
                    $(this).parent().remove();
                } else if (photoLength === 1) {
                    $(this).parent().replaceWith(liDefault);
                } else {
                    photoIndex === 1 ? $(this).parent().replaceWith(liDefault) : $(this).parent().parent().append(liDefault).end().remove();
                }
            }

            console.log('删除完毕');
        });

        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            var $li = $(
                    '<li id="' + file.id + '" class="uploader-item thumbnail">' +
                    '<img>' +
                    // '<div class="info">' + file.name + '</div>' +
                    '<span class="photo-delete iconfont icon-cuowu"></span>' +
                    '</li>'
                ),
                $img = $li.find('img');


            // $list为容器jQuery实例
            // 判断添加新的图片时已经存在的图片数量
            var photoCount = $list.find('.thumbnail').length;
            if (photoCount === 0) {
                $('.uploader-item').eq(1).replaceWith($li);
            } else if (photoCount === 1) {
                $('.uploader-item').eq(2).replaceWith($li);
            } else {
                $list.append( $li );
            }

            // 创建缩略图
            // 如果为非图片文件，可以不用调用此方法。
            // thumbnailWidth x thumbnailHeight 为 100 x 100
            uploader.makeThumb( file, function( error, src ) {
                if ( error ) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }
                $img.attr( 'src', src );
                // }, thumbnailWidth, thumbnailHeight );
            });

        });

        // 当有队列被移除时
        uploader.on('fileDequeued', function(file){
            // uploader.removeFile(file);
            console.log('队列中有图片被移除');
            console.log(uploader.getFiles());
            // uploader.reset();
        });

        $('#submit').on('click', function () {

            eventParam.eventTitle = $('#eventTitle').val();
            if (eventParam.eventTitle === '') {
                alert('请输入事件标题！');
                return;
            }

            eventParam.eventType = $('#eventType').val();

            eventParam.dateHappen = $('#dateHappen').val();
            alert(eventParam.dateHappen);
            eventParam.dateHappen = util.dateTime(eventParam.dateHappen);
            alert(eventParam.dateHappen);
            return;
            if (isNaN(eventParam.dateHappen)) {
                alert('请输入事件发生时间！');
                return;
            }

            eventParam.dateComplete = $('#dateComplete').val();
            eventParam.dateComplete = util.dateTime(eventParam.dateComplete);
            if (isNaN(eventParam.dateComplete)) {
                alert('请输入建议完成时间！');
                return;
            }

            eventParam.eventAddress = $('#eventAddress').val();
            if (eventParam.eventAddress === '') {
                alert('请输入事件发生地址！');
                return;
            }

            eventParam.eventContent = $('#eventContent').val();
            if (eventParam.eventContent === '') {
                alert('请输入事件发生内容！');
                return;
            }
            console.log('xixixixixx')

            layer.open({
                content: '您确定要上报事件吗？',
                btn: ['确定', '取消'],
                yes: function(index){
                    photoLength = $('.thumbnail').length;
                    if (photoLength > 0) {
                        uploader.upload();
                    } else {
                        reportAjax();
                    }
                    // location.reload();
                    layer.close(index);
                }
            });

        });

        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id ),
                $percent = $li.find('.progress span');

            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<p class="progress"><span></span></p>')
                    .appendTo( $li )
                    .find('span');
            }

            $percent.css( 'width', percentage * 100 + '%' );
        });

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        uploader.on( 'uploadSuccess', function( file, res ) {
            $( '#'+file.id ).addClass('upload-state-done');
            eventParam.photoSend.push(res._raw);
        });

        // 文件上传失败，显示上传出错。
        uploader.on( 'uploadError', function( file ) {
            var $li = $( '#'+file.id ),
                $error = $li.find('div.error');

            // 避免重复创建
            if ( !$error.length ) {
                $error = $('<div class="error"></div>').appendTo( $li );
            }

            $error.text('上传失败');
        });

        // 完成上传完了，成功或者失败，先删除进度条。
        uploader.on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress').remove();

            photoLength--;
            console.log(photoLength);

            if (photoLength === 0) {
                reportAjax();
            }
        });

        function reportAjax () {
            $.ajax({
                type: 'POST',
                url: 'http://fdj.cmlzjz.com/wxInterface/saveEventRegister',
                data: {
                    'eventname': eventParam.eventTitle,
                    'eventtype': eventParam.eventType,
                    'eventtime': eventParam.dateHappen,
                    'adviseendtime': eventParam.dateComplete,
                    'address': eventParam.eventAddress,
                    'eventmemo': eventParam.eventContent,
                    'photo': eventParam.photoSend,
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    // location.reload();
                },
                error: function (error) {
                    console.log('上传ajax失败');
                }
            });
        }
    });
});
