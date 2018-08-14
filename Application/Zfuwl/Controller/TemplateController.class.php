<?php

namespace Zfuwl\Controller;

use Think\Controller;

class TemplateController extends CommonController {

    public function mobileIndex() {
        $t = $m = 'Mobile';
        $arr = scandir("./Template/$t/");
        foreach ($arr as $key => $val) {
            if ($val == '.' || $val == '..')
                continue;
            $template_config[$val] = include "./Template/$t/$val/config.php";
        }
        $this->assign('t', $t);
        $template_arr = include("./Application/$m/Conf/html.php");
        $this->assign('default_theme', $template_arr['DEFAULT_THEME']);
        $this->assign('template_config', $template_config);
        $this->display('mobileIndex');
    }

    public function mobileChange() {
        $t = $m = 'Mobile';
        if (!is_writeable("./Application/$m/Conf/html.php"))
            return "文件/Application/$m/Conf/html.php不可写,不能启用模版,请修改权限!!!";

        $config_html = "<?php
		return array(
			'VIEW_PATH'       =>'./Template/$t/', // 改变某个模块的模板文件目录
			'DEFAULT_THEME'    =>'{$_GET['key']}', // 模板名称
			'TMPL_PARSE_STRING'  =>array(
                        '__STATIC__'     => '/Template/$t/{$_GET['key']}/Static', // 增加新的image  css js  访问路径  后面给 php 改了
			   ),
			);
		?>";
        file_put_contents("./Application/$m/Conf/html.php", $config_html);
        $this->success("操作成功!!!", U('Template/mobileIndex', array('t' => $t)));
    }

    public function memberIndex() {
        $t = $m = 'Member';
        $arr = scandir("./Template/$t/");
        foreach ($arr as $key => $val) {
            if ($val == '.' || $val == '..')
                continue;
            $template_config[$val] = include "./Template/$t/$val/config.php";
        }

        $this->assign('t', $t);
        $template_arr = include("./Application/$m/Conf/html.php");
        $this->assign('default_theme', $template_arr['DEFAULT_THEME']);
        $this->assign('template_config', $template_config);
        $this->display('memberIndex');
    }

    public function memberChange() {
        $t = $m = 'Member';
        if (!is_writeable("./Application/$m/Conf/html.php"))
            return "文件/Application/$m/Conf/html.php不可写,不能启用模版,请修改权限!!!";
        $config_html = "<?php
		return array(
			'VIEW_PATH'       =>'./Template/$t/', // 改变某个模块的模板文件目录
			'DEFAULT_THEME'    =>'{$_GET['key']}', // 模板名称
			'TMPL_PARSE_STRING'  =>array(
                        '__STATIC__'     => '/Template/$t/{$_GET['key']}/Static', // 增加新的image  css js  访问路径  后面给 php 改了
			   ),
			);
		?>";
        file_put_contents("./Application/$m/Conf/html.php", $config_html);
        $this->success("操作成功!!!", U('Template/memberIndex', array('t' => $t)));
    }

}
