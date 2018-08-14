<?php

namespace Zfuwl\Model;

class UsersLevelModel extends CommonModel {

    protected $tableName = 'level';

    /**
     * 查询等级数据
     * @param array $where 查询条件
     * @param array $field 查询字段
     * @return mixed
     */
    public function selectAllUsersLevelList($where = array(), $field = array()) {
        $field = implode(',', $field);
        $where['statu'] = NORMAL_STATUS;

        $levelList = $this->where($where)->field($field)->select();

        return $levelList;
    }

}
