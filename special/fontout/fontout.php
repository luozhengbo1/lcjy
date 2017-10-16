<!doctype html>
<html lang="zh">
<head>
    <?php include "head.php"?>
    <script>!function(){function n(){var n=document.documentElement.offsetWidth/20,e=document.documentElement;e.style.fontSize=n<20?n+"px":"20px"}n(),window.onresize=function(){n()}}()</script>
    <link href="http://www.bootcss.com/p/buttons/css/buttons.css" rel="stylesheet" type="text/css"/>
</head>
<body class="output-fontbg pa wh100" layout-align="center center" layout layout="column">
    <main class="container">
        <section class="tc plw">
            <h1 class="custom-head mgb fzxbs-font">定制展示</h1>
            <div class="fzxbs-font">
                <p>效果图仅供参考，实物会更美</p>
            </div>
        </section>
        <section class="tc pr center-wrap">
            <img class="pic bottle" src="images/bottle.png?v=481ed440d9" alt="">
            <iframe src="fontget.php" id="childFrame" name="childFrame" class="child-frame pa l0" width="100%"></iframe>
        </section>
        <section id="fontout-form" class="tc plw">
            <input type="text" id="ipt-font" class="ipt font-enter bd-none bg-color-none w100 tc" placeholder="请输入2~6个汉字（4个字效果最佳）">
            <div class="submit-wrap" layout-align="center center" layout>
                <input type="button" id="font-output" class="button button-caution button-rounded" ft-white value="生成效果图">
            </div>
        </section>
        <section id="mask-layer" class="tl0 wh100" hide>
            <div class="popups tl0 wh100 close-mask"></div>
            <div class="plw" layout-align="center center" layout>
                <div class="user-info-wrap w100">
                    <div class="user-info-form z1" style="display: none">
                            <h3 class="tc fzxbs-font">提交资料，索取订货政策</h3>
                            <div class="ipt-row">
                                <input type="text" id="user" maxlength="6" class="ipt bg-color-none bd-none" placeholder="姓名">
                            </div>
                            <div class="ipt-row">
                                <input type="text" id="phone" maxlength="11" class="ipt ipt-com bg-color-none bd-none" placeholder="电话">
                            </div>
                            <div class="ipt-row">
                                <input type="text" id="quantity" maxlength="11" class="ipt ipt-com bg-color-none bd-none" placeholder="预定数量">
                            </div>
                            <div class="tc" layout-align="space-between center" layout>
                                <input type="button" class="bd-none bd-fillet close-mask bg-color fzxbs-font public-submit" ft-white id="close-form" value="取消">
                                <input type="button" class="bd-none bd-fille0 bg-color fzxbs-font public-submit" ft-white id="submit-from" value="提交">
                            </div>
                        </div>
                <div class="user-info-form z1">
                    <div align="center"><button class="button button-highlight button-rounded button-small" onclick="window.location.reload();">重新输入文字</button></div>
                    <h3 class="tc fzxbs-font">长按识别二维码，订鸡年大坛</h3>
                    <div class="qcode" id="qcode" style="text-align: center">
                        <img src="" style="width: 100px;height: 100px;margin: 8px auto;">
                        <p><a href="http://t.j9zz.com/tools/qcode" class="button button-caution button-rounded button-small">换成我的二维码</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="js/ajax.js?v=e5f3e345da" charset="utf-8"></script>
