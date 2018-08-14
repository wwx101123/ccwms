<?php

namespace Mobile\Controller;

use Zfuwl\Logic\OrderLogic;
use Zfuwl\Logic\GoodsLogic;
use Common\Model\OrderGoodsModel;
use Common\Model\OrderModel;

/**
 * 订单
 * Class OrderController
 * @package Mobile\Controller
 */
class OrderController extends CommonController {

    public $orderLogic;
    public $orderGoodsLogic;
    public $goodsLogic;

    public function _initialize() {
        parent::_initialize();
//        $this->orderLogic = new OrderLogic();
//        $this->orderGoodsLogic = new OrderLogic('order_goods');
//        $this->goodsLogic = new GoodsLogic();
    }

    /**
     * 订单列表
     */
    public function orderIndex() {
        $condition = array();
        $condition['uid'] = $this->user_id;

        if (I('get.status')) {
            $condition['statu'] = trim(I('get.status'));
        } else {
            $condition['statu'] = array('neq', 5);
        }

        $count = $this->orderLogic->where($condition)->count();
        $orderResult = $this->orderLogic->selectAllListAjax($condition, array('add_time' => 'desc'));

        foreach ($orderResult['list'] as &$v) {
            $orderGoodsWhere = array(
                'order_id' => $v['order_id'],
            );
            $goodsList = $this->orderGoodsLogic->selectAll($orderGoodsWhere);
            foreach ($goodsList as &$v2) {
                $v2 = array_merge($v2, $this->goodsLogic->findDataByField('goods_id', $v2['goods_id']));
            }
            $v['goods'] = $goodsList;
        }
//        dump($orderResult['list']);
        $this->assign('orderList', $orderResult['list']);
        if (IS_AJAx && I('is_list') == 1) {
            $this->display('orderIndexAjaxList');
        } elseif (IS_AJAX) {
            $this->assign('status', I('get.status'));
            $this->assign('count', $count);
            $this->display('orderIndexAjax');
        } else {
            $this->assign('orderStatus', orderStatu());

            $this->display('orderIndex');
        }
    }

    /**
     * 订单详情
     */
    public function orderDetail() {
        $user = $this->user;
        $orderId = I('order_id', '', 'intVal');
        $order = (new OrderModel())->getOrderById($orderId);
        if ($order['uid'] != $user['user_id']) {
            $this->error("操作失败，请于管理员联系");
            exit();
        }
        $orderGoodsList = $this->orderGoodsLogic->getOrderGoodsByOrderId($orderId);
        $this->assign('orderGoodsList', $orderGoodsList);
        $this->assign('order', $order);
        $this->assign('user', $user);
        $this->assign('region', M('region')->getField('id, name_cn'));

        A('Mobile/Goods')->getRecommendGoodsList();

        $this->display('orderDetail');
    }

