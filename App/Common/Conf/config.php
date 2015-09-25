<?php
define('URL_CALLBACK', 'http://freelog.coding.io/user/callback?type=');

$config = array(
	//'配置项'=>'配置值'
    'URL_CASE_INSENSITIVE'=>true,
    'URL_REWRITE'=>2,
    'VAR_PAGE'=>'page',
    'DEFAULT_FILTER'=> 'addslashes,htmlspecialchars',
	/* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'freelog', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'fl_', // 数据库表前缀

    'PICTURE_UPLOAD_DRIVER'=>'Local',

    'FILE_UPLOAD_DRIVER'=>'Local',

    /* 文件上传相关配置 */
    'FILE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 1024*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,mp3,mp4', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/file/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,tmp', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    //自定义配置
    'comment_uid_youyan'=>'90040',

    //腾讯QQ登录配置
    'THINK_SDK_QQ' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'qq',
    ),

    //新浪微博配置
    'THINK_SDK_SINA' => array(
        'APP_KEY'    => '3073201486', //应用注册成功后分配的 APP ID
        'APP_SECRET' => 'b3a79ef47b060e58a7ac51dc7d8747ec', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'sina',
    ),

    //人人网配置
    'THINK_SDK_RENREN' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'renren',
    ),
    //360配置
    'THINK_SDK_X360' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'x360',
    ),
    //豆瓣配置
    'THINK_SDK_DOUBAN' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'douban',
    ),
    //Github配置
    'THINK_SDK_GITHUB' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'github',
    ),
    //淘宝网配置
    'THINK_SDK_TAOBAO' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'taobao',
    ),
    //百度配置
    'THINK_SDK_BAIDU' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'baidu',
    ),
    //微信登录
    'THINK_SDK_WEIXIN' => array(
        'APP_KEY'    => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'weixin',
    ),

    //微信相关
    'WEIXIN'=>array(
        'TOKEN'  => 'freelog',
        'APPID'  => 'wx72e0b206f3fb96af',
        'AESKEY' => 'PbYuJRJ1UKYzpZoH7PUA6MQySGaqiLTit0XveNPtWQ',
        'SECRET' => 'e733caf9234ed2ea1342e85366172513',
    ),
);

if(isset($_ENV['VCAP_SERVICES'])){
    $json = json_decode($_ENV['VCAP_SERVICES'], true);
    $db = $json['mysql'][0]['credentials'];
    $file_root = $json['filesystem-1.0']['credentials']['host_path'];
    $config['DB_HOST'] = $db['hostname'];
    $config['DB_NAME'] = $db['name'];
    $config['DB_USER'] = $db['username'];
    $config['DB_PWD'] = $db['password'];
    $config['DB_PORT'] = $db['port'];
    $config['FILE_UPLOAD']['rootPath'] = $file_root.'/file/';
    $config['PICTURE_UPLOAD']['rootPath'] = $file_root.'/picture/';
}

//支持cnpaas
if(stripos($_SERVER['HTTP_HOST'], 'cnpaas.io')){
    $config['DB_HOST'] = getenv('OPENSHIFT_MYSQL_DB_NAME');
    $config['DB_NAME'] = getenv('OPENSHIFT_MYSQL_DB_NAME');
    $config['DB_USER'] = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
    $config['DB_PWD'] = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');
    $config['DB_PORT'] = getenv('OPENSHIFT_MYSQL_DB_PORT');
}
return $config;