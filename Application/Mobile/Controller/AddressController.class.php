<?php

namespace Mobile\Controller;

use Common\Model\UserAddressModel;

class AddressController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 地址列表
     */
    public function addressList()
    {
        $user_id = $this->user['user_id'];
        $data = D("Common/UserAddress")->relation(true)->where(['user_id' => $user_id])->order(['is_default' => 'desc'])->select();
//        p($data);
        $this->assign('list', $data);
        $this->display('addressList');
    }

    /**
     * 添加新地址
     */
    public function addAddress()
    {
        if (IS_POST) {
            $data = I('post.');
//            $this->error('操作失败,' );
//            dd($data);
            $model = new UserAddressModel();
            $res = $model->addAddress($data, $this->user_id);
            if ($res['status'] == 1) {
                if ($data['org'] == 2) {
                    $this->success('操作成功', U('Cart/confirmCart', ['address_id' => $res['address_id']]));
                    return false;
                }
                $this->success('操作成功', U('address/addressList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display("addressInfo");
        }
    }

    /**
     * 修改新地址
     */
    public function editAddress()
    {
        if (IS_POST) {
            $data = I('post.');
            $model = new UserAddressModel();
            $res = $model->editAddress($data, $this->user_id);
            if ($res['status'] == 1) {
                if ($data['org'] == 2) {
                    $this->success('操作成功', U('Cart/confirmCart', ['address_id' => $data['address_id']]));
                    return false;
                }
                $this->success('操作成功', U('address/addressList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $address_id = I("get.id", 0, 'intval');
            $info = M("UserAddress")->find($address_id);
            $this->assign('region', M('region')->getField('id, name_cn'));
            $this->assign('info', $info);
            $this->display('addressInfo');
        }
    }

    /**
     * 删除用户收货地址
     */
    public function delAddress()
    {
        $id = I("post.id", 0, 'intval');
        $model = new UserAddressModel();
        $res = $model->delAddress($id, $this->user_id);
        if ($res['status'] == 1) {
            $this->success('删除成功');
            exit;
        } else {
            $this->error('删除失败,' . $res['msg']);
        }
    }

    /**
     * 设置默认地址
     */
    public function setDefaultAddress()
    {
        $id = I('id', 0, 'intval');
        $model = new UserAddressModel();
        $res = $model->setDefaultAddress($id, $this->user_id);
        if ($res['status'] == 1) {
            $this->success('设置成功');
            exit;
        } else {
            $this->error('设置失败,' . $res['msg']);
        }
    }

}
