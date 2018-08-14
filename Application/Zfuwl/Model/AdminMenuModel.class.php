<?php

namespace Zfuwl\Model;

class AdminMenuModel extends CommonModel {

    protected $tableName = 'auth_rule';

    /**
     * 显示菜单
     * @param unknown $num
     * @return multitype:unknown string
     */
    public function selectAllMenu($type = 1) {
        $where = array(
            'status' => array('neq', DEL_STATUS),
        );
        if ($type == 2) {
            unset($where['is_menu']);
        }
        return $this->where($where)->order('sort desc')->select();
    }

    /**
     * @description:添加菜单
     * @param unknown $data
     */
    public function addAdminMenu($data) {
        $menu_info = array(
            'menu_name' => $data['controller'] ? $data['controller'] . '/' . $data['action'] : '',
            'icon' => $data['menuicon'],
            'title' => $data['menuname'],
            'pid' => $data['pid'] ? $data['pid'] : 0,
            'is_menu' => $data['is_menu'],
        );
        return $this->add($menu_info);
    }

    /**
     * @description:查询是否已存在的opt
     */
    public function isExistOpt($controller, $action, $id = null) {
        $where = array(
            'menu_name' => $controller . '/' . $action,
            'status' => NORMAL_STATUS,
        );
        if ($id) {
            $where['id'] = array('neq', $id);
        }
        return $this->where($where)->find();
    }

    /**
     * @description:是否为二级菜单
     */
    public function isSecondaryMenu($id) {
        $where = array(
            'id' => $id,
        );
        return $this->where($where)->getField('pid') ? true : false;
    }

    /**
     * @description:编辑菜单
     */
    public function editAdminMenu($data) {
        $where = array(
            'id' => $data['id'],
        );
        $menu_info = array(
            'menu_name' => $data['controller'] ? $data['controller'] . '/' . $data['action'] : '',
            'icon' => $data['menuicon'],
            'title' => $data['menuname'],
        );
        unset($data['id']);
        return $this->where($where)->save($menu_info);
    }

    /**
     * @description:是否存在子菜单
     */
    public function isExistSonMenu($id) {
        $where = array(
            'pid' => $id,
            'status' => NORMAL_STATUS,
        );
        return $this->where($where)->find();
    }

    /**
     * @description:删除菜单
     * @param unknown $id
     */
    public function deleteAdminMenu($id) {
        $where = array(
            'id' => $id,
        );

        $data = array(
            'status' => DEL_STATUS,
        );
        return $this->where($where)->save($data);
    }

    /**
     * @description:根据id查询菜单信息
     * @param unknown $id
     * @return \Think\mixed
     */
    public function selectMenuById($id) {
        $where = array(
            'id' => $id,
        );
        return $this->where($where)->find();
    }

    /**
     * @description:查询三级菜单
     * @param unknown $id
     * @return \Think\mixed
     */
    public function selectOpt($id) {
        $where = array(
            'pid' => $id,
            'status' => NORMAL_STATUS,
        );
        return $this->where($where)->select();
    }

    /**
     * 根据规则id数组获取菜单
     * @param unknown $rules_arr
     */
    public function getMenus($rules_arr, $is_menu = 1) {
        $where = array(
            'id' => array('in', $rules_arr),
            'is_menu' => 1,
            'status' => 1,
        );
        return $this->where($where)->order('sort desc')->select();
    }

    /**
     * @description:查询菜单信息
     * @param unknown $name
     */
    public function selectMenuInfoByName($name) {
        $where = array(
            'menu_name' => $name,
            'status' => NORMAL_STATUS,
        );
        return $this->where($where)->find();
    }

    /**
     * 显示菜单
     * @param unknown $num
     * @return multitype:unknown string
     */
    public function selectAllSellerMenu($type = 1) {
        $where = array(
            'status' => array('neq', DEL_STATUS),
        );
        if ($type == 2) {
            unset($where['is_menu']);
        }
        return M('seller_auth_rule')->where($where)->order('sort desc')->select();
    }

}
