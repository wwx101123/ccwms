<?php

namespace Mobile\Controller;

use Zfuwl\Logic\BlockLogic;

class BlockController extends CommonController {

    public function _initialize() {
        parent::_initialize();
      	$this->blockLogic = new BlockLogic();
        $this->assign('countryInfo', countryList());
        buySellRemitTimeBlock($this->user_id);
        $this->assign('blockLogType', blockLogType());
        $this->assign('userSecurityList', userSecurityList());
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
    }

    public function depositInfo() {
        $user = $this->user;
        if (IS_POST) {
            $post = I('post.');
            $res = $this->agentLogic->upAgentAdd($post, $user);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('depositInfo');
        }
    }

    public function marketFor() {

        $bid = I('id');

        //获取次日凌晨的时间戳
        $time = strtotime(date('Y-m-d'));

        // 获取昨天的时间戳
        $jj = strtotime(date('Y-m-d', strtotime('-1 day')));

        //获取明天凌晨的时间戳
        $kk = strtotime(date('Y-m-d', strtotime('+1 day')));

        // 昨日时间
        $condition['sj_time'] = array(array('gt', $jj), array('lt', $time));
        $condition['bid'] = $bid;

        // 今日时间
        $data['sj_time'] = array(array('gt', $time), array('lt', $kk));
        $data['bid'] = $bid;

        // 昨日收盘价
        $mbi2 = M('block_trade')->where($condition)->order('add_time desc')->find();

        $time = 0;
        $type = I('type') ? I('type') : '1day';
        $db = M();
        $data = $db->query("select * from __PREFIX__block_trade where bid={$bid} order by add_time desc limit 100");
        $tOrders = array();
        foreach ($data as $v1) {
            $tTime = strtotime($this->ctime($v1['add_time'], $type));
            if (empty($tOrders[$tTime])) {
                # open
                $tOrders[$tTime] = array(date('Y/m/d', ($tTime)), $v1['price'], $v1['price'], $v1['price'], $v1['price'], $v1['num']);
            } else {
                $tOrders[$tTime][5] += $v1['num'];
                # high
                $v1['price'] > $tOrders[$tTime][4] && $tOrders[$tTime][4] = $v1['price'];
                # low
                $v1['price'] < $tOrders[$tTime][3] && $tOrders[$tTime][3] = $v1['price'];
                # close
                $tOrders[$tTime][2] = $v1['price'];
            }
        }
        $tOrders = array_slice($tOrders, -80, 80, true);
        $tOrders = array_reverse($tOrders);
        foreach ($tOrders as $k => $v1) {
            $tJS[] = $v1;
        }
        $datas = json_encode($tJS);

        $bidname = M('block')->where(array('id' => I('id')))->field('name_cn,now_price,id,logo')->find();

        // 涨跌幅
        $zhang = ($bidname['now_price'] - $mbi2['price']) / $mbi2['price'] * 100;

        $this->assign('data', $datas);
        $this->assign('bidname', $bidname);
        //涨跌幅
        $this->assign('zhang', $zhang);
        // 成交量
        $this->assign('liang', M('block_sell')->where(array('status' => 9, 'bid' => $bid))->count());
        $this->assign('type', $type);
        $this->display('marketFor');
    }

    public function marketFor2() {
        $bid = I('id');

        $time = 0;
        $type = I('type') ? I('type') : '1day';
        $db = M();
        $data = $db->query("select * from __PREFIX__block_trade where bid={$bid} order by add_time desc limit 100");
        $tOrders = array();
        foreach ($data as $v1) {
            $tTime = strtotime($this->ctime($v1['add_time'], $type));
            if (empty($tOrders[$tTime])) {
                # open
                $tOrders[$tTime] = array(date('Y/m/d', ($tTime)), $v1['price'], $v1['price'], $v1['price'], $v1['price'], $v1['num']);
            } else {
                $tOrders[$tTime][5] += $v1['num'];
                # high
                $v1['price'] > $tOrders[$tTime][4] && $tOrders[$tTime][4] = $v1['price'];
                # low
                $v1['price'] < $tOrders[$tTime][3] && $tOrders[$tTime][3] = $v1['price'];
                # close
                $tOrders[$tTime][2] = $v1['price'];
            }
        }
        $tOrders = array_slice($tOrders, -80, 80, true);
        asort($tOrders);
        foreach ($tOrders as $k => $v1) {
            $v1[6] = floatval($k . '000');
            $tJS[] = $v1;
        }
        $this->ajaxReturn(array('msg' => $tJS));
    }

