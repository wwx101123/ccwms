<?php

namespace Zfuwl\Controller;

class AdminController extends CommonController {

    protected $adminUserModel;

    public function _initialize() {
        parent::_initialize();
        $this->adminUserModel = D('AdminUser');
    }

    /**
     * 角色列表
     */
    public function index() {
        $userList = $this->adminUserModel->selectAllUser();
        $this->assign('userList', $userList['list']);
        $this->assign('page', $userList['page']);
        $this->display();
    }

    /**
     * 添加用户
     */
    public function addUser() {
        if (IS_POST) {
            $userInfo = array(
                'user_name' => I('post.user_name', '', 'trim'),
                'password' => adminEncrypt(I('post.password', '', 'trim')),
                'email' => I('post.email', '', 'trim'),
                'mobile' => I('post.mobile', '', 'trim')
            );
            if ($this->adminUserModel->findAdminUserByName($userInfo['user_name'])) {
                $this->error('该用户已被占用!');
            }
            if (!checkMobile($userInfo['mobile'])) {
                $this->error('手机号格式不正确!');
            }
            if (!checkEmail($userInfo['email'])) {
                $this->error('邮箱格式不正确!');
            }

            if ($this->adminUserModel->addAdminUser($userInfo)) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        } else {

            $this->display('addUser');
        }
    }

    /**
     * 更新用户信息
     */
    public function editUser() {
        $admin_id = I('id', '', 'intVal');
        if (IS_POST) {
            $userInfo = array(
                'user_name' => I('post.user_name', '', 'trim'),
                'mobile' => I('post.mobile', '', 'trim'),
                'email' => I('post.email', '', 'trim'),
                'admin_id' => $admin_id
            );
            if (I('post.password')) {
                $userInfo['password'] = adminEncrypt(I('post.password', '', 'trim'));
            }
            $userNameInfo = $this->adminUserModel->findAdminUserByName($userInfo['user_name']);
            if ($userNameInfo && $userNameInfo['admin_id'] != $admin_id) {
                $this->error('该用户已被占用!');
            }
            if (!checkMobile($userInfo['mobile'])) {
                $this->error('手机号格式不正确!');
            }
            if (!checkEmail($userInfo['email'])) {
                $this->error('邮箱格式不正确!');
            }
            if ($this->adminUserModel->editAdminUser($userInfo)) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        } else {
            $userInfo = $this->adminUserModel->findAdminUserById($admin_id); // 根据id查出用户信息
            $this->assign('userInfo', $userInfo);
            $this->display('addUser');
        }
    }

    /**
     * 修改管理员状态
     */
    public function saveStatus() {
        if (IS_POST) {
            $val = I('val');
            $id = I('id');
            $res = M('AdminUser')->where(array('admin_id' => $id))->save(array('status' => $val));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    /**
     * 删除用户
     */
    public function delUser() {
        $admin_id = I('post.id', '', 'intval');
        $result = $this->adminUserModel->delAdminUser($admin_id);
        if ($result) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    public function logIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('admin_id') ? $condition['admin_id'] = $res = M('admin_user')->where(array('user_name' => trim(I('user_name'))))->getField('admin_id') : false;
            I('log_info') && $condition['log_info'] = array('like', '%' . trim(I('log_info') . '%'));
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['log_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('admin_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('admin_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'admin_id');
            if ($userIdArr) {
                $this->assign('adminList', M('admin_user')->where("admin_id in (" . implode(',', $userIdArr) . ")")->getField('admin_id,user_name'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('logIndexAjax');
            die;
        }
        $this->display('logIndex');
    }

    /**
     * 根据ip获取地址
     */
    public function checkAddressByIp() {
        $ip = I('ip');
        $this->success('ip: ' . $ip . '     地址: ' . getcposition($ip));
    }

    public function dellogIndex() {
        $where = array('id' => array('in', I('id')));
        $res = M('admin_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptylogIndex() {
        $db = M('admin_log');
        $dbconn = M();
        $tables = array(
            'admin_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