<script src="/public/static/js/jquery.min.js"></script>
<script>
    var takeFont=[];
    ({
        init:function(){
            var fontIpt=document.getElementById("ipt-font"),
                fontOutput=document.getElementById("font-output"),
                fontFounder=document.getElementsByClassName("founder-font"),
                fontoutForm=document.getElementById("fontout-form"),
                maskLayer=document.getElementById("mask-layer"),
                _this=this,
                isReal=true;

            maskLayer.style.display='block';
            var closeForm=document.getElementById("close-form");
            maskLayer.style.display='none';

            fontOutput.onclick=fontEnterSubmit;
            fontIpt.onkeydown=function(evt){ //
                var evtevt=evt ? evt: (window.event ? window.event : null);//兼容IE和FF
                evt.keyCode==13 &&	fontEnterSubmit();
            }
            fontIpt.onkeyup=function(){
                var getFontVal=getFontEnter();
                parseInt(getFontVal.olength)>6 && (fontIpt.value=getFontVal.ovalue.substr(0,6));
            }

            closeForm.onclick=function(){
                fontoutForm.style.display='block';
                maskLayer.style.display='none';
            }

            this.removeSymbol();

            function getFontEnter () {//获取输入的值
                var fontVal=fontIpt.value,
                    fontLen=fontVal.length;
                return {
                    ovalue:fontVal,
                    olength:fontLen
                }
            }

            function fontEnterSubmit () {
                var getFontVal=getFontEnter();
                if(parseInt(getFontVal.olength)<2 || parseInt(getFontVal.olength)>6){
                    _this.showMsg('请输入2到6个字符')
                }else{
                    if(isReal){
                        oRequest.countViews(2);
                        isReal=!isReal;
                    }
                    takeFont.push(getFontVal.ovalue);
                    document.getElementById('childFrame').contentWindow.location.reload(true);
//							_this.maskTime && clearTimeout(_this.maskTime);
                    fontoutForm.style.display='none';
                    maskLayer.style.display='block';
                    fontIpt.value='';
                    /*_this.maskTime=setTimeout(function(){
                        document.getElementById("mask-layer")
                    },3000);*/
                    /*var sPhoto=document.getElementsByClassName('photo'),
                        imgUrl='http://192.168.1.176/lcjj/index1.php?m=Home&c=index&a=tmp&text='+getFontVal.ovalue;
                    document.getElementById("font-in").style.backgroundImage='url('+imgUrl+')';
                    if(sPhoto.length===0){
                        var sImage=new Image();
                        sImage.className='photo';
                        sImage.src=imgUrl;
                        for (var i=0;i<fontFounder.length;i++) {
                            fontFounder[i].appendChild(sImage);
                        }
                    }else{
                        for (var i=0;i<sPhoto.length;i++) {
                            sPhoto[i].src=imgUrl;
                        }
                    }*/
                }
            }

            this.userFormSubmit();
        },
        removeSymbol: function() { //去掉输入框的字符 and 空格
            var ipt = document.getElementsByClassName("ipt");
            for(var i = 0; i < ipt.length; i++) {
                (function(e) {
                    this.addEvent(ipt[e], 'keyup', function() {
                        if(this.hasClass(ipt[e], 'ipt-com')) {
                            ipt[e].value = ipt[e].value.replace(/\D|^0/g, '').replace(/\s/g, '');
                        } else {
                            ipt[e].value = ipt[e].value.replace(/\s/g, '');
                        }
                    })
                }.bind(this))(i)
            }
        },
        addEvent: function(obj, type, fn) { //添加事件监听，三个参数分别为 对象、事件类型、事件处理函数，默认为false
            if(obj.addEventListener) {
                obj.addEventListener(type, fn.bind(this), false); //非IE
            } else {
                obj.attachEvent('on' + type, fn.bind(this)); //ie,这里已经加上on，传参的时候注意不要重复加了
            };
        },
        hasClass: function(el, cls) {
            return el.className.match(new RegExp('(\\s|^)(' + cls + ')(\\s|$)'));
        },
        userFormSubmit: function() {
            var user = document.getElementById("user"),
                phone = document.getElementById("phone"),
                btnsubmit=document.getElementById("submit-from"),
                maskLayer=document.getElementById("mask-layer"),
                closeMask=document.getElementsByClassName('close-mask'),
                fontoutForm=document.getElementById("fontout-form"),
                quantity=document.getElementById("quantity"),
                isReal=true,
                _this=this;
            btnsubmit.onclick = function() { //提交表单
                var userVal=user.value,
                    phoneVal = phone.value,
                    quantityVal=quantity.value;
                if(userVal == "" || userVal == null) {
                    this.showMsg("请输入您的姓名！");
                    user.focus();
                } else if(!(/^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]{2,6}$/.test(userVal))) {
                    this.showMsg("姓名为2~6个字符！");
                    user.focus();
                }else if(phoneVal == "" || phoneVal == null) {
                    this.showMsg("请输入手机号！");
                    phone.focus();
                }else if(phoneVal.length !== 11) {
                    this.showMsg("手机号码格式不对<br />请输入11位手机号码！");
                    phone.focus();
                } else if(quantityVal == "" || quantityVal == null) {
                    this.showMsg("请输入预定数量！");
                    quantity.focus();
                }else {
                    oRequest.ajax({
                        type:'POST',
                        url: oRequest.apiUrl+'add',
                        data: {
                            name:userVal,
                            tel:phoneVal,
                            num:quantityVal
                        },
                        dataType: 'json',
                        success: function(result) {
                            if(result.code===2001){
                                _this.showMsg('提交成功,我们设计师会尽快联系您!');
                                user.value='';
                                phone.value='';
                                quantity.value='';
                                fontoutForm.style.display='block';
                                maskLayer.style.display='none';
                                /*setTimeout(function(){
                                    window.location.reload();
                                },2000)*/
                            }else{
                                result.msg && _this.showMsg(result.msg);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr)
                        }
                    });
                    if(isReal){
                        oRequest.countViews(3);
                        isReal=!isReal;
                    }
                }
            }.bind(this);
            /*for (var i=0;i<closeMask.length;i++) {
                (function(e){
                    closeMask[e].onclick=function(){
                        if(maskLayer.style.display==='block') maskLayer.style.display='none';
                    }
                })(i)
            }*/
        },
        showMsg: function(msg) {
            this.tipTime && clearTimeout(this.tipTime);
            const formMsg = document.getElementById("form-msg");
            if(!formMsg) {
                const sDiv = document.createElement('div');
                sDiv.id = 'form-msg';
                sDiv.className = 'tc form-msg';
                sDiv.innerHTML = "<div layout-align='center center' layout>" + msg + "<div>";
                //			sDiv.setAttribute('layout-align','center center');
                //			sDiv.setAttribute('layout','');
                document.getElementsByTagName("body")[0].appendChild(sDiv);
            } else {
                formMsg.style.display = 'block';
                formMsg.innerHTML = "<div layout-align='center center' layout>" + msg + "<div>";
            }
            this.tipTime = setTimeout(function() {
                document.getElementById("form-msg").style.display = 'none';
            }, 1000);
        },
    }).init();
    function getFontGroup () {
        return takeFont;
    }
    var id=getQueryString('id');
    $.ajax({
        type:'GET',
        data:{'id':id},
        url:'/tools/qcode/get_code',
        success:function (res) {
            console.log(res)
            if(res.code ==0){
                $('#qcode>img').attr('src','/'+res.data.qcode)
             }else{
                //alert('获取二维码失败')
                $('#qcode>img').attr('src','/public/static/images/tools/qcode/wxy.png')
            }
        }
    })
    //获取url参数
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }
    oRequest.ajax({
        url: "http://t.j9zz.com/api/wechat/share",
        dataType: "json",
        data: {
            share_url: encodeURIComponent(location.href.split("#")[0])
        },
        success: function(e) {
            console.log(e), wx.config(e);
            var t = {
                title: "丁酉鸡年·茅台镇大坛佳酿,10坛就能定制!",
                desc: "匠心打造，大师酿造！定制好礼，彰显个性！",
                link: "http://t.j9zz.com/special/fontout/index.php?id="+id,
                imgUrl: "http://t.j9zz.com/special/fontout/images/slide2-img.jpg",
                trigger: function(e) {},
                success: function(e) {},
                cancel: function(e) {}
            };
            wx.ready(function() {
                wx.onMenuShareAppMessage(t), wx.onMenuShareTimeline(t), wx.onMenuShareQQ(t), wx.onMenuShareWeibo(t)
            })
        }
    });
</script>
</body>
</html>