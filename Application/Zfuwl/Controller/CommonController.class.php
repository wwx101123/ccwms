<?php

namespace Zfuwl\Controller;

use Think\Controller;
use Think\Auth;

class CommonController extends Controller {

    public function _initialize() {
        //测试
        header('Content-Type:text/html;Charset=UTF-8');
        $session_id = session_id(); // session id
        $this->assign('session_id', $session_id);
        define('SESSION_ID', $session_id);
        $admin_id = session('admin_id');
        if (!$admin_id) {
            $this->error('请先登录', U('Zfuwl/Login/login'));
        } else {
            $info = D('AdminUser')->findAdminUserById($admin_id);
//            if ($info['session_id'] != $session_id) {
//                unset($_SESSION['admin_id']);
//                $this->error('帐号已在其它地方登录！', U('Zfuwl/Login/login'));
//            }
            $name = CONTROLLER_NAME . '/' . ACTION_NAME;
            if (CONTROLLER_NAME != 'Index') { // 不需要判断权限
                $auth = new Auth();
                $auth_result = $auth->check($name, $admin_id);
                if ($auth_result === false) {
                    $this->error('没有权限!');
                }
            }
            $out_time = zfCache('securityInfo.admin_past_due_time') == '' ? 1000 : zfCache('securityInfo.admin_past_due_time') * 60;
            if (($_SESSION['admin_past_due_time'] + $out_time) <= time()) {
                unset($_SESSION['admin_id']);
                $this->error('身份已过期，请重新登录', U('Zfuwl/Login/login'));
            }
            session('admin_past_due_time', time());
            $this->admin_id = $admin_id;
            $this->assign('adminlist', M('admin_user')->cache('adminUser')->getField('admin_id,user_name'));
            $this->assign('languageType', languageType());
        }
        $this->public_assign();
    }

    /**
     * @description:ajax错误返回
     * @param string $msg
     * @param unknown $fields
     */
    protected function ajaxError($msg = '', $fields = array()) {
        header('Content-Type:application/json; charset=utf-8');
        $data = array('status' => 'error', 'msg' => $msg, 'fields' => $fields);
        echo json_encode($data);
        exit;
    }

    protected function ajaxSuccess($msg, $_data = array()) {
        header('Content-Type:application/json; charset=utf-8');
        $data = array('status' => 'success', 'msg' => $msg, 'data' => $_data);
        echo json_encode($data);
        exit;
    }

    /**
     * 管理员退出登录
     */
    public function logout() {
        unset($_SESSION['admin_id']);
        $this->success("退出成功", U('Zfuwl/Login/login'));
    }

    /**
     * 保存公告变量到 smarty中 比如 导航
     */
    protected function public_assign() {
        $config = array();
        $configList = D('config')->selectAll();
        foreach ($configList as $k => $v) {
            $config[$v['inc_type'] . '_' . $v['name']] = $v['value'];
        }
        $this->assign('config', $config);
    }

}
