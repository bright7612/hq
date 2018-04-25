define(function () {
    var server = 'http://fdj.cmlzjz.com/'

    return {

        qyfcListURL: server + 'Xxfb/qyfcindex', // 企业风采列表数据接口

        qyxxListURL: server + 'Xxfb/qyxxindex', // 企业信息列表数据接口
        
        shywListURL: server + 'Xxfb/shdtindex', // 商会要闻列表数据接口

        bsymListURL: server + 'Xxfb/shfcindex', // 榜上有名列表数据接口

        qyfcDetailURL: server + 'Xxfb/qyfcdetil', // 企业风采详情数据接口

        qyxxDetailURL: server + 'Xxfb/qyxxdetil', // 企业信息详情数据接口

        shywDetailURL: server + 'Xxfb/shdtdetil', // 商会要闻详情数据接口

        bsymDetailURL: server + 'Xxfb/shfcdetil', // 榜上有名详情数据接口

        creatMURL: server + 'Meeting/creatMeeting', // 新建会议接口

        saveMURL: server + 'Meeting/saveMeeting', // 会议保存接口

        receiveMURL: server + 'Meeting/receiveMeetingList', // 我参与会议列表接口

        sendMURL: server + 'Meeting/sendMeetingList', // 我发起会议列表接口

        detailMURL: server + 'Meeting/detailMeeting' // 会议详情接口
    }
});
