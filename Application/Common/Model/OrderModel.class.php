<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/26 0026
 * Time: 18:01
 */

namespace Common\Model;


use Think\Model\RelationModel;

class OrderModel extends RelationModel
{

    protected $_link = array(
//        'address'=>array(
//            'mapping_type'      => self::HAS_ONE,
//            'class_name'        => 'UserAddress',
//            // 定义更多的关联属性
//            'foreign_key'=>'address_id',
//            'mapping_key'=>'address_id',
////            'mapping_fields '=>['name'],
////            'as_fields'=>'picture'
//        ),

    );
    /**
     * 根据订单ID获取订单信息
     * @param $order_id
     * @return mixed
     */
    public function getOrderById($order_id)
    {
        $data = $this->where(['order_id'=>$order_id])->find();
        if($data['address_id']){// 获取地址信息
            // $address_data = (new UserAddressModel())->getAddressById($data['address_id']);
            if(!empty($address_data)){
                $data = array_merge($data,$address_data);
            }
        }
        return $data;
    }
}