<?php

/**
 *  众福网络直销系统管理软件
 * ============================================================================
 * 版权所有 2015-2027 深圳市众福网络软件有限公司，并保留所有权利。
 * 网站地址: http://www.zfuwl.com   http://www.jiafuw.com
 * 联系方式：qq:1845218096 电话：15899929162
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author:  众福团队
 * Date:2016-12-10 21:30  154
 */

namespace Zfuwl\Controller;

class BlockController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('bonusPerList', bonusPer());
        $this->assign('bonusSingle', M('bonus')->where("statu=1")->cache('bonus')->getField('bonus_id,name_cn'));
        $this->assign('blockLogType', blockLogType());
        $this->assign('blockcrowdStatus', blockcrowdStatus());
        $this->assign('lockUserBlockStatu', lockUserBlockStatu());
        $this->assign('lockStatu', lockStatu());
        $this->assign('tradeStatus', tradeStatus());
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('blockInfo', M('block')->where("statu=1")->cache('blockInfo')->getField('id,name_cn'));
      	$this->assign('bankInfo', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->addBlockConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Block/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('block')->where(array('id' => I('id')))->find());
            $this->display('blockInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('block')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveStatusCrowd() {
        if (IS_POST) {
            $res = M('block_crowd')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function userBlock() {
        $this->assign('totalNum', M('block_user')->where('bid = 1')->sum('money'));
        $this->assign('lockNum', M('block_user')->where('bid = 1')->sum('frozen'));
        $this->assign('cunNum', M('block_user')->where('bid = 1')->sum('deposit'));

        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_user')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_user')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userBlockAjax');
            die;
        }
        $this->display('userBlock');
    }

    /**
     * 对会员股票做修改
     */
    public function userBlockEdit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->userBlockEdit($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Block/userBlock'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $info = M('block_user')->where(array('id' => I('id')))->find();
            $this->assign('info', $info);
            $this->assign('user', M('users')->where(array('user_id' => $info['uid']))->field('account')->find());
            $this->display('userBlockEdit');
        }
    }
  
  	/**
     * 确认收款操作
     */
    public function confirmPay() {
        if (IS_POST) {
            $post = I('post.');
            $blockLogic = new \Zfuwl\Logic\BlockLogic();
            $res = $blockLogic->confirmPay($post);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 确认撤销操作
     */
    public function tradeOut()
    {
        if (IS_POST) {
            $post = I('post.');
            $blockLogic = new \Zfuwl\Logic\BlockLogic();
            $res = $blockLogic->tradeOut($post);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }


    /**
     * 管理员撤回交易
     */
    public function SellOutTrade() {
        if (IS_POST) {
            $id = I('id', '');

            $list = M('block_sell')->where(array('id' => $id))->find();
            if ($list['status'] < 3 && $list['stay_num'] > 0) {
                $res = M('block_sell')->where(array('id' => $id))->save(array('status' => 3, 'return_time' => time(), 'return_num' => $list['stay_num']));
                if ($res) {
                    $num = $list['num'];
                    userBlockLogAdd($list['uid'], $list['bid'], $num, 112, '撤销交易');
                    $this->success('撤销成功');
                } else {
                    $this->error('撤销失败!');
                }
            } else {
                $this->error('撤销失败!');
            }
        }
    }

    public function blockLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('to_account') ? $condition['come_uid'] = $res = M('users')->where(array('account' => trim(I('to_account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $A = getArrColumn($result, 'uid');
            $B = getArrColumn($result, 'come_uid');
            $userIdArr = array_filter(array_merge($A, $B));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('blockLogAjax');
            die;
        }
        $this->display('blockLog');
    }

    public function priceLog() {
        if (IS_AJAX) {
            $condition = array();
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('bid') ? $condition['bid'] = I('bid') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_price')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_price')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('priceLogAjax');
            die;
        }
        $this->display('priceLog');
    }

    /**
     * 兑换参数设置
     */
    public function transConfig() {
        if (IS_AJAX) {
            $condition = array();
            I('money_id') ? $condition['money_id'] = I('money_id') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_transform')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_transform')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('transConfigAjax');
            die;
        }
        $this->display('transConfig');
    }

    public function editConfig() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->editConfigInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Block/transConfig'));
            }
        } else {
            $this->assign('info', M('block_transform')->where(array('id' => I('id')))->find());
            $this->display('transInfo');
        }
    }

    /**
     * 兑换日志
     */
    public function transLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_transform_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_transform_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('transLogAjax');
            die;
        }
        $this->display('transLog');
    }

    /**
     * 确认兑换
     */
    public function affirmAdd() {
        $data = $_POST['id'];
        $type = 108;
        if (is_array($data)) {
            foreach ($data as $v) {
                $info = M('block_transform_log')->where(array('id' => $v))->find();
                if (!$info || $info['statu'] > 2) {
                    continue;
                }
                $res = M('block_transform_log')->where(array('id' => $info['id']))->save(array('statu' => 9, 'affirm_time' => time(), 'admin_id' => session('admin_id')));
                userBlockLogAdd($info['uid'], 1, $info['num'], 1, '兑换ID' . $info['id'], session('admin_id'));
                if ($info['dmid'] > 0 && $info['dmoney'] > 0) {
                    userMoneyLogAdd($info['uid'], $info['dmid'], $info['dmoney'], $type, '兑换ID' . $info['id'], session('admin_id'));
                }
            }
        } else {
            $info = M('block_transform_log')->where(array('id' => $_POST['id']))->find();
            if (!$info || $info['statu'] > 2) {
                $this->error('网络失败，请刷新页面后重试');
            }
            if ($info['statu'] <= 2) {
                $res = M('block_transform_log')->where(array('id' => $info['id']))->save(array('statu' => 9, 'affirm_time' => time(), 'admin_id' => session('admin_id')));
                userBlockLogAdd($info['uid'], 1, $info['num'], 1, '兑换ID' . $info['id'], session('admin_id'));
                if ($info['dmid'] > 0 && $info['dmoney'] > 0) {
                    userMoneyLogAdd($info['uid'], $info['dmid'], $info['dmoney'], $type, '兑换ID' . $info['id'], session('admin_id'));
                }
            }
        }
        if ($res) {
            $this->success('确认成功');
        } else {
            $this->error('操作失败，请刷新页面后重试');
        }
    }

    /**
     * 拒绝兑换
     */
    public function refuseAdd() {
        if ($_POST) {
            $info = M('block_transform_log')->where(array('id' => $_POST['id']))->find();
            if ($info['statu'] == 1) {
                $model = new \Think\Model();
                $model->startTrans();
                $res = M('block_transform_log')->where(array('id' => $info['id']))->save(array('statu' => 3, 'refuse_time' => time(), 'refuse' => $_POST['name'], 'admin_id' => session('admin_id')));
                $info = userMoneyLogAdd($info['uid'], $info['mid'], $info['money'], 104, $_POST['name'], session('admin_id'));
                if ($res && $info) {
                    $model->commit();
                    $this->success('确认成功');
                } else {
                    $model->rollback();
                    $this->error('操作失败，请刷新页面后重试');
                }
            } else {
                $this->error('网络失败，请刷新页面后重试');
            }
        }
    }

    /**
     * 转账参数
     */
    public function change() {
        if (IS_AJAX) {
            $condition = array();
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('type_id') ? $condition['type_id'] = I('type_id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_change')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_change')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('changeAjax');
            die;
        }
        $this->display('change');
    }

    /**
     * 修改转账参数
     */
    public function changeEdit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->editConfigInfo2($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Block/change'));
            }
        } else {
            $this->assign('info', M('block_change')->where(array('id' => I('id')))->find());
            $this->assign('id', 1);
            $this->display('transInfo');
        }
    }

    public function saveIsUpper() {
        if (IS_POST) {
            $res = M('block_change')->where(array('id' => I('id')))->save(array('is_upper' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIslower() {
        if (IS_POST) {
            $res = M('block_change')->where(array('id' => I('id')))->save(array('is_lower' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveStatu1() {
        if (IS_POST) {
            $res = M('block_change')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function changeLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('to_account') ? $condition['to_uid'] = $res = M('users')->where(array('account' => trim(I('to_account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('type_id') ? $condition['type_id'] = I('type_id') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_change_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_change_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $A = getArrColumn($result, 'uid');
            $B = getArrColumn($result, 'to_uid');
            $userIdArr = array_filter(array_merge($A, $B));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('changeLogAjax');
            die;
        }
        $this->display('changeLog');
    }

    public function lockUserBlock() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('type') ? $condition['type'] = I('type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['lock_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_user_lock')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_user_lock')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', D('UserView')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account,username'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('lockUserBlockAjax');
            die;
        }
        $this->display('lockUserBlock');
    }

    public function releaseBlock() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->releaseBlock($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Block/lockUserBlock'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('lockUserMoney');
        }
    }

    /**
     * 所有交易列表
     */
    public function tradeIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('sell_account') ? $condition['sell_uid'] = $res = M('users')->where(array('account' => I('sell_account')))->getField('user_id') : false;
            I('buy_account') ? $condition['buy_uid'] = $res = M('users')->where(array('account' => I('buy_account')))->getField('user_id') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('sell_id') ? $condition['sell_id'] = I('sell_id') : false;
            I('buy_id') ? $condition['buy_id'] = I('buy_id') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_trade')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_trade')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $sellIdArr = getArrColumn($result, 'sell_uid');
            $buyIdArr = getArrColumn($result, 'buy_uid');
            $userIdArr = array_filter(array_merge($sellIdArr, $buyIdArr));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('tradeIndexAjax');
            die;
        }
        $this->display('tradeIndex');
    }

    /**
     * 委托 卖出列表
     */
    public function sellIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('sell_account') ? $condition['uid'] = $res = M('users')->where(array('account' => I('sell_account')))->getField('user_id') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('status') ? $condition['status'] = I('status') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_sell')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_sell')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sellIndexAjax');
            die;
        }
        $this->display('sellIndex');
    }

    /**
     * 委托 买入列表
     */
    public function buyIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('buy_account') ? $condition['uid'] = $res = M('users')->where(array('account' => I('buy_account')))->getField('user_id') : false;
            I('bid') ? $condition['mid'] = I('bid') : false;
            I('status') ? $condition['status'] = I('status') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_buy')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_buy')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('buyIndexAjax');
            die;
        }
        $this->display('buyIndex');
    }

    /**
     * 投诉 列表
     */
    public function tousu() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $count = M('tousu_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('tousu_log')->where($condition)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('tousuAjax');
            die;
        }
        $this->display('tousu');
    }

    /**
     * 添加 众筹
     */
    public function addCrowd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->addCrowdConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Block/crowdIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('block')->where(array('id' => I('id')))->find());
            $this->display('crowdInfo');
        }
    }

    /**
     * 众筹 会员 列表
     */
    public function crowdUser() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => I('account')))->getField('user_id') : false;
            I('bid') ? $condition['bid'] = I('bid') : false;
            I('status') ? $condition['status'] = I('status') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('block_crowd_user')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('block_crowd_user')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('crowdUserAjax');
            die;
        }
        $this->display('crowdUser');
    }
	
  	public function rechargeBlock() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BlockLogic();
            $res = $model->rechargeBlock($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Block/crowdIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }
  
  	public function returnben()
    {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['b_uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $count = M('recovery_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('recovery_log')->where($condition)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('recoveryAjax');
            die;
        }
        $this->display('recovery');
    }

    public function recoveryisType()
    {
        $post = I('post.');
        if (!$post['id']) {
            $this->error('网络错误，请刷新页面重试');
        }

        $info = M('recovery_log')->where(['id' => $post['id']])->find();
        $res = M('recovery_log')->where(['id' => $post['id']])->save(['is_type' => 2]);
        if ($res && $info) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
