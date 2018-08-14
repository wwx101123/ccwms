<?php
return array(
    'VIEW_PATH'       =>'./Template/Mobile/', // 改变某个模块的模板文件目录
    'DEFAULT_THEME'    =>'default', // 模板名称
    'TMPL_PARSE_STRING'  =>array(
        '__STATIC__'     => '/Template/Mobile/default/Static', // 增加新的image  css js  访问路径  后面给 php 改了
    ),
);
?>