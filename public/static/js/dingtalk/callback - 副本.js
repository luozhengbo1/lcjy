// jsapi权限验证配置
dd.config({
    agentId: $('#agentId').text(), // 必填，微应用ID
    corpId:$('#corpId').text(),//必填，企业ID
    timeStamp: $('#timeStamp').text(), // 必填，生成签名的时间戳
    nonceStr: $('#nonceStr').text(), // 必填，生成签名的随机串
    signature:$('#signature').text(), // 必填，签名
//        type:0,   //选填。0表示微应用的jsapi,1表示服务窗的jsapi。不填默认为0。该参数从dingtalk.js的0.8.3版本开始支持
    jsApiList : [
        'runtime.info',
        'biz.contact.choose',
        'device.notification.confirm',
        'device.notification.alert',
        'device.notification.prompt',
        'biz.ding.post',
        'biz.user.get',
        'biz.util.openLink'
    ] // 必填，需要使用的jsapi列表，注意：不要带dd。
});

//通过ready接口处理成功验证
dd.ready(function() {
    // 设置导航栏标题
    dd.biz.navigation.setTitle({
        title: '酱酒智造（后台）',
        onSuccess: function(data) {
        },
        onFail: function(err) {
            log.e(JSON.stringify(err));
        }
    });
 
   
    // 获取用户信息
    dd.biz.user.get({
        onSuccess: function (info) {
        	//弹出用户信息
         alert('userGet success: ' + JSON.stringify(info));
            $('#avatar').attr('src',info.avatar);
            $('#nickname').text(info.nickName);

        },
        onFail: function (err) {
            alert('userGet fail: ' + JSON.stringify(err));
        }
    });
    // 开启下拉刷新
    dd.ui.pullToRefresh.enable({
        onSuccess: function() {

        },
        onFail: function() {
        	
        }
    });

    //收起下拉loading
    dd.ui.pullToRefresh.stop();

   

    // 获取免登授权码
    dd.runtime.permission.requestAuthCode({
    	//企业ID
        corpId : _config.corpId,
        onSuccess : function(info) {
            $.ajax({
                // url : '/userinfo?code=' + info.code + '&corpid='
                // + _config.corpId,
                url : '/userinfo?code=' + info.code + '&corpid='
                + _config.corpId,
                type : 'GET',
                success : function(data, status, xhr) {
                    var info = JSON.parse(data);
                    document.getElementById("userName").innerHTML = info.name;
                    document.getElementById("userId").innerHTML = info.userid;
                    // 图片
//					if(info.avatar.length != 0){
//			            var img = document.getElementById("userImg");
//			            img.src = info.avatar;
//			                      img.height = '100';
//			                      img.width = '100';
//			          }
					

                },
                error : function(xhr, errorType, error) {
                    logger.e("yinyien:" + _config.corpId);
                    alert(errorType + ', ' + error);
                }
            });

        },
        onFail : function(err) {
            alert('fail: ' + JSON.stringify(err));
        }
    });
});

//错误调试
dd.error(function(err) {
    alert('dd error: ' + JSON.stringify(err));
});