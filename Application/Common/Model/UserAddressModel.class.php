<?php

namespace Common\Model;

use Think\Model\RelationModel;

class UserAddressModel extends RelationModel
{
    protected $_link = array(
        'province_region'=>array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'Region',
            // 定义更多的关联属性
            'foreign_key'=>'id',
            'mapping_key'=>'province',
            'as_fields'=>'name_cn:province_name'
        ),
        'city_region'=>array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'Region',
            // 定义更多的关联属性
            'foreign_key'=>'id',
            'mapping_key'=>'city',
            'as_fields'=>'name_cn:city_name'
        ),
        'district_region'=>array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'Region',
            // 定义更多的关联属性
            'foreign_key'=>'id',
            'mapping_key'=>'district',
            'as_fields'=>'name_cn:district_name'
        ),
        'twon_region'=>array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'Region',
            // 定义更多的关联属性
            'foreign_key'=>'id',
            'mapping_key'=>'twon',
            'as_fields'=>'name_cn:twon_name'
        ),
    );

    /**
     * 获取所有的收货地址
     */
    public function getAddressAll($user_id,$oreder=5)
    {
        $order['is_default'] ='desc';
//        $order['id'] ='desc';
        $data = $this->where(['user_id'=>$user_id])->relation(true)->order($order)->select();
        return $data;
    }

    /**获取单条 地址数据
     * @param $address_id
     * @return mixed
     */
    public function getAddressById($address_id,$user_id=0)
    {
        if($user_id)
            $condition['user_id'] = $user_id;
        $condition['address_id'] = $address_id;
        return $this->where($condition)->relation(true)->find();
    }

    /**获取单条 地址数据
     * @param $address_id
     * @return mixed
     */
    public function getDefaultAddressByUserId($user_id)
    {
        $data = $this->where(['user_id'=>$user_id,'is_default'=>2])->relation(true)->find();
        if(empty($data)){
            $data = $this->where(['user_id'=>$user_id])->relation(true)->find();
        }
        return $data;
    }

    /**
     * 修改地址
     * @param type $post
     * @param type $user_id
     * @return type
     */
    public function editAddress($post, $user_id = '') {
        $user = getUserInfo($user_id);
        if (!$user) {
            return array('status' => -1, 'msg' => '会员账号不存在');
        } else {
            $data = array(
                'province' => $post['province'],
                'city' => $post['city'],
                'district' => $post['district'],
                'twon' => $post['twon'],
                'address' => $post['address'],
                'consignee' => $post['username'],
                'mobile' => $post['mobile'],
//                'is_default' => $post['is_default'],
                'user_id' => $user_id
            );
            $res = M('UserAddress')->where("address_id = {$post['address_id']}")->save($data);
            if (false !== $res) {
                $this->postEditDefault($post['is_default'],$user_id,$post['address_id']);// 修改默认地址
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                return array('status' => -1, 'msg' => '操作失败');
            }
        }
    }

    /**
     * 删除用户地址
     * @param $address_id
     * @param $user_id
     * @return array
     */
    public function delAddress($address_id,$user_id)
    {
        $condition['user_id'] = $user_id;
        $condition['address_id'] = $address_id;
        if(false !== M("UserAddress")->where($condition)->delete()){
            return array('status' => 1, 'msg' => '操作成功');
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

    /**
     * 设置 未默认地址
     * @param $address_id
     * @param $user_id
     * @return array
     */
    public function setDefaultAddress($address_id,$user_id)
    {
        $condition['user_id'] = $user_id;
        $condition['address_id'] = $address_id;
        $model = M("UserAddress");
        $model->where(['user_id'=>$user_id])->setField('is_default',1);
        if(false !== $model->where($condition)->setField('is_default',2)){
            return array('status' => 1, 'msg' => '操作成功');
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

    /**
     * 用户添加收货地址
     * @param $post
     * @param $user_id
     * @return array
     */
    public function addAddress($post,$user_id)
    {
        $user = getUserInfo($user_id);
        if (!$user) {
            return array('status' => -1, 'msg' => '会员账号不存在');
        }
        if($this->where(['user_id'=>$user_id])->count()>19){
            return array('status' => -1, 'msg' => '最多只能添加20条地址');
        }
        if($post['province'] <= 0 || $post['city'] <= 0 || $post['district'] <= 0) {
            return array('status' => -1, 'msg' => '请选择地址');
        }
        if($post['address'] == '') {
            return array('status' => -1, 'msg' => '请输入详细地址');
        }
        if(!checkMobile($post['mobile'])) {
            return array('status' => -1, 'msg' => '请输入正确的手机号');
        }
        if($post['username'] == '') {
            return array('status' => -1, 'msg' => '请输入收货人姓名');
        }
        $data = array(
            'province' => $post['province'],
            'city' => $post['city'],
            'district' => $post['district'],
            'twon' => $post['twon'],
            'address' => $post['address'],
            'consignee' => $post['username'],
            'mobile' => $post['mobile'],
            'user_id' => $user_id
        );

        $res = $this->add($data);
        if ($res) {
            $this->postEditDefault($post['is_default'],$user_id,$res);// 修改默认地址
            return array('status' => 1, 'msg' => '操作成功','address_id'=>$res);
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

    /**
     * post 提交 是否设置为 默认地址
     * @param $is_default
     * @param $user_id
     * @param int $address_id
     */
    private function postEditDefault($is_default,$user_id,$address_id=0){
        if($is_default == 2){
            $this->setDefaultAddress($address_id,$user_id);
        }else{
            $this->where(['address_id'=>$address_id])->setField('is_default',1);
        }
    }


}