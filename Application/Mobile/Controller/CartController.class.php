<?php

namespace Mobile\Controller;

use Zfuwl\Logic\CartLogic;
use Zfuwl\Logic\GoodsLogic;
use Common\Model\UserAddressModel;

//use Common\Model\UserAddressModel;

class CartController extends CommonController {

    public $cartLogic;
    public $goodsLogic;

    public function _initialize() {
        parent::_initialize();
        $this->goodsLogic = new GoodsLogic();

        $this->cartLogic = new CartLogic();
    }

    /**
     * 购物车列表
     */
    public function cartList() {
        $this->display('cartList');
    }

    /**
     * 添加购物车
     */
    public function addCart() {
        $user = $this->user;

        $post = I('post.');
        $res = $this->cartLogic->addCart($user, $post);
        if ($res['status'] == 1) {
            $this->success($res['msg'], $res['url']);
        } else {
            $this->error($res['msg']);
        }
    }

    /**
     * ajax加载数据
     */
    public function cartListAjax() {
        $condition = array();
        $postGoodsNum = I("goods_num"); // goods_num 购物车商品数量
        $postCartSelect = I("cart_select"); // 购物车选中状态
        if ($postGoodsNum) {
            // 修改购物车数量 和勾选状态
            foreach ($postGoodsNum as $key => $val) {
                $data['goods_num'] = intVal($val) < 1 ? 1 : intVal($val);

                $data['selected'] = $postCartSelect[$key] ? 1 : 0;
                $cart = M('cart')->where("id = $key")->field('goods_id')->find();
                $goods = $this->goodsLogic->findDataByField('goods_id', $cart['goods_id']);
                if ($goods['stock'] < $data['goods_num']) {
                    $data['goods_num'] = $goods['stock'];
                }
                M('Cart')->where("id = $key")->save($data);
            }
            $this->assign('select_all', $_POST['select_all']); // 全选框
        }

        $condition['user_id'] = $this->user_id;

        $cartResult = $this->cartLogic->selectCart($condition);

        $cartList = array();

        foreach ($cartResult['list'] as $k => $v) {
            $v['seller_id'] = intval($v['seller_id']);
            if (is_numeric($k)) {
                $cartList['list'][$v['seller_id']][] = $v;
            } else {
                $cartList['list'][$k] = $v;
            }
        }

        $this->assign('cartList', $cartList['list']);

        $this->display('cartListAjax');
    }

