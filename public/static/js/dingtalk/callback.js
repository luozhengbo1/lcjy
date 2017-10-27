
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
//       alert('userGet success: ' + JSON.stringify(info));
            $('#avatar').attr('src',info.avatar);
            $('#nickname').text(info.nickName);

        },
        onFail: function (err) {
            alert('userGet fail: ' + JSON.stringify(err));
        }
    });
    // 开启下拉刷新
//  dd.ui.pullToRefresh.enable({
//      onSuccess: function() {
//
//      },
//      onFail: function() {
//      	
//      }
//  });
	   dd.runtime.permission.requestAuthCode({
      corpId :'ding38872dd0a69908aa35c2f4657eb6378f',
      onSuccess : function(result) {
          $.ajax({
              url : '/dingtalk/index/userinfo?code=' + result.code + '&corpid=ding38872dd0a69908aa35c2f4657eb6378f',
              type : 'GET',
              success : function(data, status, xhr) {
                 
                  var info = JSON.parse(data);
//					 alert(info);
                  document.getElementById("userName").innerHTML = info.name;
                  document.getElementById("userId").innerHTML = info.userid;
                  
                  // 图片
//                  if(info.avatar.length != 0){
//                      var img = document.getElementById("userImg");
//                      img.src = info.avatar;
//                         img.height = '100';
//                         img.width = '100';
//                    }	
              },
              error : function(xhr, errorType, error) {
                    alert(errorType + ', ' + error);
//                alert(1234);
              }
          });

      },
      onFail : function(err) {
            alert('fail: ' + JSON.stringify(err));
//			alert(123);
      }
  });

   dd.runtime.info({
        onSuccess: function(info) {
//             alert('runtime info: ' + JSON.stringify(info));
        },
        onFail: function(err) {
//这里一直返回返回errorMessage:”err msg redirect_uri domain is not secure domain”,”errorCode”:”3”错误
        }
    });
});

//错误调试
dd.error(function(err) {
    alert('dd error: ' + JSON.stringify(err));
});