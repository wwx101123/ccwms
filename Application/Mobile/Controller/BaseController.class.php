<?php

namespace Mobile\Controller;

use Org\Util\Timer;
use Think\Controller;
use Zfuwl\Model\ArticleCatModel;
use Zfuwl\Model\ConfigModel;

class BaseController extends Controller {

    public $session_id;
    public $cateTrre = array();

    /*
     * 初始化操作
     */

    public function _initialize() {
        $str = file_get_contents('noShowIp.json');
        $ipArr = json_decode($str, true);
        $ips = getArrColumn($ipArr, 'ip');
        if (in_array(getIp(), $ips) && $str) {
//            header('Content-Type:text/html;Charset=UTF-8');
//            die;
        }

        $timestampcc = time();
        $cc_nowtime = $timestampcc;
        if (session('cc_lasttime')) {
            $cc_lasttime = $_SESSION['cc_lasttime'];
            $cc_times = $_SESSION['cc_times'] + 1;
            $_SESSION['cc_times'] = $cc_times;
        } else {
            $cc_lasttime = $cc_nowtime;
            $cc_times = 1;
            $_SESSION['cc_times'] = $cc_times;
            $_SESSION['cc_lasttime'] = $cc_lasttime;
        }
        if (($cc_nowtime - $cc_lasttime) < 2) {//3秒内刷新5次以上可能为cc攻击
            if ($cc_times >= 10) {
                $i = count($ipArr);
                $ipArr[$i]['time'] = time();
                $ipArr[$i]['ip'] = getIp();
                $str = json_encode($ipArr);
                file_put_contents('noShowIp.json', $str);
                die;
            }
        } else {
            $cc_times = 0;
            $_SESSION['cc_lasttime'] = $cc_nowtime;
            $_SESSION['cc_times'] = $cc_times;
        }
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID', $this->session_id); //将当前的session_id保存为常量，供其它方法调用
        // 判断当前用户是否手机
        //    if (isMobile())
        //     header("location:" . U('Mobile/Login/index'));
//            header("location:" . U('Mobile'.$_SERVER['REQUEST_URI']));
//        else
//            cookie('is_mobile', '0', 3600);

        $w = intval(date('w')) ? intval(date('w')) : 7;
        if (zfCache('loginInfo.monday_' . $w) != 1) {
            setcookie('user_id', '', time() - 3600, '/');
            unset($_SESSION['user']);
            $this->error('loginInfo.kgcontent');
        } elseif (zfCache('loginInfo.monday_' . $w . '_add') > date('H')) {
            setcookie('user_id', '', time() - 3600, '/');
            unset($_SESSION['user']);
            $this->error(zfCache('loginInfo.kgcontent'));
        } elseif (zfCache('loginInfo.monday_' . $w . '_out') < date('H')) {
            setcookie('user_id', '', time() - 3600, '/');
            unset($_SESSION['user']);
            $this->error(zfCache('loginInfo.kgcontent'));
        }
        $this->publicAssign();
    }

    /**
     * 保存公告变量到 smarty中 比如 导航
     */
    protected function publicAssign() {
        $configModel = new ConfigModel();
        $config = array();
        $configList = $configModel->selectAll();
        foreach ($configList as $k => $v) {
            $config[$v['inc_type'] . '_' . $v['name']] = $v['value'];
        }
        $this->assign('config', $config);
    }

}
