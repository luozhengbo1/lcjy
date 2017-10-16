var that;
$('.delete_box').on('click',function(){
    $(this).children('.delete_up').css(
        {
            transition :'all 1s',
            'transformOrigin':"0 5px" ,
            transform :'rotate(-30deg) translateY(2px)'
        }

    )
    $('.jd_win').show();
    that = $(this);
})

$('.cancle').on('click',function(){
    $('.jd_win').hide();
    $('.delete_up').css('transform','none')
})
$('.submit').on('click',function(){
    that.parent().parent().parent().parent().remove();
    $('.jd_win').hide();
})

//设置为默认地址
function setdefault(_id) {

    $.ajax({
        url: '/wechat/user/setDefaultAddress',
        type: "PUT",
        data:{'id':_id},
        success: function (info) {
            if (info.code == 2001) {
                $(this).children('.one>img').attr('src','__IMAGES__/icons/checked.png');
                setTimeout(function () {

                    window.location.href=window.location.href+"?rand="+Math.floor(Math.random()*50);

                }, 1000);
            }
            layer.msg(info.msg)
        }
    });
}


// 删除地址
function del(_id) {

    $.ajax({
        url: '/wechat/user/deleteAddress',
        type: "get",
        data:{'id':_id},
        success: function (info) {
            if (info.code === 2017) {
                setTimeout(function () {

                    window.location.href=window.location.href+"?rand="+Math.floor(Math.random()*50);
                }, 1000);
            }
            layer.msg(info.msg)
        }
    });
}

