<?php

use think\Db;

/**
 * 获取分类所有子分类
 * @param int $cid 分类ID
 * @return array|bool
 */
function get_category_children($cid)
{
    if (empty($cid)) {
        return false;
    }

    $children = Db::name('category')->where(['path' => ['like', "%,{$cid},%"]])->select();

    return array2tree($children);
}

/**
 * 根据分类ID获取文章列表（包括子分类）
 * @param int $cid 分类ID
 * @param int $limit 显示条数
 * @param array $where 查询条件
 * @param array $order 排序
 * @param array $filed 查询字段
 * @return bool|false|PDOStatement|string|\think\Collection
 */
function get_articles_by_cid($cid, $limit = 10, $where = [], $order = [], $filed = [])
{
    if (empty($cid)) {
        return false;
    }

    $ids = Db::name('category')->where(['path' => ['like', "%,{$cid},%"]])->column('id');
    $ids = (!empty($ids) && is_array($ids)) ? implode(',', $ids) . ',' . $cid : $cid;

    $fileds = array_merge(['id', 'cid', 'title', 'introduction', 'thumb', 'reading', 'publish_time'], (array)$filed);
    $map = array_merge(['cid' => ['IN', $ids], 'status' => 1, 'publish_time' => ['<= time', date('Y-m-d H:i:s')]], (array)$where);
    $sort = array_merge(['is_top' => 'DESC', 'sort' => 'DESC', 'publish_time' => 'DESC'], (array)$order);

    $article_list = Db::name('article')->where($map)->field($fileds)->order($sort)->limit($limit)->select();

    return $article_list;
}

/**
 * 根据分类ID获取文章列表，带分页（包括子分类）
 * @param int $cid 分类ID
 * @param int $page_size 每页显示条数
 * @param array $where 查询条件
 * @param array $order 排序
 * @param array $filed 查询字段
 * @return bool|\think\paginator\Collection
 */
function get_articles_by_cid_paged($cid, $page_size = 15, $where = [], $order = [], $filed = [])
{
    if (empty($cid)) {
        return false;
    }

    $ids = Db::name('category')->where(['path' => ['like', "%,{$cid},%"]])->column('id');
    $ids = (!empty($ids) && is_array($ids)) ? implode(',', $ids) . ',' . $cid : $cid;

    $fileds = array_merge(['id', 'cid', 'title', 'introduction', 'thumb', 'reading', 'publish_time'], (array)$filed);
    $map = array_merge(['cid' => ['IN', $ids], 'status' => 1, 'publish_time' => ['<= time', date('Y-m-d H:i:s')]], (array)$where);
    $sort = array_merge(['is_top' => 'DESC', 'sort' => 'DESC', 'publish_time' => 'DESC'], (array)$order);

    $article_list = Db::name('article')->where($map)->field($fileds)->order($sort)->paginate($page_size);

    return $article_list;
}

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int $pid
 * @param int $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[] = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }

    return $list;
}

/**
 * 构建层级（树状）数组
 * @param array $array 要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid_name 父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid_name = 'pid', $child_key_name = 'children')
{
    $counter = array_children_count($array, $pid_name);
    if (!isset($counter[0]) || $counter[0] == 0) {
        return $array;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0) {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0) {
            array_push($array, $temp);
        } else {
            if ($temp[$pid_name] == 0) {
                $tree[] = $temp;
            } else {
                $array = array_child_append($array, $temp[$pid_name], $temp, $child_key_name);
            }
        }
        $counter = array_children_count($array, $pid_name);
    }

    return $tree;
}

/**
 * 子元素计数器
 * @param array $array
 * @param int $pid
 * @return array
 */
function array_children_count($array, $pid)
{
    $counter = [];
    foreach ($array as $item) {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }

    return $counter;
}

/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name)
{
    foreach ($parent as &$item) {
        if ($item['id'] == $pid) {
            if (!isset($item[$child_key_name]))
                $item[$child_key_name] = [];
            $item[$child_key_name][] = $child;
        }
    }

    return $parent;
}

/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name)
{
    $result = false;
    if (is_dir($dir_name)) {
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DS . $item)) {
                        delete_dir_file($dir_name . DS . $item);
                    } else {
                        unlink($dir_name . DS . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }

    return $result;
}

