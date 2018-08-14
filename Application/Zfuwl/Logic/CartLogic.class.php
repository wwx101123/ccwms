<?php

namespace Zfuwl\Logic;

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
        if (!$goods || $goods['statu'] != 1) {
            return array('status' => -1, 'msg' => '此商品不存在!');
        }
        // 根据获取 规格 获取 商品的库存和价格
//        $data['goods_spec'];
        $goods_spec_data = $this->getPriceByspec($goodsId,$data['goods_spec']);
//        p($goods_spec_data);
        if($goods_spec_data){
            $goods['stock']=$goods_spec_data['store_count'];
            $goods['price']=$goods_spec_data['price'];
            $goods['pv']=$goods_spec_data['pv'];
        }
        if ($goodsNum <= 0 || $goodsNum > $goods['stock']) {
            return array('status' => -1, 'msg' => '库存不足!');
        }
        // 根据商品规格ids 获取商品规格名
//        $spec_data = M("GoodsPriceStore")->where(['goods_id'=>$goodsId,'spec_key'=>$data['goods_spec']])->getField('spec_name');
//        dd($spec_data);
//        dd($goods);
        $cart = $this->where("user_id = {$user['user_id']} and goods_id = {$goodsId} and spec_name ='{$goods_spec_data['spec_name']}'")->find();
        if (!$cart) {
            $cartData = array(
                'user_id' => $user['user_id'],
                'goods_id' => $goodsId,
                'session_id' => session_id(),
                'goods_num' => $goodsNum,
                'goods_name' => $goods['name'],
                'goods_price' => $goods['price'],
                'member_price' => $goods['price'],
                'pv' => $goods['pv'],
                'add_time' => time(),
            );
            if($goods_spec_data['spec_name'])$cartData['spec_name'] = $goods_spec_data['spec_name'];

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

    /**根据商品规格 获取 商品的价格和库存
     * @param $goods_id
     * @param $spec_key
     * @return mixed
     */
    protected function getPriceByspec($goods_id,$spec_key){
        $data = D("GoodsPriceStore")->where(['goods_id'=>$goods_id,'spec_key'=>$spec_key])->find();
        return $data;
    }
    /**根据商品规格 获取 商品的库存
     * @param $goods_id
     * @param $spec_name
     * @return mixed
     */
    protected function getStoreByspec($goods_id,$spec_name){
        $data = M("GoodsPriceStore")->where(['goods_id'=>$goods_id,'spec_name'=>$spec_name])->getField('store_count');
//        dd($data);
        return $data;
    }

    /**
     * 检测会员进入公排网
     * @param int $userId 会员id
     * @param float|int $money 消费金额
     * @return bool
     */
    public function checkAddBranch($userId, $money)
    {
        $tjMoney = floatval(zfCache('regInfo.enter_network_goods_money'));

        if($money >= $tjMoney) {
            $branch = M('users_branch')->where(['uid' => $userId])->find();
            if(!$branch) {
                $res = addUserBranch($userId);
                userMoneyLogAdd($userId, 1, floatval(zfCache('regInfo.enter_network_give_money')), 125, '赠送');
                if($res > 0) {
                    playMoney($res);
                }
            }
        }

        return false;
    }
}