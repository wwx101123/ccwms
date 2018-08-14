<?php

namespace Common\Model;

use Zfuwl\Model\CommonModel;

class UsersBranchModel extends CommonModel
{

    /**
     * 根据会员id查询接点信息
     * @param int $userId 会员id
     * @return array 接点信息
     */
    public function getBranchByUid($userId)
    {
        $where = [
            'uid' => $userId
        ];

        $branch = $this->where($where)->order('sy_num+0 asc')->find();

        return $branch;
    }
    /**
     * 根据id查询接点信息
     * @param int $id branch_id
     * @return array 接点信息
     */
    public function getBranchById($id)
    {
        $where = [
            'branch_id' => $id
        ];

        $branch = $this->where($where)->order('sy_num+0 asc')->find();

        return $branch;
    }


}
