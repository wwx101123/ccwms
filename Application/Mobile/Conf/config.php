<?php
return array(

    'IS_SHORT'=>1,  //1为开启，0为不开启，默认的为不开启
    'URL_ASGIN'=>'new',
    //简短分页路由分隔符'/^c_(\d+)_(\d+)$/'以_开头，以'_'为默认分隔符
    'URL_SPLIT'=>'_',

    //'配置项'=>'配置值'
    'LOAD_EXT_CONFIG' => 'html', // 加载其他自定义配置文件 html.php
    'URL_HTML_SUFFIX' => 'html',
    'HTML_CACHE_ON' => true, // 开启静态缓存
    'HTML_CACHE_TIME' => 60, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(// 定义静态缓存规则
        //'静态地址'    =>     array('静态规则', '有效期', '附加规则'),
        'index:index' => array('{:module}_{:controller}_{:action}', ZFUWL_CACHE_TIME), // 首页静态缓存 3秒钟
        'index:intro' => array('{:module}_{:controller}_{:action}', ZFUWL_CACHE_TIME),
        'index:payment' => array('{:module}_{:controller}_{:action}', ZFUWL_CACHE_TIME),
        'index:contactUs' => array('{:module}_{:controller}_{:action}', ZFUWL_CACHE_TIME),
        'article:index' => array('{:module}_{:controller}_{:action}_{cat_id}_{p}', ZFUWL_CACHE_TIME),
        'article:articleDetail' => array('{:module}_{:controller}_{:action}_{id}', ZFUWL_CACHE_TIME),
    ),
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',
    'DEFAULT_CONTROLLER' => 'User', // 会员中心默认访问的控制器
    'DEFAULT_ACTION' => 'userIndex', // 会员中心默认访问的方法
);
