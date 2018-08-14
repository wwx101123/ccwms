<?php

namespace Zfuwl\Model;

class AdminBankModel extends CommonModel
{
    
    protected $tableName = 'admin_bank';

    /**
     * 添加收款方式
     * @param array $data 要添加的数据
     */
    public function addBank($data)
    {
        $bankData = array(
            'bank_name' => $data['bank_name'],
            'bank_account' => $data['bank_account'],
            'bank_opening' => $data['bank_opening'],
            'bank_img' => $data['bank_img'],
            'bank_qrcode_img' => $data['bank_qrcode_img']
        );

        return $this->add($bankData);
    }

    /**
     * 修改收款方式
     * @param array $data 要修改的数据
     */
    public function editBank($data)
    {
        $where = array(
            'bank_id' => $data['id']
        );
        $bankData = array(
            'bank_name' => $data['bank_name'],
            'bank_account' => $data['bank_account'],
            'bank_opening' => $data['bank_opening'],
            'bank_img' => $data['bank_img'],
            'bank_qrcode_img' => $data['bank_qrcode_img']
        );

        return $this->where($where)->save($bankData);
    }

    /**
     * 根据id查询收款方式
     */
    public function findBankById($bankId)
    {

        $where = array(
            'bank_id' => $bankId
        );

        return $this->where($where)->find();
    }
}