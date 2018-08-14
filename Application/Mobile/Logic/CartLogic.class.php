<?php

namespace Member\Logic;

use Zfuwl\Logic\GoodsLogic;
use Think\Model\RelationModel;

class CartLogic extends RelationModel
{

    protected $tableName = 'cart';

    public $goodsLogic;

    public function __construct()
    {
        parent::__construct();
        $this->goodsLogic = new GoodsLogic();
    }

    /**
     * 加入购物车
     */
    public function addCart($user, $data)
    {
        $goodsId = $data['goods_id']; // 商品id
        $goodsNum = intVal($data['goods_num']); // 购买数量
        $goods = $this->goodsLogic->findDataByField('goods_id', $goodsId);
        if (!$goods || $goods['is_type'] != 1) {
            return array('status' => -1, 'msg' => '此商品不存在!');
        }

        if ($goodsNum <= 0 || $goodsNum > $goods['stock']) {
            return array('status' => -1, 'msg' => '库存不足!');
        }

        $cart = $this->where("user_id = {$user['user_id']} and goods_id = {$goodsId}")->find();
        if (!$cart) {
            $cartData = array(
                'user_id' => $user['user_id'],
                'goods_id' => $goodsId,
                'session_id' => session_id(),
                'goods_num' => $goodsNum,
                'goods_name' => $goods['goods_name'],
                'goods_price' => $goods['goods_price'],
                'add_time' => time(),
            );
            $res = $this->add($cartData);
        } else {
            $res = $this->where("id = {$cart['id']}")->setInc('goods_num', $goodsNum);
        }
        if ($res) {
            return array('status' => 1, 'msg' => '成功加入购物车!', 'url' => U('Cart/cartList'));
        } else {
            return array('status' => -1, 'msg' => '加入购物车失败，请刷新页面后重试!');
        }
    }

    /**
     * 查出购物车列表
     */
    public function selectCart($where, $sortArray)
    {
        $list = $this->where($where)->order($sortArray)->select();
        foreach($list as &$v) {
            if($v['selected'] == 1) {
                $list['total_price'] += $v['goods_price']*$v['goods_num'];
            }
            $goods = $this->goodsLogic->findDataByField('goods_id', $v['goods_id']);
            $v['stock'] = $goods['stock'];
            $v['picture'] = $goods['picture'];
        }
        return array('list' => $list);
    }

    /**
     * 删除购物车数据
     */
    public function delCart($cartId, $userId)
    {
        $cartId = is_array($cartId) ? implode(',', $cartId) : $cartId;
        $where = array(
            'id' => array('in', $cartId),
            'user_id' => $userId
        );

        return $this->where($where)->delete();
    }
}