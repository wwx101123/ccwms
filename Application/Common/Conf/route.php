<?php

return array(
    'URL_ROUTER_ON' => true,      // 开启路由
//    'URL_MODEL' => 2, //
    'URL_ROUTE_RULES' => array(
        'guanyuwomen' => 'Index/intro',
        'fukuanfangshi' => 'Index/payment',
        'lianxiwomen' => 'Index/contactUs',
        'new$'  => 'Article/index',
        '/^new_(\d+)$/'  => 'Article/index?cat_id=:1',
        '/^new_(\d+)_(\d+)$/'  => 'Article/index?cat_id=:1&p=:2 ',
        'new/:cat_id\d$'  => 'Article/index',
        'new/:cat_id\d/:p\d$'  => 'Article/index',
        '/^detail_(\d+)$/'  => 'Article/articleDetail?id=:1',
    ),
    'URL_HTML_SUFFIX' => '.html',  // URL伪静态后缀设置  默认为html  去除默认的 否则很多地址报错
);
?>