    /**
     * 删除购物车数据
     */
    public function delCart() {
        $res = $this->cartLogic->delCart(I('id'), $this->user_id);
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 确认订单
     */
    public function confirmCart() {

        $condition['user_id'] = $this->user_id;
        $condition['selected'] = 1;
        $address_id = I("get.address_id", 0, 'intval');
        // 获取用户所有的地址
        $address = new UserAddressModel();
        if ($address_id) {
            $address_data = $address->getAddressById($address_id, $this->user_id);
        } else // 如果传入地址id 就显示地址id信息，否则就显示默认地址
            $address_data = $address->getDefaultAddressByUserId($this->user_id);
//        dump($address_data);
        $this->assign('address_info', $address_data);

        $cartResult = $this->cartLogic->selectCart($condition);

        if (count($cartResult['list']) <= 0) {
            $this->error('你的购物车中没有商品!');
        }
        $cartList = array();

        foreach ($cartResult['list'] as $k => $v) {
            $v['seller_id'] = intval($v['seller_id']);
            if (is_numeric($k)) {
                $cartList['list'][$v['seller_id']][] = $v;
            } else {
                $cartList['list'][$k] = $v;
            }
        }

        $this->assign('cartList', $cartList['list']);
        $this->assign('region', M('region')->getField('id, name_cn'));

        $this->display('confirmCart');
    }

    /**
     * 提交订单
     */
    public function addOrder() {
        $post = I("post.");
        $cartId = I('post.cartId');
        if (!$cartId) {
            $this->error('操作失败!');
        }

        $user = $this->user;
//        if (!$user['province'] || !$user['city'] || !$user['district'] || !$user['address'] || !$user['username'] || !$user['mobile']) {
//            $this->error('请把收货信息填写完整!');
//        }

        $address_id = $post['address_id'];
        if (intval($address_id) < 1) {
            $this->error('请选择收货地址!');
        }

        $where = array(
            'address_id' => $address_id
        );

        $address = M('user_address')->where($where)->find();

        $cartWhere = array(
            'id' => array('in', implode(',', $cartId))
        );

        $cartList1 = $this->cartLogic->selectCart($cartWhere);
        if (!$cartList1['list']) {
            $this->error('你的购物车中没有商品!');
        }
        $cartList = array();

        foreach ($cartList1['list'] as $k => $v) {
            $v['seller_id'] = intval($v['seller_id']);
            if (is_numeric($k)) {
                $cartList['list'][$v['seller_id']][] = $v;
                $cartList['list'][$v['seller_id']]['total_price'] += $v['goods_price'] * $v['goods_num'];
                $cartList['list'][$v['seller_id']]['sj_price'] += $v['sj_price'] * $v['goods_num'];
                $cartList['list'][$v['seller_id']]['total_pv'] += $v['pv'] * $v['goods_num'];
                $cartList['list'][$v['seller_id']]['integral'] += $v['integral'] * $v['goods_num'];
            } else {
                $cartList['list'][$k] = $v;
            }
        }
        $master_order_sn = date('YmdHis') . rand(11111111, 99999999);
        foreach ($cartList['list'] as $key => $val) {
            if (is_numeric($key)) {
                $orderData = array(
                    'uid' => $user['user_id'],
                    'order_sn' => date('YmdHis') . rand(1111111, 9999999),
                    'master_order_sn' => $master_order_sn,
                    'consignee' => $address['consignee'],
                    'province' => $address['province'],
                    'city' => $address['city'],
                    'district' => $address['district'],
                    'twon' => $address['twon'],
                    'address' => $address['address'],
                    'mobile' => $address['mobile'],
                    'email' => $user['email'],
                    'goods_price' => $cartList['list'][$key]['total_price'],
                    'sj_price' => $cartList['list'][$key]['sj_price'],
                    'integral' => $cartList['list'][$key]['integral'],
                    'total_amount' => $cartList['list'][$key]['total_price'],
                    'total_pv' => $cartList['list'][$key]['total_pv'],
                    'add_time' => time(),
                    'seller_id' => $key
                );

                $orderId = M('order')->add($orderData);
                foreach ($val as $k => $v) {
                    if (is_numeric($k)) {
                        $goodsData = array(
                            'order_id' => $orderId,
                            'goods_id' => $v['goods_id'],
                            'goods_name' => $v['goods_name'],
                            'goods_num' => $v['goods_num'],
                            'goods_price' => $v['goods_price'],
                            'sj_price' => $v['sj_price'],
                            'total_pv' => $v['total_pv'],
                            'spec_name' => $v['spec_name'],
                            'integral' => $v['integral'],
                        );
                        $goodsOrderRes = M('order_goods')->add($goodsData);
                        $res = M('cart')->where("id = {$v['id']}")->delete();
                    }
                }
            }
        }


        if ($orderId && $goodsOrderRes && $res) {
            $this->success('提交成功!', U('Cart/payOrder') . '?master_order_sn=' . $master_order_sn);
            // $this->success('提交成功!', U('Cart/payOrder') . '?order_id=' . $orderId);
        } else {
            $this->error('提交失败!');
        }
    }

    /**
     * 订单支付页面
     */
    // public function payOrder() {
    //     $orderWhere = array(
    //         'order_id' => I('order_id'),
    //         'uid' => $this->user_id,
    //         'statu' => 1,
    //     );
    //
    //     $order = M('order')->where($orderWhere)->find();
    //     if (!$order) {
    //         $this->error('该订单已支付!');
    //     }
    //     if (IS_POST && IS_AJAX) {
    //         $post = I('post.');
    //         $payCode = $post['payCode'];
    //         if (!$payCode) {
    //             $this->error('请选择支付方式!');
    //         }
    //         if (usersMoney($order['uid'], $payCode) < $order['total_amount']) {
    //             $this->error(moneyList($payCode) . '余额不足!');
    //         }
    //         $res = userMoneyLogAdd($this->user_id, $payCode, '-' . $order['total_amount'],  106, '订单支付订单号' . $order['order_sn']);
    //         if ($res) {
    //             $orderData = array(
    //                 'statu' => 2,
    //                 'pay_time' => time(),
    //                 'pay_code' => moneyList($payCode)
    //             );
    //             $orderRes = M('order')->where("order_id = {$order['order_id']}")->save($orderData);
    //             $this->success('支付成功!', U('Order/orderIndex'));
    //         } else {
    //             $this->error('支付失败!');
    //         }
    //     } else {
    //         $this->assign('order', $order);
    //
    //         $this->display('payOrder');
    //     }
    // }
    public function payOrder() {

        # 主订单号 有可能是多个订单
        $masterOrderSn = I('master_order_sn');

        # 订单id
        $orderId = I('order_id', '', 'intval');

        if ($masterOrderSn) {
            $sumOrderAmount = floatval(M('order')->where(array('master_order_sn' => $masterOrderSn, 'uid' => $this->user_id))->sum('total_amount'));
            if ($sumOrderAmount <= 0) {
                $this->redirect("Order/orderIndex");
                die;
            }
            $sumOrderPv = floatval(M('order')->where(array('master_order_sn' => $masterOrderSn, 'uid' => $this->user_id))->sum('total_pv'));
            $order = array(
                'order_sn' => $masterOrderSn,
                'total_amount' => $sumOrderAmount,
                'total_pv' => $sumOrderPv,
                'uid' => $this->user_id
            );
            $where = array(
                'master_order_sn' => $masterOrderSn
            );
        } else {
            $orderWhere = array(
                'order_id' => I('order_id'),
                'uid' => $this->user_id,
                'statu' => 1,
            );
            $order = M('order')->where($orderWhere)->find();
            if (!$order) {
                $this->error('该订单已支付!');
            }
            $where = array(
                'order_id' => $order['order_id']
            );
        }

        if (IS_POST && IS_AJAX) {
            $post = I('post.');
            $payCode = $post['payCode'];
            if (!$payCode) {
                $this->error('请选择支付方式!');
            }
            if($post['secpwd'] == '') {
                $this->error('请输入二级密码');
            }
            $user = $this->user;
            if(webEncrypt($post['secpwd']) != $user['secpwd']) {
                $this->error('二级密码验证失败');
            }
            if (usersMoney($this->user_id, $payCode) < $order['total_amount']) {
                $this->error(moneyList($payCode) . '余额不足!');
            }

            // if ($order['integral'] > 0) {
            //     if (usersMoney($this->user_id, zfCache('securityInfo.integral_mid')) < $order['integral']) {
            //         $this->error(moneyList(zfCache('securityInfo.integral_mid')) . '余额不足!');
            //     }
            //     userMoneyLogAdd($this->user_id, zfCache('securityInfo.integral_mid'), '-' . $order['integral'], 106, '订单支付订单号' . $order['order_sn']);
            // }

            $res = userMoneyLogAdd($this->user_id, $payCode, '-' . $order['total_amount'], 106, '订单支付订单号' . $order['order_sn']);
            if ($res) {
                $orderData = array(
                    'statu' => 2,
                    'pay_time' => time(),
                    'pay_code' => moneyList($payCode)
                );
                M('order')->where($where)->save($orderData);
                $this->cartLogic->checkAddBranch($order['uid'], $order['total_amount']);
//                $branchInfo = M('users_branch')->where(array('uid' => $order['uid']))->field('branch_id')->find();
                // bonus6Clear($branchInfo['branch_id'], $order['uid'], $order['total_pv'], $this->user['account'].'购买商品');
                $this->success('支付成功!', U('Order/orderIndex'));
            } else {
                $this->error('支付失败!');
            }
        } else {
            $this->assign('masterOrderSn', $masterOrderSn);
            $this->assign('sumOrderAmount', $sumOrderAmount);
            $this->assign('order', $order);

            $this->display('payOrder');
        }
    }

}