/**
 * 判断是否为手机访问
 * @return  boolean
 */
function is_mobile()
{
    static $is_mobile;

    if (isset($is_mobile)) {
        return $is_mobile;
    }

    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        $is_mobile = false;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
    ) {
        $is_mobile = true;
    } else {
        $is_mobile = false;
    }

    return $is_mobile;
}

/**
 * 手机号格式检查
 * @param string $mobile
 * @return bool
 */
function check_mobile_number($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    $reg = '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#';

    return preg_match($reg, $mobile) ? true : false;
}

//获取产品分类名称
function get_type_name($value)
{
    $result = \app\common\model\GoodsCate::where('id', $value)->value('name');
    return $result;
}


function getCurl($url)
{
    // Initialize curl
    $curl = curl_init();
    // Set the options
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, $url);
    // Execute the request
    $resp = curl_exec($curl);
    //close the connection
    curl_close($curl);
    return $resp;
}

function http_post_data($url, $data_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string))
    );
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();
    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return array($return_code, $return_content);
}

//获取微信Token
function get_wechat_token($appid = 'wx774bccff0312978f', $appsecret = '7e44b712a9735a6de6c8446825a2f49a')
{
    $condition = array('appid' => $appid, 'appsecret' => $appsecret);
    $access_token_model = new \app\common\model\AccessToken();
    $access_token_set = $access_token_model->where($condition)->find();//获取数据
    if ($access_token_set) {
        //检查是否超时，超时了重新获取
        if ($access_token_set['AccessExpires'] > time()) {
            //未超时，直接返回access_token
            return $access_token_set['access_token'];
        } else {
            //已超时，重新获取
            $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
            $json = json_decode(getCurl($url_get));
            $access_token = $json->access_token;
            $AccessExpires = time() + intval($json->expires_in);
            $data['access_token'] = $access_token;
            $data['AccessExpires'] = $AccessExpires;
            $result = $access_token_model->allowField(true)->where($condition)->update($data);//更新数据
            if ($result) {
                return $access_token;
            } else {
                return $access_token;
            }
        }
    } else {
        /*数据库中无$appid,$appsecret对应的记录需要再做处理，如插入到数据库
return 0;*/
    }
}

/*
 * 检查当前微信用户是否已经关注(for user)
 */
function check_openid($value = '32130')
{
    $model = new \app\common\model\User();
    if($model->where('openid',$value)->find()){
        return true;
    } else {
        return false;
    }
}
/*
 * 检查当前微信用户是否已经关注(for shangjia)
 */
function check_openid_shangjia($value = '32130')
{
    $model = new \app\common\model\Dealer();
    if($model->where('openid',$value)->find()){
        return true;
    } else {
        return false;
    }
}
/*
 * 获取后台用户名称
 */
function get_user_name($id)
{
    return \db('AdminUser')->where('id',$id)->value('username');
}

/*
 * 获取商品名称
 */
function get_goods_name($id)
{
    $info = \db('goods')->where('id',$id)->value('title');
    if(empty($info))
    {
        return '该商品已下架';
    } else {
        return $info;
    }
}

/*
 * 获取订单原浆类型
 */
function get_order_type($id)
{
    $model = new \app\common\model\GoodsType();
    $name = $model->where('id',$id)->value('name');
    return $name;
}

/*
 * 获取商品订单单价
 */
function get_order_unit($type_id)
{
    $model = new \app\common\model\GoodsType();
    $price = $model->where('id',$type_id)->value('price');
    return $price;
}

/*
 * 获取订单状态
 */
function get_order_status($value)
{
    $status = ['-1'=>'支付失败','0'=>'待支付','1'=>'生产中','2'=>'已发货','3'=>'已收货'];
    return $status[$value];
}

/*
 * 获取原浆类型（列表）
 */
function get_yuanjiang($value)
{
    $baseUrl = 'http://t.j9zz.com/';
    $data =  \app\common\model\GoodsType::where('id',$value)->find();

    $data['image'] = $baseUrl.$data['image'];
    return $data;
}

/**
 * 得到新订单号
 * @return  string
 */
