<?php

namespace Zfuwl\Model;

class UsersModel extends CommonModel {

    /**
     * 查出会员数据
     * @param $where 查询条件
     * @param int $pageNum 查询多少条
     * @return array
     */
    public function selectAllUser($where, $sort_order, $pageNum = PAGE_LIMIT) {

        $count = $this->where($where)->count();
        $page = ajaxGetPage($count, $pageNum);

        $show = $page->show();
        $list = $this->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($sort_order)->select();
        return array('page' => $show, 'list' => $list);
    }

    /**
     * 查出会员信息
     * @param $where 查询条件
     * @param array $field  查询的字段
     * @param bool $isGetField  是否直接获取值
     * @return mixed
     */
    public function selectUsers($where, $field = array(), $isGetField = false) {
        $field = implode(',', $field);
        if ($isGetField) {
            return $this->where($where)->getField($field);
        }
        $list = $this->where($where)->field($field)->select();
        return $list;
    }

    /**
     * 更新会员信息
     * @param $data  要更新的数据
     * @return bool  更新状态
     */
    public function saveUserData($data) {
        $where = array('user_id' => $data['user_id']);
        unset($data['user_id']);

        return $this->where($where)->save($data);
    }

    /**
     * 根据帐号查询用户信息
     * @param $account 会员帐号
     * @param string $field 查询的字段
     * @param bool $isReturn 是否直接返回单个数据
     * @return mixed
     */
    public function getUserByAccount($account, $field = '', $isReturn = false) {
        $where = array('account' => $account);
        $info = $this->where($where)->field($field)->find();
        if ($field) {
            return $info[$field];
        }
        return $info;
    }

}
