define(['jquery'], function ($) {

    /*
    * 获取cookie值
    * */
    function getCookie(name) {
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
        if(arr=document.cookie.match(reg))
            return unescape(arr[2]);
        else
            return null;
    }

    /*
    * 时间撮日期格式化
    * */
    function dateFormat (date, fmt) {
        var date = new Date(date);

        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length));
        }
        var o = {
            'M+': date.getMonth() + 1,
            'd+': date.getDate(),
            'h+': date.getHours(),
            'm+': date.getMinutes(),
            's+': date.getSeconds()
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(fmt)) {
                var str = o[k] + '';
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? str : ('00' + str).substr(str.length))
            }
        }
        return fmt
    }

    /*
    * 日期字符串转时间撮
    * */
    function dateTime(str) {
        str = str.replace(/-/g,'/');
        str = str.replace(/T/g, ' ');
        if (str.indexOf('.') > 0) {
            alert(1);
            str = str.match(/((\S|\s)*)\./)[1];
        }
        var date = new Date(str);
        return date.getTime();
    }

    /*
    * 计算截止剩余时间
    * */
    function dateResidue (dateEnd) {
        var date = new Date();
        var dateNow = date.getTime();
        return Math.round((dateEnd - dateNow) / 1000);
    }

    /*
    * 截取url参数值
    * */
    function getUrl(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]);
        return null;
    }

    return {
        getCookie: getCookie,
        dateFormat: dateFormat,
        dateTime: dateTime,
        dateResidue: dateResidue,
        getUrl: getUrl
    }
});
