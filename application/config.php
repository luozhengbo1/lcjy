<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 站点设置
    // +----------------------------------------------------------------------
    'web' =>[
        'sitename' => '酱酒智造™后台管理系统',
        'siteurl'  => 'http://t.j9zz.com',
        'author'   => 'Demo.Ran@icloud.com',
        'version'  => 'v1.0.0',

        'dev_version' => 'v1.0.3',
    ],
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'htmlspecialchars',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // auth配置
    'auth'                   => [
        // 权限开关
        'auth_on'           => 1,
        // 认证方式，1为实时认证；2为登录认证。
        'auth_type'         => 1,
        // 用户组数据不带前缀表名
        'auth_group'        => 'auth_group',
        // 用户-用户组关系不带前缀表
        'auth_group_access' => 'auth_group_access',
        // 权限规则不带前缀表
        'auth_rule'         => 'auth_rule',
        // 用户信息不带前缀表
        'auth_user'         => 'admin_user',
    ],

    // 全站加密密钥（开发新站点前请修改此项）
    'salt'                   => '1dFlxLhiuLqnUZe9kA',

    // 验证码配置
    'captcha'                => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 32,
        // 是否画混淆曲线
        'useCurve' => true,
        // 验证码位数
        'length'   => 4,
        // 验证成功后是否重置
        'reset'    => true
    ],

    // +----------------------------------------------------------------------
    // | 微信设置
    // +----------------------------------------------------------------------
    'wechat' => [
        'debug' => false,
        'app_id' => 'wx774bccff0312978f',
        'secret' => '7e44b712a9735a6de6c8446825a2f49a',
        'token' => 'lcjj2017',
        'log' => [
            'level' => 'debug',
            'file' => 'runtime/log/easywechat.log',
        ],
        'oauth' => [
            'scopes' => ['snsapi_userinfo'],
            'callback' => '/wechat/user/callback',
        ],
        'payment' => [
//            'merchant_id'        => '1485409702',
            'mch_id'        => '1485409702',
            'key'                => 'Gzlcjyjt20170725Liaoliutaocaozuo',
            'cert_path'          => '/extend/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path'           => '/extend/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
            'notify_url'         => 'http://t.j9zz.com/wechat/callback/index',       // 你也可以在下单时单独设置来想覆盖它

        ],
    ],

    // +----------------------------------------------------------------------
    // | 短信设置
    // +----------------------------------------------------------------------
    'aliyunsms' => [
        'accessId' => 'LTAIzHqvb2YF4hXx',
        'accessKey' => 'ufFkiUN9zXvXGMHdtkPWbHV7RUkrzY',
        'templateId' =>'SMS_78605132',
        'signName' => '酱酒智造',
        'token' => '3f7d2271bd621b57'  //用户接口签名判断，md5(lcjj)
    ],
/*    'aliyunsms' => [
        'accessId' => 'yDDUJ2LVAbjHf8rb',
        'accessKey' => '7ZsEXqtqfgluzKHgD8W9xPLAgqChcC',
        'endPoint' => 'http://1457205051244089.mns.cn-hangzhou.aliyuncs.com',
        'topicName' => 'lvyou-sms-topic1',
        'signName' => '摄图数据',
        'token' => '3f7d2271bd621b57'  //用户接口签名判断，md5(lcjj)
    ],*/


    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'              => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 手机模板开启
    'mobile_theme'          => false,

    // 视图输出字符串内容替换
    'view_replace_str'      => [
        '__UPLOAD__' => '/public/uploads',
        '__STATIC__' => '/public/static',
        '__IMAGES__' => '/public/static/images',
        '__JS__'     => '/public/static/js',
        '__CSS__'    => '/public/static/css',
    ],

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'   => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'        => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'         => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'        => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'      => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'   => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache' => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 1800,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'  => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'   => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate' => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    // +----------------------------------------------------------------------
    // |钉钉配置信息
    // +----------------------------------------------------------------------
    'ddconfig' 	=>  [
        'corpid' 			    =>'ding38872dd0a69908aa35c2f4657eb6378f',//企业id
        'corpsecret'			=>'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv',//企业应用的凭证密钥
        'ssosecret'				=>'sTNu4D_bAW30LLtPqPQtm0Xeo4T0WW6J84XbdQtwu3QAMvz9RT-U0Ey0zF0dPRtS',//后台管理应用密匙
        'agentid'				=>'122700771 ',//微应用id	-- 云消息

        'appid' 				    =>'dingoa70ok7bqooyhq9ttl',//开放应用id
        'appsecret'			    =>'dBr-XDEudfYd-GA2ie3X-MIvxOgC-A6Vx1EBTHq_iO6T6o2scWqpZtKcBf7mh3QZ',//开放应用密匙

        'suite_key'             =>'suitezsnwbtva3snuqpnb',// suite key 套件key
        'suite_secret'         =>'IZPQLj570_fcTr55e1olqeq6SUxKaT1lsvkFdn1j_P2S6Si1SF_PRGYFYh1rRatU', // 套件 suite secret

        'get_sns_token_url'  =>'https://oapi.dingtalk.com/sns/gettoken',//开放应用获取token url
        'get_persistent_code'	=>'https://oapi.dingtalk.com/sns/get_persistent_code',
        'get_sns_token'		=>'https://oapi.dingtalk.com/sns/get_sns_token',//获取用户授权的SNS_TOKEN
        'sns_getuserinfo'		=>'https://oapi.dingtalk.com/sns/getuserinfo',//获取授权用户信息

        //请求参数url
        'gettoken_url'  		=>'https://oapi.dingtalk.com/gettoken',
        'get_sso_token_url' 	=>'https://oapi.dingtalk.com/sso/gettoken',//后台免登token
        'get_jsapi_ticket'		=>'https://oapi.dingtalk.com/get_jsapi_ticket',//获取jsapi_ticket

        'department_list'		=>'https://oapi.dingtalk.com/department/list',//获取部门列表
        'department_list_detail'=>'https://oapi.dingtalk.com/department/get',//获取部门列表(详细信息)
        'user_simplelist'		=>'https://oapi.dingtalk.com/user/simplelist',//获取部门成员列表
        'user_list'				=>'https://oapi.dingtalk.com/user/list',//获取部门成员列表(带详细信息)
        'user_getinfo' 		=>'https://oapi.dingtalk.com/user/get',//通过用户id获取用户信息
        'user_getinfo_by_code'	=>'https://oapi.dingtalk.com/user/getuserinfo',//通过code获取用户信息
        'get_userid_by_unionid'=>'https://oapi.dingtalk.com/user/getUseridByUnionid', // 通过unionid 获取userid
        'get_admin'             =>'https://oapi.dingtalk.com/user/get_admin', // 获取管理员列表
        //微应用url
        'microapp_visible_scopes' 	=>'https://oapi.dingtalk.com/microapp/visible_scopes',//微应用可见范围
        'microapp_create'	=>'https://oapi.dingtalk.com/microapp/create',//创建微应用

        //群会话接口
        'chat_create'			=>'https://oapi.dingtalk.com/chat/create',//群会话创建接口
        'chat_update'			=>'https://oapi.dingtalk.com/chat/update',//群会话修改接口
        'chat_get'				=>'https://oapi.dingtalk.com/chat/get',//群会话获取接口
        'chat_send'			=>'https://oapi.dingtalk.com/chat/send',//群会话获取接口

        /*****会话消息接口*****/
        'message_send'		=>'https://oapi.dingtalk.com/message/send',//发送企业会话消息
        'media_upload'		=>'https://oapi.dingtalk.com/media/upload',//上传文件

    ],
];
