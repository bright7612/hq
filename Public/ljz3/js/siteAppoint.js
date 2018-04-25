$(function () {
    /*
     初始化时间选择器
     * */
    $(".form-datetime").datetimepicker(
        {
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            format: "yyyy-mm-dd hh:ii"
        });
    $('body').on("change","#sbblock input[type='checkbox']",function(){
        $('input[name="sb"]').val($('input[type=checkbox]:checked').map(function(){return this.value}).get().join(','))
    });
    /*
     点击tab栏控制列表的显现
     */
//        $('body').on('click', '.btn', function () {
//            $('.btn').removeClass('active');
//            $(this).addClass('active');
//            $('.appoint-content').scrollTop(0);
//            $('.list-wrapper').removeClass('show').addClass('hide');
//        });

    /*
     点击列表页控制模态框的行为
     */
    $('body').on('click', '#place-wrapper .want-app', function () {
        //点击场地预约中我要预约
        var title = $(this).parent().parent().children().first().text();
        $("input[name='aid']").val($(this).data("id"));
        $("#sbblock").remove();
        $.getJSON("/home/ljzv/getSb?id="+$(this).data("id"),function (res) {
            var sb =  template("sb",res);
            $("form").append(sb);
        });
        $('#placeBox').find('.title').text(title)
            .end().show().parent().show();

    }).on('click', '#place-wrapper .watch-app', function () {
        var title = $(this).parent().parent().children().first().text();
        $.getJSON("/home/api/getApplied?id="+$(this).data("id"),function (res) {
            if(res.data && res.data.length > 0){
                var yylist =  template("temp",res);
                $('#appointList').html(yylist);
                $("#appointList").show();
                $("#watchBox").find(".hint").hide();
            }else{
                $("#appointList").hide();
                $("#watchBox").find(".hint").show();
            }
            $('#watchBox').find('.title').text(title)
                .end().show().parent().show();
        });
    }).on('click', '#placeConfirm', function () {
        //点击场地预约的确认按钮
        // alert($("form").serialize());return;
        $.post("/home/ljzv/hdbm",$("form").serialize(),function (data) {
            if(data.status == 1){
                $('#placeConfirm').parent().prev().find('input, textarea').val('').removeAttr('checked')
                    .end().parent().hide().parent().hide();
            }else{

            }
            if(data.info){
                alert(data.info);
            }

        },"json");


    }).on('click', '.cancel', function () {
        //点击确认与取消按钮清除表单数据
        $(this).parent().prev().find('input, textarea').val('').removeAttr('checked')
            .end().parent().hide().parent().hide();

    }).on('click', '#watchClose ', function () {
        $('#appointList').html('');
    });

    /*
     监听滚动区域的scrollTop
     * */
    $('#scroll').on('scroll', function () {
        var scroll = $(this).scrollTop();
        if (scroll > 0) {
            $(this).parent().find('.returnTop').show();
        } else {
            $(this).parent().find('.returnTop').hide();
        }
    });


    returnTop('#returnTP', '#scroll');


    /*
     返回顶部功能
     * */
    function returnTop (btn, scroll) {
        $(btn).on('click', function () {
            var top = $(scroll).scrollTop();
            var perTop = top / 25;
            var interval = setInterval(function () {
                if (top <= 0) {
                    clearInterval(interval);
                    top = 0
                }
                $(scroll).scrollTop(top -= perTop);
            }, 10);
        });
    }
});