    public function kxTu() {
        $this->display('kxTu');
    }

    /**
     * 会员兑换生命链
     */
    public function transAdd() {
        if (IS_POST) {
            $post = I('post.');
            $carrLogic = new BlockLogic();
            $res = $carrLogic->depositInfo($post, $this->user_id);
//            $res = $carrLogic->userTransAdd($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U("Block/transAdd"));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->assign('info', M('block_user')->where(['uid' => $this->user_id])->find());
            $this->display('transAdd');
        }
    }
		
  
  	/**
     * 流动转出日志
     */
    public function stream()
    {
        $condition = [
            'uid' => $this->user_id,
            'is_type' => 2
        ];
        $count = M('chucun_log')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('chucun_log')->where($condition)->order('add_time desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('streamAjax');
            exit;
        }
        $this->assign('count', $count);
        $this->display('stream');
    }
  
  	/**
     * 买入流动货币
     */
    public function transfrom()
    {
        if (IS_POST) {
            $post = I('post.');
            $carrLogic = new BlockLogic();
            $res = $carrLogic->userTransAdd($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U("Block/transfrom"));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->assign('info', M('block_transform')->where(['id' => 1, 'statu' => 1])->find());
            $this->display('transfrom');
        }
    }

    /**
     * 计算兑换所要的积分
     */
    public function fenNum()
    {
        $post = I('post.');
        if (!$post['fenNum']) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请输入兑换数量']);
        }

