<?php

return array(
    'LOAD_EXT_FILE' => 'common,type,sms,check,bochu,money,branch,goods,block,trade',
    //'配置项'=>'配置值'
    'URL_MODEL' => '2', // 去掉后面 index.php
    'MODULE_ALLOW_LIST' => array(
        'Zfuwl', 'Mobile'
    ),
    'LOAD_EXT_CONFIG' => array('db'),
//    'LOAD_EXT_CONFIG' => 'route',
    'SESSION_AUTO_START' => true, //启动session 不启动改为false
    'SHOW_PAGE_TRACE' => false, //显示调试信息
    'ERROR_PAGE' => '/404.html', // 404页面
    'TOKEN_ON' => true, // 是否开启令牌验证 默认关闭
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true'TAGLIB_LOAD' => true,
    'APP_AUTOLOAD_PATH' => '@.TagLib',
    'TAGLIB_BUILD_IN' => 'cx,zfuwl', //  自定义标签类名称
    'DEFAULT_MODULE' => 'Mobile', // 默认模块
    'DEFAULT_CONTROLLER' => 'User', // 默认控制器名称
    'DEFAULT_ACTION' => 'userIndex', // 默认操作名称
    'alipay_config' => array(
        'partner' => '2088911641070133', //这里是你在成功申请支付宝接口后获取到的PID；
        'key' => 'n1scf21of3gxdb94f4prjxdnymab03nu', //这里是你在成功申请支付宝接口后获取到的Key
        'sign_type' => strtoupper('MD5'),
        'input_charset' => strtolower('utf-8'),
        'cacert' => getcwd() . '\\cacert.pem',
        'transport' => 'http',
    ),
    //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；
    'alipay' => array(
        //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
        'seller_email' => 'admin@taojfu.com',
//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
        'notify_url' => 'http://www.xxx.com/Pay/notifyurl',
//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
        'return_url' => 'http://www.xxx.com/Pay/returnurl',
//支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
        'successpage' => 'User/myorder?ordtype=payed',
//支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
        'errorpage' => 'User/myorder?ordtype=unpay',
    ),
);
