<?php

namespace Zfuwl\Model;

use Zfuwl\Model;

class AdminUserModel extends CommonModel {

    protected $tableName = 'admin_user';

    /**
     * 查询用户
     * @param  [array or string] $where [查询条件]
     */
    public function findUser($where) {
        $result = $this->where($where)->find();

        return $result;
    }

    /**
     * 更具id修改会员最后一次登录信息
     * @param  [number] $id [管理员id]
     * @return [bool]       [修改状态]
     */
    public function updateLastTime($id) {
        $where = array('admin_id' => $id);
        $data = array(
            'last_ip' => getIP(),
            'last_login' => time(),
            'session_id' => session_id(),
        );

        return $this->where($where)->save($data);
    }

    /**
     * @param number $num 每页显示数目
     * @return [array] 会员数据
     */
    public function selectAllUser($num = PAGE_LIMIT) {
        $where = array(
            'status' => array('neq', DEL_STATUS),
        );
        $count = $this->where($where)->count();
        $page = getPage($count, $num);
        $show = $page->show();
        $list = $this->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();

        return array('page' => $show, 'list' => $list);
    }

    /**
     * 添加后台用户
     * @param unknown $data
     * @return boolean
     */
    public function addAdminUser($data) {
        return $this->add($data) ? true : false;
    }

    /**
     * 更新用户信息
     * @param unknown $data
     * @return boolean
     */
    public function editAdminUser($data) {
        $where = array(
            'admin_id' => $data['admin_id'],
        );
        unset($data['id']);
        return $this->where($where)->save($data);
    }

    /**
     * @description:删除用户
     * @param unknown $admin_id
     */
    public function delAdminUser($admin_id) {
        $where = array(
            'admin_id' => $admin_id,
        );
        return $this->where($where)->delete();
    }

    /**
     * @description:根据id查询用户
     */
    public function findAdminUserById($admin_id) {
        $where = array(
            'admin_id' => $admin_id,
            'status' => NORMAL_STATUS,
        );
        return $this->where($where)->find();
    }

    /**
     * @description:根据帐号查询用户
     */
    public function findAdminUserByName($user_name) {
        $where = array(
            'user_name' => $user_name,
            'status' => NORMAL_STATUS,
        );
        return $this->where($where)->find();
    }

}
