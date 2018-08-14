<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启数据亚索
define ( "GZIP_ENABLE", function_exists ( 'ob_gzhandler' ) );
ob_start ( GZIP_ENABLE ? 'ob_gzhandler' : null );

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

# 诺一链数据库
define('NUOYILIANNAME', 'gp008_jiafuw');
define('PTVAL', 2);
define('PTVALFORNEXT', 1);

//  定义插件目录
define('PLUGIN_PATH','plugins/');

// 编辑器图片上传路径
define('UPLOAD_PATH','Public/upload/');

define('NORMAL_STATUS', 1); // 正常状态
define('DEL_STATUS', 0); // 删除状态
define('PAGE_LIMIT', 10); // 默认分页

// 缓存时间  31104000
define('ZFUWL_CACHE_TIME',600);

// 网站域名
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']);

// 静态缓存文件目录，HTML_PATH可任意设置，此处设为当前项目下新建的html目录
define('HTML_PATH','./Application/Runtime/Html/');

// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
