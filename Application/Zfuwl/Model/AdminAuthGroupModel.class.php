<?php

namespace Zfuwl\Model;

class AdminAuthGroupModel extends CommonModel {

    protected $tableName = 'auth_group';

    /**
     * 获取角色列表
     * @params $type=1有分页，2无分布全部数据
     */
    public function getGroupList($user_id = 0) {
        $where = array(
            'status' => array('neq', DEL_STATUS),
        );
        $count = $this->where($where)->count();
        $page = getPage($count, PAGE_LIMIT);
        if (!$user_id) {
            $list = $this->where($where)->limit($page->firstRow, $page->listRows)->order('is_manage DESC,id DESC')->select();
        } else {
            $where = array(
                'a.status' => 1,
            );
            $field = "a.*,b.uid";
            $join = "LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id=b.group_id AND b.uid={$user_id}";
            $list = $this->alias('a')->field($field)->join($join)->where($where)->order('a.is_manage DESC,a.id DESC')->select();
        }

        return array(
            'list' => $list,
            'page' => $page->show(),
        );
    }

    /**
     * 根据id查找角色信息
     * @param unknown $id
     */
    public function findGroup($id) {
        $where = array(
            'id' => $id,
            'status' => NORMAL_STATUS,
        );
        return $this->where($where)->find();
    }

    /**
     * 添加角色
     * @param $data 添加的数据
     * @return bool 添加结果
     */
    public function addGroup($data) {
        return $this->add($data) ? true : false;
    }

    /**
     * 更新角色信息
     * @param $data 修改的数据
     * @return bool 修改结果
     */
    public function editGroup($data) {
        $where = array('id' => $data['id']);
        unset($data['id']);
        return $this->where($where)->save($data);
    }

    /**
     * 根据id修改状态
     * @param unknown $id
     */
    public function changeResult($id, $status) {
        $where = array(
            'id' => $id,
        );
        return $this->where($where)->setField('status', $status);
    }

    /**
     * @description:删除用户
     * @param unknown $admin_id
     */
    public function delAuthGroup($id) {
        if ($id != 1) {
            $where = array(
                'id' => $id,
            );
            return $this->where($where)->delete();
        }else{
            return FALSE;
        }
    }

    /**
     * @description:角色分配权限
     * @param unknown $rule_ids
     * @param unknown $role_id
     * @param string $note 描述
     * @return Ambigous <boolean, unknown>
     */
    public function addAuthRule($rule_ids, $role_id, $note) {
        $where = array(
            'id' => $role_id,
        );
        $data = array(
            'rules' => $rule_ids,
            'note' => $note
        );
        return $this->where($where)->save($data);
    }

}
