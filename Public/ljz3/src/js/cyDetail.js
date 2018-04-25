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

        var detailId = util.getUrl('id');
        var contentScroll;

        template.helper('formatDate', util.dateFormat);

        $.ajax({
            type: 'GET',
            url: 'http://fdj.cmlzjz.com/wxInterface/detailEventRegister',
            // url: '../json/data.json',
            data: {
                'detailId': detailId
            },
            cache: false,
            success: function (data) {
                console.log(data);
                if (data.result.status == 2) {
                    $('#statusLine').addClass('sended');
                    $('#btnWrapper').show();
                } else if (data.result.status == 3) {
                    $('#statusLine').addClass('received');
                    $('body').addClass('received');
                    replyScroll.refresh();
                } else if (data.result.status == 4) {
                    $('#statusLine').addClass('end');
                }
                $('#incidentDetail').html(template('detail', data));

                contentScroll = new BScroll(document.getElementById('contentWrapper', {

                }));
            },
            error: function (error) {
                console.log(error);
            }
        });

        var replyScroll = null;

        replyScroll = new BScroll(document.getElementById('replyWrapper'), {
            click: true
        });

        $('#receive').on('click', function () {
            $('#statusLine').addClass('received');
            $('body').addClass('received');

            $.ajax({
                type: 'POST',
                url: 'http://fdj.cmlzjz.com/wxInterface/agreeEventRegister',
                data: {'detailId': detailId},
                cache: false,
                success: function (data) {
                    console.log(data);
                },
                error: function (error) {
                    console.log('上传ajax失败');
                }
            });


            contentScroll.refresh();

        });

        $('#return').on('click', function () {
            layer.open({
                content: '您确定要退回吗？',
                btn: ['确定', '取消'],
                yes: function(index){
                    layer.close(index);
                }
            });
        });

        $('#submit').on('click', function () {
            text = $('#textarea').val();
            if (text === '') {
                alert('请填写事件回复！');
                return;
            }

            layer.open({
                content: '您确定要上报事件回复吗？',
                btn: ['确定', '取消'],
                yes: function(index){
                    photoLength = $('.thumbnail').length;
                    if (photoLength > 0) {
                        uploader.upload();
                    } else {
                        disposeAjax();
                    }
                    // location.reload();
                    layer.close(index);
                }
            });
        });

        $('#close').on('click', function () {
            $('body').removeClass('received');
            // uploaderInit();
            contentScroll.refresh();
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
        var photoSend = [];
        var text = "";
        /*
         监听图片删除按钮的点击
         * */
        $('#fileList').on('click', '.photo-delete', function () {
            var photoId = $(this).parent().attr('id'); // 获取图片ID
            console.log(photoId);
            uploader.removeFile(photoId, true);

            var photoIndex = $('.photo-delete').index($(this));
            var photoLength = $('.photo-delete').length;

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

            replyScroll.refresh();
            console.log(uploader.getFiles());
        });

        // 当有队列被移除时
        uploader.on('fileDequeued', function(file){
            // uploader.removeFile(file);
            console.log('队列中有图片被移除');
            console.log(uploader.getFiles());
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
            photoSend.push(res._raw);
            console.log(photoSend);
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
                disposeAjax();
            }
        });

        function disposeAjax () {
            $.ajax({
                type: 'POST',
                url: 'http://fdj.cmlzjz.com/wxInterface/saveAgreeEventRegister',
                data: {
                    'detailId': detailId,
                    'windview': text,
                    'photoName': photoSend,
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    location.reload();
                },
                error: function (error) {
                    console.log('上传ajax失败');
                }
            });
        }
    });
});