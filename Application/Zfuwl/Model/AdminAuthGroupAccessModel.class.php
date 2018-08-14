<?php

namespace Zfuwl\Model;

class AdminAuthGroupAccessModel extends CommonModel
{
    protected $tableName = 'auth_group_access';

    /**
     * 获取用户所有权限
     */
    public function getUserRules($user_id)
    {

        $where = array(
            'a.uid' => $user_id,
        );
        $join = 'LEFT JOIN __AUTH_GROUP__ b ON b.id=a.group_id';
        $rules = $this->alias('a')
            ->where($where)
            ->join($join)
            ->field('b.rules')
            ->select();

        if (!$rules) {
            return array();
        }

        $rules_str = '';
        foreach ($rules as $v) {
            $rules_str .= $v['rules'] . ',';
        }

        $rules_str = rtrim($rules_str, ',');

        $rules_arr = array_unique(explode(',', $rules_str));

        $admin_menu_model = new AdminMenuModel();
        $menus = $admin_menu_model->getMenus($rules_arr);


        $menus = get_column($menus, 2);

        //dump($menus);exit;
        return $menus;

    }

    /**
        * 根据用户查询角色
    */
    public function getUserGroupByUserId($userId)
    {
        $info = $this
            ->table('__ADMIN_USER__ as au')
            ->join('__AUTH_GROUP_ACCESS__ as aga on aga.uid = au.admin_id')
            ->join('__AUTH_GROUP__ as ag on ag.id = aga.group_id')
            ->find();

        return $info;
    }

    /**
     * 添加用户 角色
     */
    public function addUserGroupAccess($data)
    {
        return $this->add($data) ? true : false;
    }
}