function build_order_no()
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);

    return date('Ymd') . str_pad(mt_rand(11111, 99999), 5, '0', STR_PAD_LEFT);
}

/*
 * 查询用户的上级经销商ID
 */
function get_from_id($openid)
{
    $from = \app\common\model\User::where('openid',$openid)->value('from');
    if(is_null($from)){
        return '0';
    } else {
        return $from;
    }
}

/*
 * 获取收货地址
 */
function get_address($id)
{
    $address = \app\common\model\UserAddress::where('id',$id)->find();
    return $address;
}


/*
 * 远程下载图片到指定文件夹
 */
function getImage($url,$save_dir='',$filename='',$type=0){
    if(trim($url)==''){
        return array('file_name'=>'','save_path'=>'','error'=>1);
    }
    if(trim($save_dir)==''){
        $save_dir='./';
    }
    if(trim($filename)==''){//保存文件名
        $ext=strrchr($url,'.');
        if($ext!='.gif'&&$ext!='.jpg'&&$ext!='.png'&&$ext!='.jpeg'){
            return array('file_name'=>'','save_path'=>'','error'=>3);
        }
        $filename=time().rand(0,10000).$ext;
    }
    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return array('file_name'=>'','save_path'=>'','error'=>5);
    }
    //获取远程文件所采用的方法
    if($type){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$img);
    fclose($fp2);
    unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}


/*
 * 记录和查询微信openid来源
 */
function get_openid_from($openid)
{
    $model = new \app\common\model\UserQrfrom();
    $from = $model->where('openid',$openid)->value('from');
    
    //判断来源是否存在，存在则直接返回，不存在则添加当前来源（默认为0，即来源非分销用户处）
    return $from?$from:0;
}
/*
 * 添加用户来源
 */
function set_openid_from($openid,$from)
{
    $model = new \app\common\model\UserQrfrom();
    $model->openid = $openid;
    $model->from = $from;
    $model->save();
}

/*
 * 判断手机号码是否和后台商家用户绑定的手机号码一致
 */
function check_mobile_for_sj_register($tel)
{
    $model = new \app\common\model\Dealer();
    $result = $model->where('tel',$tel)->value('id');
    if($result){
        return true;
    } else{
        return false;
    }
}

/*
 * 检查当前经销商是否有上级经销商
 */
function check_dealer_id($id)
{
    $model = new \app\common\model\Dealer();
    $result = $model->where('id',$id)->value('fid');
    if($result){
        return $result;
    } else{
        return 0;
    }
}


/**
 * 通过openid获取userid
 * @param $openid
 */
function get_userid_by_openid($openid)
{
    $model = new \app\common\model\User();
    $userid = $model->where('openid',$openid)->value('id');
    return $userid;
}

/*
 * 获取分佣金比例
 */
function get_fenyong_percent($id)
{
    $model = new \app\common\model\Dealer();
    $result = $model->where('id',$id)->value('fenyong');
    if($result!=0){
        return $result;
    } else{
        return 0;
    }
}

/*
 * 佣金计算公式
 */
function get_fenyong_amount($fenyong=0,$amount)
{
    $result = $fenyong * $amount /100;
    return $result;
}

/** 检查商家用户是否绑定微信
 * @param string $openid
 * @return string
 */
function check_dealer_for_weixin($openid='')
{
    if(!empty($openid)){
        return '<span style="color: #2ca02c">已绑定</span>';
    } else{
        return '<span style="color: red">未绑定</span>';
    }
}

/** 获取分佣订单状态
 * @param $status
 * @return mixed
 */
function get_fenyong_status($status)
{
    $array = [
        1=>'<span style="color: #2ca02c">已支付</span>',
        2=>'<span style="color: red">已退款</span>',
        2=>'<span style="color: blue">已提现</span>'
    ];
    return $array[$status];
}

/** 获取商家名称
 * @param $id
 * @return mixed
 */
function get_dealer_name($id)
{
    $name = \app\common\model\Dealer::where('id',$id)->value('name');
    return $name;
}
/*
 * 通过openid获取商家用户id
 */
function get_dealerID_by_openid($openid)
{
    $id = \app\common\model\Dealer::where('openid',$openid)->value('id');
    return $id;
}