        $fenNum = $post['fenNum'] * blockList($post['id'], 2);
        $this->ajaxReturn(['status' => 1, 'msg' => $fenNum]);
    }
  
  
    /**
     * 会员兑换生命链
     */
    public function flowAround() {
        if (IS_POST) {
            $user = $this->user;
            $post = I('post.');
            $carrLogic = new BlockLogic();

            $res = smsCodeVerify($user['account'], $post['mobileCode'], session_id());
            if ($res['status'] != 1) {
                $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
            }
            $res = $carrLogic->userChangeAdd($post, $this->user_id);

            if ($res['status'] == 1) {
                $this->success($res['msg'], U("Block/flowAround"));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->assign('info', M('block_user')->where(['uid' => $this->user_id, 'bid' => 1])->find());
            $this->display('flowAround');
        }
    }
	
  	/**
    * 显示对方账号
    */
	public function Daccount()
    {
        $post = I('post.');
        if (!$post['address']) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请输入正确的钱包地址']);
        }

        $address = M('block_user')->where(['address' => $post['address']])->find();
        if (!$address) {
            $this->ajaxReturn(['status' => -1, 'msg' => '该钱包地址不存在']);
        }

        $toAccount = M('users')->where(['user_id' => $address['uid']])->find();

        $this->ajaxReturn(['status' => 1, 'msg' => $toAccount['account']]);
    }

    /**
     * 卖出操作
     */
    public function currencySell() {
        
    }
	
  	/**
     * 挂卖
     */
    public function blockSellIndex()
    {
        if (IS_POST) {
            $post = I('post.');

            $res = $this->blockLogic->sellAdd($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
            exit;
        }

        $this->assign('info', M('block')->where(['id' => 1, 'statu' => 1])->find());
        $this->display('blockSellIndex');
    }

    /**
     *  买入
     */
    public function blockBuyIndex()
    {
        if (IS_POST) {
            $post = I('post.');
            $res = $this->blockLogic->buyOneAdd($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U("Block/blockStatusOne"));
            } else {
                $this->error($res['msg']);
            }
        }
    }

    /**
     * 买入记录
     */
    public function fenmoneyLog()
    {
        $condition = [
            'uid' => array('neq', $this->user_id),
            'status' => 1
        ];
        $count = M('block_sell')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_sell')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('fenmoneyLogAjax');
            exit;
        }

        $this->assign('count', $count);
        $this->display('fenmoneyLog');
    }

    /**
     * 卖出记录
     */
    public function blockSellList()
    {
        $condition = [
            'uid' => $this->user_id,
            'status' => 1
        ];
        $count = M('block_sell')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_sell')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('blockSellListAjax');
            exit;
        }

        $this->assign('count', $count);
        $this->display('blockSellList');
    }

    /**
     * 待打款
     */
    public function blockStatusOne()
    {
        $condition['is_type'] = ['in', '1,2,3'];
        $condition['_string'] = 'sell_uid = ' . $this->user_id . ' or buy_uid = ' . $this->user_id;
        $count = M('block_trade')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_trade')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->assign('block', M('block')->where(['id' => 1, 'statu' => 1])->field('dakuan_time,shoukuan_time')->find());
            $this->assign('bankList', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
            $this->display('blockStatusOneAjax');
            exit;
        }

        $this->assign('count', $count);
        $this->display('blockStatusOne');
    }

    /**
     * 确认打款操作
     */
    public function tradeBank()
    {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->blockLogic->buyPayAdd($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U("Block/blockStatusOne"));
                exit;
            } else {
                $this->error($res['msg']);
            }
        }
    }

    /**
     * 待收款
     */
    public function blockStatusTwo()
    {

        $condition['is_type'] = ['in', '1,2,4'];
        $condition['_string'] = 'sell_uid = ' . $this->user_id . ' or buy_uid = ' . $this->user_id;
        $count = M('block_trade')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_trade')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('block', M('block')->where(['id' => 1, 'statu' => 1])->field('dakuan_time,shoukuan_time')->find());
            $this->assign('list', $result);
            $this->assign('bankList', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
            $this->display('blockStatusTwoAjax');
            exit;
        }

        $this->assign('count', $count);
        $this->display('blockStatusTwo');
    }


    /**
     * 确认收款操作
     */
    public function confirmPay() {
        if (IS_POST) {
            $post = I('post.');
            $res = $this->blockLogic->confirmPay($post);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 已完成
     */
    public function blockStatusOk()
    {
        $condition['_string'] = 'sell_uid = ' . $this->user_id . ' or buy_uid = ' . $this->user_id;
        $condition['is_type'] = 9;
        $count = M('block_trade')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('block_trade')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->assign('bankList', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
            $this->display('blockStatusOkAjax');
            exit;
        }

        $this->assign('count', $count);
        $this->display('blockStatusOk');
    }

    /**
     *  卖家撤销交易
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

    /**
     * 投诉
     */
    public function tousu()
    {
        if (IS_POST) {
            $post = I('post.');

            $kk = M('tousu_log')->where(['uid' => $this->user_id, 'block_trade_id' => $post['id'], 'is_type' => $post['is_type']])->find();
            if ($kk) {
                $this->error('你已经投诉');
            }

            if (!$post['content']) {
                $this->error('请输入你要投诉的内容');
            }

            if (!$post['file']) {
                $this->error('请输入投诉图片');
            }

            $block_trade = M('block_trade')->where(['id' => $post['id']])->find();

            $data = [];
            $data['block_trade_id'] = $post['id'];
            $data['img'] = $post['file'];
            $data['uid'] = $this->user_id;
            if ($post['is_type'] == 3) {
                $data['sell_uid'] = $block_trade['buy_uid'];
            } else {
                $data['sell_uid'] = $block_trade['sell_uid'];
            }
            $data['img'] = $post['file'];
            $data['is_type'] = $post['is_type'];
            $data['status'] = 1;
            $data['note'] = $post['content'];


            $res = M('tousu_log')->add($data);

            if ($res) {
                $this->success('投诉成功，我们会尽快为你处理');
            } else {
                $this->error('投诉失败');
            }

        }
        $this->assign('list', M('tousu_log')->where(['uid' => $this->user_id, 'block_trade_id' => I('get.id'), 'is_type' => I('get.is_type')])->find());
        $this->assign('info', M('block_trade')->where(['id' => I('get.id'), 'is_type' => I('get.is_type')])->find());
        $this->display('tousu');
    }
  	
  	public function returnben()
    {
        if (IS_POST) {
            $post = I('post.');
            $res = $this->blockLogic->returnben($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U('User/userIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }

        $this->assign('recovery', M('recovery_log')->where(['b_uid' => $this->user_id])->find());
        $this->assign('bankInfo', M('users_bank')->where(['uid' => $this->user_id])->find());
        $this->display('returnben');
    }
  
    public function blockLog() {
        $condition = array();
        I('bid') && $condition['bid'] = I('bid');
        I('type') && $condition['is_type'] = I('type');
        I('kwd') && $condition['note'] = array('like', '%' . trim(I('kwd') . '%'));
        $startTime = strtotime(I('add_time'));
        $endTime = strtotime(I('end_time'));
        if ($startTime && $endTime) {
            $condition['zf_time'] = array('between', array($startTime, $endTime + 86400));
        } elseif ($startTime > 0) {
            $condition['zf_time'] = array('gt', $startTime);
        } elseif ($endTime > 0) {
            $condition['zf_time'] = array('lt', $endTime);
        }
        $condition['uid'] = $this->user_id;
        $model = M('block_log');
        $list1 = $model->where($condition)->getField('id, is_type');
        $count = count($list1);
        if (IS_AJAX) {
            $p = I('p');
            $pSize = 10;
            $sort_order = (I('order') ? I('order') : 'id') . ' ' . (I('sort') ? I('sort') : 'desc');
            $list = $model->where($condition)->limit($p * $pSize, $pSize)->order($sort_order)->select();
            $this->assign('list', $list);
            $this->display('blockLogAjax');
        } else {
            $this->assign('count', $count);
            $this->display('blockLog');
        }
    }

    private function ctime($time, $min = "30m") {
        $date = date("Y-m-", $time);
        $d = date('d', $time);
        $h = date('H', $time);
        $h1 = date('G', $time);
        $d1 = ltrim($d, '0');
        $m = ltrim(date('i', $time), '0');
        switch ($min) {
            case '1min':
                if ($m <= 1) {
                    $tTime = $date . $d . ' ' . $h . ':01:00';
                } elseif ($m <= 2) {
                    $tTime = $date . $d . ' ' . $h . ':02:00';
                } elseif ($m <= 3) {
                    $tTime = $date . $d . ' ' . $h . ':03:00';
                } elseif ($m <= 4) {
                    $tTime = $date . $d . ' ' . $h . ':04:00';
                } elseif ($m <= 5) {
                    $tTime = $date . $d . ' ' . $h . ':05:00';
                } elseif ($m <= 6) {
                    $tTime = $date . $d . ' ' . $h . ':06:00';
                } elseif ($m <= 7) {
                    $tTime = $date . $d . ' ' . $h . ':07:00';
                } elseif ($m <= 8) {
                    $tTime = $date . $d . ' ' . $h . ':08:00';
                } elseif ($m <= 9) {
                    $tTime = $date . $d . ' ' . $h . ':09:00';
                } elseif ($m <= 10) {
                    $tTime = $date . $d . ' ' . $h . ':10:00';
                } elseif ($m <= 11) {
                    $tTime = $date . $d . ' ' . $h . ':11:00';
                } elseif ($m <= 12) {
                    $tTime = $date . $d . ' ' . $h . ':12:00';
                } elseif ($m <= 13) {
                    $tTime = $date . $d . ' ' . $h . ':13:00';
                } elseif ($m <= 14) {
                    $tTime = $date . $d . ' ' . $h . ':14:00';
                } elseif ($m <= 15) {
                    $tTime = $date . $d . ' ' . $h . ':15:00';
                } elseif ($m <= 16) {
                    $tTime = $date . $d . ' ' . $h . ':16:00';
                } elseif ($m <= 17) {
                    $tTime = $date . $d . ' ' . $h . ':17:00';
                } elseif ($m <= 18) {
                    $tTime = $date . $d . ' ' . $h . ':18:00';
                } elseif ($m <= 19) {
                    $tTime = $date . $d . ' ' . $h . ':19:00';
                } elseif ($m <= 20) {
                    $tTime = $date . $d . ' ' . $h . ':20:00';
                } elseif ($m <= 21) {
                    $tTime = $date . $d . ' ' . $h . ':21:00';
                } elseif ($m <= 22) {
                    $tTime = $date . $d . ' ' . $h . ':22:00';
                } elseif ($m <= 23) {
                    $tTime = $date . $d . ' ' . $h . ':23:00';
                } elseif ($m <= 24) {
                    $tTime = $date . $d . ' ' . $h . ':24:00';
                } elseif ($m <= 25) {
                    $tTime = $date . $d . ' ' . $h . ':25:00';
                } elseif ($m <= 26) {
                    $tTime = $date . $d . ' ' . $h . ':26:00';
                } elseif ($m <= 27) {
                    $tTime = $date . $d . ' ' . $h . ':27:00';
                } elseif ($m <= 28) {
                    $tTime = $date . $d . ' ' . $h . ':28:00';
                } elseif ($m <= 29) {
                    $tTime = $date . $d . ' ' . $h . ':29:00';
                } elseif ($m <= 30) {
                    $tTime = $date . $d . ' ' . $h . ':30:00';
                } elseif ($m <= 31) {
                    $tTime = $date . $d . ' ' . $h . ':31:00';
                } elseif ($m <= 32) {
                    $tTime = $date . $d . ' ' . $h . ':32:00';
                } elseif ($m <= 33) {
                    $tTime = $date . $d . ' ' . $h . ':33:00';
                } elseif ($m <= 34) {
                    $tTime = $date . $d . ' ' . $h . ':34:00';
                } elseif ($m <= 35) {
                    $tTime = $date . $d . ' ' . $h . ':35:00';
                } elseif ($m <= 36) {
                    $tTime = $date . $d . ' ' . $h . ':36:00';
                } elseif ($m <= 37) {
                    $tTime = $date . $d . ' ' . $h . ':37:00';
                } elseif ($m <= 38) {
                    $tTime = $date . $d . ' ' . $h . ':38:00';
                } elseif ($m <= 39) {
                    $tTime = $date . $d . ' ' . $h . ':39:00';
                } elseif ($m <= 40) {
                    $tTime = $date . $d . ' ' . $h . ':40:00';
                } elseif ($m <= 41) {
                    $tTime = $date . $d . ' ' . $h . ':41:00';
                } elseif ($m <= 42) {
                    $tTime = $date . $d . ' ' . $h . ':42:00';
                } elseif ($m <= 43) {
                    $tTime = $date . $d . ' ' . $h . ':43:00';
                } elseif ($m <= 44) {
                    $tTime = $date . $d . ' ' . $h . ':44:00';
                } elseif ($m <= 45) {
                    $tTime = $date . $d . ' ' . $h . ':45:00';
                } elseif ($m <= 46) {
                    $tTime = $date . $d . ' ' . $h . ':46:00';
                } elseif ($m <= 47) {
                    $tTime = $date . $d . ' ' . $h . ':47:00';
                } elseif ($m <= 48) {
                    $tTime = $date . $d . ' ' . $h . ':48:00';
                } elseif ($m <= 49) {
                    $tTime = $date . $d . ' ' . $h . ':49:00';
                } elseif ($m <= 50) {
                    $tTime = $date . $d . ' ' . $h . ':50:00';
                } elseif ($m <= 51) {
                    $tTime = $date . $d . ' ' . $h . ':51:00';
                } elseif ($m <= 52) {
                    $tTime = $date . $d . ' ' . $h . ':52:00';
                } elseif ($m <= 53) {
                    $tTime = $date . $d . ' ' . $h . ':53:00';
                } elseif ($m <= 54) {
                    $tTime = $date . $d . ' ' . $h . ':54:00';
                } elseif ($m <= 55) {
                    $tTime = $date . $d . ' ' . $h . ':55:00';
                } elseif ($m <= 56) {
                    $tTime = $date . $d . ' ' . $h . ':56:00';
                } elseif ($m <= 57) {
                    $tTime = $date . $d . ' ' . $h . ':57:00';
                } elseif ($m <= 58) {
                    $tTime = $date . $d . ' ' . $h . ':58:00';
                } elseif ($m <= 59) {
                    $tTime = $date . $d . ' ' . $h . ':59:00';
                } else {
                    $tTime = $date . $d . ' ' . ($h1 + 1) . ':00:00';
                }
                break;
            case '3min':
                if ($m <= 3) {
                    $tTime = $date . $d . ' ' . $h . ':03:00';
                } elseif ($m <= 6) {
                    $tTime = $date . $d . ' ' . $h . ':06:00';
                } elseif ($m <= 9) {
                    $tTime = $date . $d . ' ' . $h . ':09:00';
                } elseif ($m <= 12) {
                    $tTime = $date . $d . ' ' . $h . ':12:00';
                } elseif ($m <= 15) {
                    $tTime = $date . $d . ' ' . $h . ':15:00';
                } elseif ($m <= 18) {
                    $tTime = $date . $d . ' ' . $h . ':18:00';
                } elseif ($m <= 21) {
                    $tTime = $date . $d . ' ' . $h . ':21:00';
                } elseif ($m <= 24) {
                    $tTime = $date . $d . ' ' . $h . ':24:00';
                } elseif ($m <= 27) {
                    $tTime = $date . $d . ' ' . $h . ':27:00';
                } elseif ($m <= 33) {
                    $tTime = $date . $d . ' ' . $h . ':33:00';
                } elseif ($m <= 36) {
                    $tTime = $date . $d . ' ' . $h . ':36:00';
                } elseif ($m <= 39) {
                    $tTime = $date . $d . ' ' . $h . ':39:00';
                } elseif ($m <= 42) {
                    $tTime = $date . $d . ' ' . $h . ':42:00';
                } elseif ($m <= 45) {
                    $tTime = $date . $d . ' ' . $h . ':45:00';
                } elseif ($m <= 48) {
                    $tTime = $date . $d . ' ' . $h . ':48:00';
                } elseif ($m <= 51) {
                    $tTime = $date . $d . ' ' . $h . ':51:00';
                } elseif ($m <= 54) {
                    $tTime = $date . $d . ' ' . $h . ':54:00';
                } elseif ($m <= 57) {
                    $tTime = $date . $d . ' ' . $h . ':57:00';
                } else {
                    $tTime = $date . $d . ' ' . ($h1 + 1) . ':00:00';
                }
                break;
            case '5min':
                if ($m <= 5) {
                    $tTime = $date . $d . ' ' . $h . ':05:00';
                } elseif ($m <= 10) {
                    $tTime = $date . $d . ' ' . $h . ':10:00';
                } elseif ($m <= 15) {
                    $tTime = $date . $d . ' ' . $h . ':15:00';
                } elseif ($m <= 20) {
                    $tTime = $date . $d . ' ' . $h . ':20:00';
                } elseif ($m <= 25) {
                    $tTime = $date . $d . ' ' . $h . ':25:00';
                } elseif ($m <= 30) {
                    $tTime = $date . $d . ' ' . $h . ':30:00';
                } elseif ($m <= 35) {
                    $tTime = $date . $d . ' ' . $h . ':35:00';
                } elseif ($m <= 40) {
                    $tTime = $date . $d . ' ' . $h . ':40:00';
                } elseif ($m <= 45) {
                    $tTime = $date . $d . ' ' . $h . ':45:00';
                } elseif ($m <= 50) {
                    $tTime = $date . $d . ' ' . $h . ':50:00';
                } elseif ($m <= 55) {
                    $tTime = $date . $d . ' ' . $h . ':55:00';
                } else {
                    $tTime = $date . $d . ' ' . ($h1 + 1) . ':00:00';
                }
                break;
            case '15min':
                if ($m <= 15) {
                    $tTime = $date . $d . ' ' . $h . ':15:00';
                } elseif ($m <= 30) {
                    $tTime = $date . $d . ' ' . $h . ':30:00';
                } elseif ($m <= 45) {
                    $tTime = $date . $d . ' ' . $h . ':45:00';
                } else {
                    $tTime = $date . $d . ' ' . ($h1 + 1) . ':00:00';
                }
                break;
            case '30min';
                if ($m <= 30) {
                    $tTime = $date . $d . ' ' . $h . ':30:00';
                } else {
                    $tTime = $date . $d . ' ' . ($h1 + 1) . ':00:00';
                }
                break;
            case '1hour':
                if ($h1 < 1) {
                    $tTime = $date . $d . ' 01:00:00';
                } elseif ($h1 < 2) {
                    $tTime = $date . $d . ' 02:00:00';
                } elseif ($h1 < 3) {
                    $tTime = $date . $d . ' 03:00:00';
                } elseif ($h1 < 4) {
                    $tTime = $date . $d . ' 04:00:00';
                } elseif ($h1 < 5) {
                    $tTime = $date . $d . ' 05:00:00';
                } elseif ($h1 < 6) {
                    $tTime = $date . $d . ' 06:00:00';
                } elseif ($h1 < 7) {
                    $tTime = $date . $d . ' 07:00:00';
                } elseif ($h1 < 8) {
                    $tTime = $date . $d . ' 08:00:00';
                } elseif ($h1 < 9) {
                    $tTime = $date . $d . ' 09:00:00';
                } elseif ($h1 < 10) {
                    $tTime = $date . $d . ' 10:00:00';
                } elseif ($h1 < 11) {
                    $tTime = $date . $d . ' 11:00:00';
                } elseif ($h1 < 12) {
                    $tTime = $date . $d . ' 12:00:00';
                } elseif ($h1 < 13) {
                    $tTime = $date . $d . ' 13:00:00';
                } elseif ($h1 < 14) {
                    $tTime = $date . $d . ' 14:00:00';
                } elseif ($h1 < 15) {
                    $tTime = $date . $d . ' 15:00:00';
                } elseif ($h1 < 16) {
                    $tTime = $date . $d . ' 16:00:00';
                } elseif ($h1 < 17) {
                    $tTime = $date . $d . ' 17:00:00';
                } elseif ($h1 < 18) {
                    $tTime = $date . $d . ' 18:00:00';
                } elseif ($h1 < 19) {
                    $tTime = $date . $d . ' 19:00:00';
                } elseif ($h1 < 20) {
                    $tTime = $date . $d . ' 20:00:00';
                } elseif ($h1 < 21) {
                    $tTime = $date . $d . ' 21:00:00';
                } elseif ($h1 < 22) {
                    $tTime = $date . $d . ' 22:00:00';
                } elseif ($h1 < 23) {
                    $tTime = $date . $d . ' 23:00:00';
                } else {
                    $tTime = $date . ($d + 1) . ' 00:00:00';
                }
                break;
            case '2hour':
                if ($h1 <= 2) {
                    $tTime = $date . $d . ' 02:00:00';
                } elseif ($h1 <= 4) {
                    $tTime = $date . $d . ' 04:00:00';
                } elseif ($h1 <= 6) {
                    $tTime = $date . $d . ' 06:00:00';
                } elseif ($h1 <= 8) {
                    $tTime = $date . $d . ' 08:00:00';
                } elseif ($h1 <= 10) {
                    $tTime = $date . $d . ' 10:00:00';
                } elseif ($h1 <= 12) {
                    $tTime = $date . $d . ' 12:00:00';
                } elseif ($h1 <= 14) {
                    $tTime = $date . $d . ' 14:00:00';
                } elseif ($h1 <= 16) {
                    $tTime = $date . $d . ' 16:00:00';
                } elseif ($h1 <= 18) {
                    $tTime = $date . $d . ' 18:00:00';
                } elseif ($h1 <= 20) {
                    $tTime = $date . $d . ' 20:00:00';
                } elseif ($h1 <= 22) {
                    $tTime = $date . $d . ' 22:00:00';
                } else {
                    $tTime = $date . ($d + 1) . ' 00:00:00';
                }
                break;
            case '4hour':
                if ($h1 <= 4) {
                    $tTime = $date . $d . ' 04:00:00';
                } elseif ($h1 <= 8) {
                    $tTime = $date . $d . ' 08:00:00';
                } elseif ($h1 <= 12) {
                    $tTime = $date . $d . ' 12:00:00';
                } elseif ($h1 <= 16) {
                    $tTime = $date . $d . ' 16:00:00';
                } elseif ($h1 <= 20) {
                    $tTime = $date . $d . ' 20:00:00';
                } else {
                    $tTime = $date . ($d + 1) . ' 00:00:00';
                }
                break;
            case '6hour':
                if ($h1 <= 6) {
                    $tTime = $date . $d . ' 08:00:00';
                } elseif ($h1 <= 12) {
                    $tTime = $date . $d . ' 12:00:00';
                } elseif ($h1 <= 18) {
                    $tTime = $date . $d . ' 18:00:00';
                } else {
                    $tTime = $date . ($d + 1) . ' 00:00:00';
                }
                break;
            case '12hour':
                if ($h1 <= 12) {
                    $tTime = $date . $d . ' 12:00:00';
                } else {
                    $tTime = $date . ($d + 1) . ' 00:00:00';
                }
                break;
            case '1day':
                if ($d1 < 1) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 2) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 3) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 4) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 5) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 6) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 7) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 8) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 9) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 10) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 11) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 12) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 13) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 14) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 15) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 16) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 17) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 18) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 19) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 20) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 21) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 22) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 23) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 24) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 25) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 26) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 27) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 28) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 29) {
                    $tTime = $date . $d . ' 23:59:59';
                } elseif ($d1 < 30) {
                    $tTime = $date . $d . ' 23:59:59';
                } else {
                    $tTime = $date . $d . ' 23:59:59';
                }
                break;
        }
        return $tTime;
    }

}