    /**
     * 确认收货
     */
    public function confirmOrder() {
        if (IS_AJAX) {
            $user = $this->user;
            $orderId = I('order_id', '', 'intVal');
            $order = $this->orderLogic->findDataByField('order_id', $orderId);

            if (!$order || $order['statu'] != 3) {
                $this->error('操作失败!');
            }
            $orderData = array(
                'statu' => 4,
                'confirm_time' => time()
            );
            $res = $this->orderLogic->saveData("order_id = {$orderId}", $orderData);
            if ($res) {
                // $orderGoodsList = M('order_goods')->where(array('order_id' => $orderId))->select();
                // $pv = 0;
                // foreach ($orderGoodsList as $v) {
                //     $goods = M('goods')->where(array('goods_id' => $v['goods_id']))->field('pv')->find();
                //     $pv += $goods['pv'] * $v['goods_num'];
                // }
                // if ($pv > 0) {
                //     userMoneyLogAdd($user['user_id'], 4, $pv, 120, '购买商品赠送', '', $user['user_id']);
                // }
                // if ($user['is_seller'] == 1) {
                //     $per = array(
                //         '0' => zfCache('securityInfo.redirect_merchant_per'),
                //         '1' => zfCache('securityInfo.redirect_merchant_second_per'),
                //     );
                // } else {
                //     $per = array(
                //         '0' => zfCache('securityInfo.redirect_member_per'),
                //         '1' => zfCache('securityInfo.redirect_member_second_per'),
                //     );
                // }
                // if ($pv > 0) {
                //     # 地址业绩奖计算
                //     bonusAchievementByAddress($user['user_id'], $pv, $user['account'] . '消费订单' . $order['order_sn']);
                //     # 团队业绩奖 合伙人
                //     bonusAchievementByTeam($user['tjr_id'], $user['user_id'], $pv, $user['account'] . '消费订单' . $order['order_sn']);
                // }
                // if ($user['tjr_id'] && $pv > 0) {
                //     # 消费推荐奖
                //     bonus1ClearForConsumption($user['tjr_id'], $user['user_id'], $pv, $per, $user['account'] . '消费订单' . $order['order_sn']);
                // }
                // $sellerInfo = M('seller')->where(array('seller_id' => $order['seller_id']))->field('uid')->find();
                // if (zfCache('securityInfo.seller_cz_per') > 0) {
                //     # 发放 商家自己的奖励
                //     userMoneyLogAdd($sellerInfo['uid'], zfCache('securityInfo.change_mid_to_mid'), $pv * zfCache('securityInfo.seller_cz_per'), 105, $user['account'] . '消费订单' . $order['order_sn'], '', $user['user_id']);
                // }
                // $sellerTjrId = M('users')->where(array('user_id' => $sellerInfo['uid']))->field('tjr_id')->find();
                // if ($sellerTjrId['tjr_id'] > 0) {
                //     # 发放商家推荐人领导奖
                //     $money = $pv * zfCache('securityInfo.merchant_tjr') / 100;
                //     userMoneyLogAdd($sellerTjrId['tjr_id'], zfCache('securityInfo.change_mid_to_mid'), $money, 119, $user['account'] . '消费订单' . $order['order_sn'] . '商家领导人获利' . $money, '', $user['user_id']);
                // }

                $this->success('操作成功!');
            } else {
                $this->error('操作失败!');
            }
        }
    }

    /**
     * 取消订单
     */
    public function cancelOrder() {
        if (IS_AJAX) {
            $user = $this->user;
            $orderId = I('order_id', '', 'intVal');
            $order = $this->orderLogic->findDataByField('order_id', $orderId);

            if (!$order || $order['statu'] != 1) {
                $this->error('操作失败!');
            }
            $orderData = array(
                'statu' => 5,
                'cancel_time' => time()
            );
            $res = $this->orderLogic->saveData("order_id = {$orderId}", $orderData);
            if ($res) {
                $this->success('取消成功!');
            } else {
                $this->error('取消失败!');
            }
        }
    }

    /**
     * 线下订单
     */
    public function lineOrder() {
        $condition = array();
        $condition['user_id'] = $this->user_id;

        $count = M('seller_order')->where($condition)->count();

        $p = I('p') > 0 ? I('p') : 0;
        $pSize = 10;
        $list = M('seller_order')->where($condition)->limit(($p * $pSize) . ',' . $pSize)->order(array('add_time' => 'desc'))->select();

        $sellerIdArr = getArrcolumn($list, 'seller_id');
        $sellerIdArr && $this->assign('sellerList', M('seller')->where(array('seller_id' => array('in', $sellerIdArr)))->getField('seller_id,name,logo'));

        $this->assign('list', $list);
        if (IS_AJAX) {
            $this->display('lineOrderAjax');
        } else {
            $this->assign('count', $count);
            $this->display('lineOrder');
        }
    }

    /**
     * 线下订单详情
     */
    public function lineOrderDetail() {
        $orderId = I('get.order_id', '', 'intval');
        if ($orderId <= 0) {
            $this->error('操作失败');
        }

        $orderInfo = M('seller_order')->where(array('id' => $orderId))->find();

        $this->assign('orderInfo', $orderInfo);
        $this->assign('sellerInfo', getSellerInfo($orderInfo['seller_id']));
        $this->assign('region', M('region')->getField('id, name_cn'));

        $this->display('lineOrderDetail');
    }

    /**
     * 取消线下订单
     */
    public function cancelOrderForSeller() {
        if (IS_POST) {
            $id = I('post.id', '', 'intval');
            if ($id <= 0) {
                $this->error('操作失败');
            }
            $sellerOrderInfo = M('seller_order')->where(array('id' => $id, 'user_id' => $this->user_id))->find();
            if (!$sellerOrderInfo) {
                $this->error('该订单不存在');
            } elseif ($sellerOrderInfo['order_status'] == 3) {
                $this->error('该订单已取消了');
            }
            $data = array(
                'order_status' => 3,
                'cancel_time' => time()
            );

            $res = M('seller_order')->where(array('id' => $sellerOrderInfo['id']))->save($data);
            if ($res) {
                $this->success('取消成功');
            } else {
                $this->error('取消失败');
            }
        }
    }

    /**
     * 会员评论
     */
    public function addComment()
    {
        if(IS_POST) {
            $post = I('post.');
            $post['score'] = intval($post['score']);
            if($post['score'] <= 0) {
                $this->error('请选择评分');
            }
            if($post['content'] == '') {
                $this->error('请输入评论内容');
            }

            $goodsId = I('gid', '', 'intval');
            $ogId = I('ogid', '', 'intval');

            $goods = M('goods')->where(array('goods_id' => $goodsId))->find();
            $orderGoods = M('order_goods')->where(array('id' => $ogId))->find();
            if(!$goods || !$orderGoods) {
                $this->error('请先购买该产品');
            }
            $data = array(
                'gid' => $goodsId,
                'oid' => $orderGoods['order_id'],
                'ogid' => $ogId,
                'sid' => $goods['seller_id'],
                'uid' => $this->user_id,
                'content' => $post['content'],
                'is_anonym' => $post['is_anonym'] ? 1 : 2,
                'spec_name' => $orderGoods['spec_name'],
                'score' => $post['score'],
                'add_time' => time()
            );

            $res = M('goods_comment')->add($data);
            if($res) {
                M("order_goods")->where(array('id' => $orderGoods['id']))->save(array('is_comment' => 1, 'comment_time' => time()));
                $this->success('发布评论成功', U('Goods/goodsInfo', array('id' => $goodsId)));
            } else {
                $this->error('发布评论失败');
            }
        } else {
            $this->display('addComment');
        }
    }

    /**
     * 领取积分记录
     */
    public function scoreCollectionLog()
    {
        $condition = [
            'uid' => $this->user_id,
            'is_type' => 158
        ];
        $count = M('money_log')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('money_log')->where($condition)->order('zf_time desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('scoreCollectionLogAjax');

            exit;
        }
        $this->assign('count', $count);
        $this->display('scoreCollectionLog');
    }

    /**
     * 储存转出日志
     */
    public function storeoutgoingLog()
    {
        $condition = [
            'uid' => $this->user_id
        ];
        $count = M('block_release_money')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_release_money')->where($condition)->order('add_time desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('storeoutgoingLogAjax');

            exit;
        }
        $this->assign('count', $count);
        $this->display('storeoutgoingLog');
    }
    /**
     * 流动转出日志
     */
    public function flowOutLog()
    {
        $condition = [
            'uid' => $this->user_id,
        ];
        $count = M('block_change_log')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_change_log')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('flowOutLogAjax');

            exit;
        }
        $this->assign('count', $count);
        $this->display('flowOutLog');
    }

   	/**
     * 积分日志
     */
    public function integralLog()
    {
        $condition = [
            'uid' => $this->user_id,
            'is_type' => 10,
            'money' => ['gt', 0]
        ];
        $count = M('block_log')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_log')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('integralLogAjax');
            exit;
        }
        $this->assign('count', $count);
        $this->display('integralLog');
    }

    /**
     * 钱包日志
     */
    public function thewalletLog()
    {
        $condition = [
            'uid' => $this->user_id,
        ];
        $count = M('money_log')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('money_log')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('thewalletLogAjax');
            exit;
        }
        $this->assign('count', $count);
        $this->display('thewalletLog');
    }
}
