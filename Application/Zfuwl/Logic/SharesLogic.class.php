<?php

/**
 *
 * ============================================================================
 *
 *
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author:
 * Date:
 */

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class SharesLogic extends RelationModel {

    protected $tabName;

    public function __construct()
    {
        $this->tabName  = NUOYILIANNAME.'.shares';
    }

    /**
     *  修改股票当前的交易价格
     * @param $user
     * @return array
     */
    public function editNowPrice($post) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '请刷新后重试');
        }
        $info = sharesInfo($post['id']);
        if ($info['now_price'] != $post['name']) {
            unifiedBuyShanes($post['id'], $post['name']);
            $infoId = M(NUOYILIANNAME.'.shares')->where(array('id' => $post['id']))->save(array('now_price' => $post['name']));
        }
        if (!$infoId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    /**
     *  股票参数添加修改
     * @param array $post 提交数据
     * @return array
     */
    public function addShares($post) {
        $post['total'] = floatval($post['total']);
        if ($post['total'] <= 0) {
            return array('status' => -1, 'msg' => '发行数量不能为空');
        }
        if ($post['shares_price'] == '' || $post['shares_price'] <= 0) {
            return array('status' => -1, 'msg' => '发行价格不能为空或者不能为0');
        }
        if ($post['now_price'] <= 0) {
            return array('status' => -1, 'msg' => '当前交易价格不能为空或者0');
        }
        if ($post['rise_auto'] <= 0) {
            return array('status' => -1, 'msg' => '涨价机制选择错误');
        }
//        if ($post['max_out_price'] <= 0) {
//            return array('status' => -1, 'msg' => '最高卖出价不能为空或者为0');
//        }
//        if ($post['max_add_price'] <= 0) {
//            return array('status' => -1, 'msg' => '最高买入价不能为空或者为0');
//        }
//        if ($post['min_add_price'] <= 0) {
//            return array('status' => -1, 'msg' => '最低买入价不能为空或者为0');
//        }
//        if ($post['min_out_price'] <= 0) {
//            return array('status' => -1, 'msg' => '最低卖出价不能为空或者为0');
//        }
//
//        if ($post['min_add_price'] <= 0) {
//            return array('status' => -1, 'msg' => '最低买入价不能为空或者为0');
//        }
//        if ($post['min_out_price'] <= 0) {
//            return array('status' => -1, 'msg' => '最低卖出价不能为空或者为0');
//        }
//        if ($post['trade_add_time'] > 23 || $post['trade_add_time'] < 0) {
//            return array('status' => -1, 'msg' => '交易时间不能填写错误，正常 1 - 23');
//        }
//        if ($post['trade_out_time'] > 23 || $post['trade_out_time'] < 0) {
//            return array('status' => -1, 'msg' => '交易时间不能填写错误，正常 1 - 23');
//        }
//        if ($post['split_auto'] == 1) {
//            if ($post['split_price'] <= 0) {
//                return array('status' => -1, 'msg' => '自动拆分价不能为空或者为0');
//            }
//            if ($post['split_per'] <= 0) {
//                return array('status' => -1, 'msg' => '拆分倍数不能为空或者为0');
//            }
//            if ($post['split_fall_price'] <= 0) {
//                return array('status' => -1, 'msg' => '拆分后价格不能为空或者为0');
//            }
//        }
        $data = array(
            'name_cn' => $post['name_cn'], // 股票中文名
            'name_en' => $post['name_en'], // 股票英文名
            'total' => floatval($post['total']), // 发行总量
            'thigh' => $post['thigh'], // 股票单位
            'shares_price' => floatval($post['shares_price']), // 发行价格
            'now_price' => floatval($post['now_price']), // 当前交易价
            'rise_auto' => $post['rise_auto'] == 1 ? 1 : 2, // 涨价机制 1 自动 2手动
            'split_auto' => $post['split_auto'] == 1 ? 1 : 2, // 交易模式 1自动 2手动
            'trade_add_time' => floatval($post['trade_add_time']), // 交易开始时间
            'trade_out_time' => floatval($post['trade_out_time']), // 交易关闭时间
//            'trade_auto' => $post['trade_auto'] == 1 ? 1 : 2, // 是否自动拆分 1是 2否
//            'split_price' => floatval($post['split_price']), // 自动拆分价
//            'split_per' => floatval($post['split_per']), // 拆分倍数
//            'split_fall_price' => floatval($post['split_fall_price']) // 拆分后价格
        );
        if ($post['id'] > 0) {
            $levelId = M(NUOYILIANNAME.'.shares')->where(array('id' => $post['id']))->save($data);
        } else {
            $levelId = M(NUOYILIANNAME.'.shares')->add($data);
        }
        if (!$levelId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    /*
     * 股票交易价格设置
     */

    public function addSharesConfig($post) {
        if ($post['shares_id'] == '') {
            return array('status' => -1, 'msg' => '股票选择不能为空');
        }
        if ($post['money_id'] == '') {
            return array('status' => -1, 'msg' => '购买钱包不能为空');
        }
        if ($post['sell_per1'] == 0 && $post['sell_per2'] == 0 && $post['sell_per3'] == 0 && $post['sell_per4'] == 0 && $post['sell_per5'] == 0) {
            return array('status' => -1, 'msg' => '分配比例必须其中一个');
        }
        $per = $post['sell_per1'] + $post['sell_per2'] + $post['sell_per3'] + $post['sell_per4'] + $post['sell_per5'];
        if ($per > 100 || $per < 100) {
            return array('status' => -1, 'msg' => '分配比例必须大于等于 100，当前' . $per);
        }
        if ($post['sell_money1'] == '') {
            return array('status' => -1, 'msg' => '分配钱包必须填写');
        }
        if ($post['buy_low'] < 1 || $post['buy_bei'] < 1 || $post['buy_fee'] == '') {
            return array('status' => -1, 'msg' => '买家参数设置错误');
        }
        if ($post['sell_low'] < 1 || $post['sell_bei'] < 1 || $post['sell_fee'] == '') {
            return array('status' => -1, 'msg' => '卖家参数设置错误');
        }
        if ($post['sell_money1'] > 0 && $post['sell_money2'] > 0) {
            if ($post['sell_money1'] == $post['sell_money2']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money1'] > 0 && $post['sell_money3'] > 0) {
            if ($post['sell_money1'] == $post['sell_money3']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money1'] > 0 && $post['sell_money4'] > 0) {
            if ($post['sell_money1'] == $post['sell_money4']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money1'] > 0 && $post['sell_money5'] > 0) {
            if ($post['sell_money1'] == $post['sell_money5']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money2'] > 0 && $post['sell_money3'] > 0) {
            if ($post['sell_money2'] == $post['sell_money3']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money2'] > 0 && $post['sell_money4'] > 0) {
            if ($post['sell_money2'] == $post['sell_money4']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money2'] > 0 && $post['sell_money5'] > 0) {
            if ($post['sell_money2'] == $post['sell_money5']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money3'] > 0 && $post['sell_money4'] > 0) {
            if ($post['sell_money3'] == $post['sell_money4']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money3'] > 0 && $post['sell_money5'] > 0) {
            if ($post['sell_money3'] == $post['sell_money5']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }
        if ($post['sell_money4'] > 0 && $post['sell_money5'] > 0) {
            if ($post['sell_money4'] == $post['sell_money5']) {
                return array('status' => -1, 'msg' => '钱包选择错误');
            }
        }

        $data['shares_id'] = $post['shares_id'] ? $post['shares_id'] : 0;
        $data['money_id'] = $post['money_id'] ? $post['money_id'] : 0;
        $data['sell_low'] = $post['sell_low'] ? $post['sell_low'] : 0;
        $data['sell_bei'] = $post['sell_bei'] ? $post['sell_bei'] : 0;
        $data['sell_fee'] = $post['sell_fee'] ? $post['sell_fee'] : 0;
        $data['sell_fd'] = $post['sell_fd'] ? $post['sell_fd'] : 0;
        $data['buy_low'] = $post['buy_low'] ? $post['buy_low'] : 0;
        $data['buy_bei'] = $post['buy_bei'] ? $post['buy_bei'] : 0;
        $data['buy_fee'] = $post['buy_fee'] ? $post['buy_fee'] : 0;
        $data['buy_fd'] = $post['buy_fd'] ? $post['buy_fd'] : 0;

        $data['sell_low_ch'] = $post['sell_low_ch'] ? $post['sell_low_ch'] : 0;
        $data['sell_bei_ch'] = $post['sell_bei_ch'] ? $post['sell_bei_ch'] : 0;
        $data['sell_fee_ch'] = $post['sell_fee_ch'] ? $post['sell_fee_ch'] : 0;
        $data['sell_fd_ch'] = $post['sell_fd_ch'] ? $post['sell_fd_ch'] : 0;
        $data['buy_low_ch'] = $post['buy_low_ch'] ? $post['buy_low_ch'] : 0;
        $data['buy_bei_ch'] = $post['buy_bei_ch'] ? $post['buy_bei_ch'] : 0;
        $data['buy_fee_ch'] = $post['buy_fee_ch'] ? $post['buy_fee_ch'] : 0;
        $data['buy_fd_ch'] = $post['buy_fd_ch'] ? $post['buy_fd_ch'] : 0;

        $data['sell_per1'] = $post['sell_per1'] ? $post['sell_per1'] : 0;
        $data['sell_money1'] = $post['sell_money1'] ? $post['sell_money1'] : 0;
        $data['sell_per2'] = $post['sell_per2'] ? $post['sell_per2'] : 0;
        $data['sell_money2'] = $post['sell_money2'] ? $post['sell_money2'] : 0;
        $data['sell_per3'] = $post['sell_per3'] ? $post['sell_per3'] : 0;
        $data['sell_money3'] = $post['sell_money3'] ? $post['sell_money3'] : 0;
        $data['sell_per4'] = $post['sell_per4'] ? $post['sell_per4'] : 0;
        $data['sell_money4'] = $post['sell_money4'] ? $post['sell_money4'] : 0;
        $data['sell_per5'] = $post['sell_per5'] ? $post['sell_per5'] : 0;
        $data['sell_money5'] = $post['sell_money5'] ? $post['sell_money5'] : 0;
        if ($post['id'] > 0) {
            $res = D(NUOYILIANNAME.'.shares_config')->where('id=' . $post['id'])->save($data);
            if ($res) {
                return array('status' => 1, 'msg' => '修改成功');
            } else {
                return array('status' => -1, 'msg' => '修改失败');
            }
        } else {
            if (D(NUOYILIANNAME.'.shares_config')->where(array('shares_id' => $post['shares_id'], 'money_id' => $post['money_id']))->count() > 0) {
                return array('status' => -1, 'msg' => '操作失败，原因：己存在当条规则记录');
            } else {
                $res = D(NUOYILIANNAME.'.shares_config')->add($data);
                if ($res) {
                    return array('status' => 1, 'msg' => '添加成功');
                } else {
                    return array('status' => -1, 'msg' => '添加失败');
                }
            }
        }
    }

    /**
     * 委拖买入股票
     * @param int $userId 会员id
     * @param array $post 提交的数据
     * @return array 返回结果
     */
    public function sharesBuy($userId, $post) {
        $post['money_id'] = intval($post['money_id']);
        if ($post['money_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择钱包');
        }
        $user = getUserInfo($userId, 0);
        $config = M(NUOYILIANNAME.'.shares_config')->where(array('shares_id' => $post['buy_shares_id'], 'is_type' => 1))->find();

        if (zfCache('securityInfo.gp_buy_sc_wc_jx') == 1 && $config['money_id'] != $post['money_id']) {
            $lastBuyInfo = M(NUOYILIANNAME.'.shares_buy')->where(array('user_id' => $user['user_id'], 'pt' => PTVAL))->order("id desc")->find();
            if ($lastBuyInfo['status'] <= 2 && $lastBuyInfo) {
                return array('status' => -1, 'msg' => '上次买入交易完成后才能继续买入');
            }
        }
//        if (zfCache('securityInfo.day_buy_num') > 0 && $config['money_id'] != $post['money_id']) {
//            $buyDayNum = M('shares_buy')->where(array('user_id' => $user['user_id'], 'zf_time' => array('egt', strtotime(date('Ymd')))))->count();
//            if ($buyDayNum >= zfCache('securityInfo.day_buy_num')) {
//                return array('status' => -1, 'msg' => '今日挂单已达封顶');
//            }
//        }

        $post['buyMoneyNum'] = floatval($post['buyMoneyNum']);
        if ($post['buy_shares_id'] == '') {
            return array('status' => -1, 'msg' => '类型不能为空');
        }
        if ($post['buyMoneyNum'] == '') {
            return array('status' => -1, 'msg' => '请输入数量');
        }
//        if ($post['buyPrice'] == '') {
//            return array('status' => -1, 'msg' => '请输入购买价格');
//        }
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '交易密码输入错误，请重新输入');
        }
        if ($post['buyMoneyNum'] > xz()) {
            return array('status' => -1, 'msg' => '不能超过最大买入数量');
        }
//        $userdata = dataInfo($user['data_id']);
//        if (trim($post['number']) != trim($userdata['number'])) {
//            return array('status' => -1, 'msg' => '证件输入错误，请重新输入');
//        }

        $shares = M(NUOYILIANNAME.'.shares')->where(array('id' => $post['buy_shares_id']))->find();
        $shaersInfo = sharesInfo($config['shares_id']);
        $price = $shaersInfo['now_price'];
//        if ($shaersInfo['split_num'] >= 3) {
//            $buyLow = floatval($config['buy_low_ch']);
//            $buyBei = floatval($config['buy_bei_ch']);
//            $buyFee = floatval($config['buy_fee_ch']);
//            $buyFd = floatval($config['buy_fd_ch']);
//        } else {
        $buyLow = floatval($config['buy_low']);
        $buyBei = floatval($config['buy_bei']);
        $buyFee = floatval($config['buy_fee']);
        $buyFd = floatval($config['buy_fd']);
//        }
//        $post['buyNum'] = floatval($post['buyMoneyNum'] / ($price + ($price * $config['buy_fee'] / 100)));
//        $post['buyMoneyNum'] = intval($post['buyMoneyNum'] / ($price + ($price * $config['buy_fee'] / 100)));
//        if ($post['buyMoneyNum'] < $buyLow || $post['buyMoneyNum'] % $buyBei != 0 || $post['buyMoneyNum'] > $buyFd) {
//            return array('status' => -1, 'msg' => '最低' . $buyLow . '并且是' . $buyBei . '的倍数, 最多' . $buyFd);
//        }
        # 当前价位 还能买多少 股
//        if ($shares['rise_auto'] == 1) {
//            $sureNum = intval(sharesSureNum($post['buy_shares_id']));
//            if ($sureNum < $post['buyNum']) {
//                return array('status' => -1, 'msg' => '当前价位最多买入' . $sureNum);
//            }
//        }
//        $list = sharesList($post['buy_shares_id']); // 查出当条股票的 参数
//        if ($post['buyPrice'] < $list['min_add_price']) {
//            return array('status' => -1, 'msg' => '你的价格低于最低买入价格' . $list['min_add_price']);
//        }
//        if ($post['buyPrice'] > $list['max_add_price']) {
//            return array('status' => -1, 'msg' => '你的价格大于最高买入价格' . $list['max_add_price']);
//        }
        // 查出当前存在交易没有完成的 订金总额  加上 本次购买数量的总额   判断当前余额是否小于 未交易完成的金额
//        $buyList = M('shares_buy')->where(array('user_id' => $userId, 'status' => array('lt', 3)))->select();
//        if ($buyList) {
//            foreach ($buyList as $v) {
//                $buyMoney = $v['out_num'] * $v['price'];
//                $addMoney += $buyMoney;
//            }
//        }
//        $stayNum = M('shares_buy')->where(array('user_id' => $user_id, 'status' => array('lt', 3)))->sum('out_num');
//        $level = M('users_level')->where(array('level_id' => $user['level_id']))->find();
//        if (($stayNum + $post['buyNum']) > $level['t_shares']) {
//            return array('status' => -1, 'msg' => '当前级别购买股己封顶，请升级在购买');
//        }


        $money = $post['buyMoneyNum'] * $shares['now_price'];
        # 开启 买家手续费
        if ($buyFee > 0) {
//            $note = '，手续费' . $buyFee . '%';
            $buyFee = $money * $buyFee / 100;
            $buyMoney = $money + $buyFee;
        } else {
            $buyMoney = $money;
        }
        if (usersMoney($userId, $post['money_id'], 1) < $buyMoney) {
            return array('status' => -1, 'msg' => moneyList($post['money_id'], 1) . '余额不足');
        } else {
            $data = array(
                'shares_id' => $post['buy_shares_id'],
                'user_id' => $userId,
                'zf_time' => time(),
                'num' => $post['buyMoneyNum'],
                'out_num' => $post['buyMoneyNum'],
                'price' => $shares['now_price'],
                'total' => $shares['now_price'] * $post['buyMoneyNum'],
                'poundage' => $config['buy_fee'],
                'status' => 1,
                'pt' => PTVAL
            );
//            $data['shares_id'] = $post['buy_shares_id'];
//            $data['user_id'] = $userId;
//            $data['zf_time'] = time();
//            $data['num'] = $post['buyNum'];
//            $data['out_num'] = $post['buyNum'];
//            $data['price'] = $post['buyPrice'];
//            $data['total'] = $post['buyPrice'] * $post['buyNum'];
//            $data['poundage'] = $config['buy_fee'];
//            $data['status'] = 1;
            # 发起扣款交易
            userMoneyLogAdd($userId, $post['money_id'], '-' . $buyMoney, 115, '申请买入' . $post['buyMoneyNum'] . '个诺一链');
            // userMoneyAddLog($userId, $post['money_id'], '-' . $buyMoney, 0, 115, '申请买入' . $post['buyMoneyNum'] . '个诺一链'); // 发起扣款交易
            usersDay($userId, 'out_' . $post['money_id'], $buyMoney); // 会员消费的费用
            $res = M(NUOYILIANNAME.'.shares_buy')->add($data);
        }
        if ($res) {
//            $num = M('users_give')->where(array('user_id' => $userId))->count();
            # 达到赠送条件赠送
//            if (zfCache('securityInfo.zs_kg') == 1 && $post['buyMoneyNum'] == zfCache('securityInfo.buy_Dollar')) {
//                giveMoney($userId, zfCache('securityInfo.buy_mz_zs_money'), zfCache('securityInfo.buy_mz_zs_money_id'), $res, $post['buyMoneyNum'], '购买' . $shares['name_cn'] . '达到条件');
//                if ($user['tjr_id'] > 0) {
//                    $money = zfCache('securityInfo.buy_mz_zs_money') * zfCache('securityInfo.tuiguang') / 100;
//                    userMoneyAddLog($user['tjr_id'], zfCache('securityInfo.buy_mz_zs_money_id'), $money, 0, 116, $user['account'] . '购买' . $shares['name_cn']);
//                }
//            }
//            if (zfCache('securityInfo.gupiao_rc') != 1) {
            sharesAddPp($userId, $res, $config);
//            }
            sharesDay($post['buyNum'], 0, 0);
            return array('status' => 1, 'msg' => '提交成功');
        } else {
            return array('status' => -1, 'msg' => '提交失败，请刷新页面后重试');
        }
    }

    /**
     * 买入 撤回
     * @param type $user_id
     * @param type $id
     */
    public function buyWithdraw($uId, $data) {
        $list = M('shares_buy')->where(array('id' => $data['id'], 'user_id' => $uId))->find();
        if ($list['status'] < 3 && $list['out_num'] > 0) {
            $config = M('shares_config')->where(array('shares_id' => $list['shares_id'], 'is_type' => 1))->find();
            $res = M('shares_buy')->where(array('id' => $data['id'], 'user_id' => $uId))->save(array('status' => 3, 'return_time' => time(), 'return_num' => $list['out_num']));
            $money = ($list['out_num'] * $list['price']) + (($list['out_num'] * $list['price']) * $list['poundage'] / 100);
            if ($money > 0) {
                userMoneyAddLog($uId, $config['money_id'], $money, 0, 115, '买入撤回' . $list['out_num'] . '股'); // 发起扣款交易
            }
            if ($res) {
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                return array('status' => -1, 'msg' => '操作失败');
            }
        } else {
            return array('status' => -1, 'msg' => '请勿重复提交');
        }
    }

    /**
     * 委托卖出股票
     * @param int $userId 会员id
     * @param array $post 提交的数据
     * @return array 操作结果
     */
    public function sharesSell($userId, $post) {
//        set_time_limit(86400);
        $user = getUserInfo($userId, 0);

        if (zfCache('securityInfo.gp_sell_sc_wc_jx') == 1) {
            $lastSellInfo = M(NUOYILIANNAME.'.shares_sell')->where(array('user_id' => $user['user_id'], 'pt' => PTVAL))->order("id desc")->find();
            if ($lastSellInfo['status'] != 9 && $lastSellInfo) {
                return array('status' => -1, 'msg' => '上次买入交易完成后才能继续卖出');
            }
        }
        if (zfCache('securityInfo.day_sell_num') > 0) {
            $sellDayNum = M(NUOYILIANNAME.'.shares_sell')->where(array('user_id' => $user['user_id'], 'zf_time' => array('egt', strtotime(date('Ymd'))), 'pt' => PTVAL))->count();
            if ($sellDayNum >= zfCache('securityInfo.day_sell_num')) {
                return array('status' => -1, 'msg' => '今日挂单已达封顶');
            }
        }
        if (zfCache('securityInfo.is_gp_sell_cf') == 1) {
            $userShares = M(NUOYILIANNAME.'.shares_user')->where(array('user_id' => $user['user_id'], 'shares_id' => $post['sell_shares_id'], 'pt' => PTVAL))->field('split_cs')->find();
            if (intval($userShares['split_cs']) <= 0) {
                return array('status' => -1, 'msg' => '暂时不能卖出');
            }
        }
        if ($post['sell_shares_id'] == '') {
            return array('status' => -1, 'msg' => '类型不能为空');
        }
        if ($post['sellNum'] == '') {
            return array('status' => -1, 'msg' => '请输入你要卖出的数量');
        }
        if ($post['sell_shares_id'] == '') {
            return array('status' => -1, 'msg' => '请选择要卖出的股票');
        }
//        if ($post['sellPrice'] == '') {
//            return array('status' => -1, 'msg' => '请输入价格');
//        }
        if ($post['sellNum'] > mcxz()) {
            return array('status' => -1, 'msg' => '卖出数量不能大于最大卖出数量');
        }
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '交易密码输入错误，请重新输入');
        }

//        $userdata = dataInfo($user['data_id']);
//        if (trim($post['number']) != trim($userdata['number'])) {
//            return array('status' => -1, 'msg' => '证件输入错误，请重新输入');
//        }

        $config = M(NUOYILIANNAME.'.shares_config')->where(array('shares_id' => $post['sell_shares_id'], 'is_type' => 1))->find();
        $shares = M(NUOYILIANNAME.'.shares')->where(array('id' => $post['sell_shares_id']))->find();
//        if ($shares['split_num'] >= 3) {
//            $sellLow = floatval($config['sell_low_ch']);
//            $sellBei = floatval($config['sell_bei_ch']);
//            $sellFee = floatval($config['sell_fee_ch']);
//            $sellFd = floatval($config['sell_fd_ch']);
//        } else {
        $sellLow = floatval($config['sell_low']);
        $sellBei = floatval($config['sell_bei']);
        $sellFee = floatval($config['sell_fee']);
        $sellFd = floatval($config['sell_fd']);
//        }
        if ($post['sellNum'] < $sellLow || $post['sellNum'] % $sellBei != 0 || $post['sellNum'] > $sellFd) {
            return array('status' => -1, 'msg' => '最低' . $sellLow . '并且是' . $sellBei . '的倍数, 最多' . $sellFd);
        }

//        $list = sharesList($post['sell_shares_id']);
//        if ($post['sellPrice'] < $list['min_out_price']) {
//            return array('status' => -1, 'msg' => '你的价格低于最低买入价格' . $list['min_out_price']);
//        }
//        if ($post['sellPrice'] > $list['max_out_price']) {
//            return array('status' => -1, 'msg' => '你的价格大于最高卖出价格' . $list['max_out_price']);
//        }
        $total = M(NUOYILIANNAME.'.shares_user')->where(array('user_id' => $userId, 'shares_id' => $post['sell_shares_id'], 'pt' => PTVAL))->find();
        # 获取会员够买股票花费了多少钱
//        $buyShareMoney = abs(floatval(M('users_money_log')->where(array('user_id' => $user['user_id'], 'is_type' => 115, 'money' => array('lt', 0)))->sum('money')));
//        # 获取会员已卖出的股票
//        $sold = floatval(M('shares_sell')->where(array('user_id' => $user['user_id'], 'status' => array('not in', array(3))))->sum('num'));
//        $allMoney = ($total['money']+$sold)*$shares['now_price'];
//        if($allMoney/2 >= $buyShareMoney) {
//            $maxSellShare = ($sold+$total['money']) * zfCache('securityInfo.sell_shares_max_per') / 100;
//            if($maxSellShare <= $sold) {
//                return array('status' => -1, 'msg' => '暂时不能卖出');
//            }
//            if($post['sellNum']+$sold > $maxSellShare) {
//                return array('status' => -1, 'msg' => '最多还能卖出'.($maxSellShare-$sold).'股');
//            }
//        } else {
//            return array('status' => -1, 'msg' => '暂时不能卖出');
//        }
        if ($total['money'] < $post['sellNum']) {
            return array('status' => -1, 'msg' => '本次最多只能卖出' . $total['money'] . '股');
        } else {
            $data = array(
                'shares_id' => $post['sell_shares_id'], // 股票类型
                'user_id' => $userId, // 会员id
                'zf_time' => time(), // 挂卖时间
                'num' => $post['sellNum'], // 交易数量
                'out_num' => $post['sellNum'], // 剩余交易数量
                'price' => $shares['now_price'], // 交易价格
                'total' => $shares['now_price'] * $post['sellNum'], // 总金额
                'poundage' => $sellFee,
                'status' => 1,
                'pt' => PTVAL
            );
            $res = M(NUOYILIANNAME.'.shares_sell')->add($data);
            if ($res) {
//                M('shares_user')->where(array('user_id' => $user_id, 'shares_id' => $post['sell_shares_id']))->setDec('money', $post['sellNum']);
                sharesDay(0, $post['sellNum'], 0);
                sharesLog($userId, $post['sell_shares_id'], '-' . $post['sellNum'], 0, 3, '卖出');
                sharesOutPp($userId, $res, $config);
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                return array('status' => -1, 'msg' => '操作失败，请刷新手重试');
            }
        }
    }

    /**
     *  股票资讯添加修改
     * @param $user
     * @return array
     */
    public function addAboutInfo($post) {
        if ($post['title'] == '') {
            return array('status' => -1, 'msg' => '标题不能为空');
        }
        if ($post['content'] == '') {
            return array('status' => -1, 'msg' => '内容不能为空');
        }
        $data['title'] = $post['title'] ? $post['title'] : 0;
        $data['keywords'] = $post['keywords'] ? $post['keywords'] : 0;
        $data['description'] = $post['description'] ? $post['description'] : 0;
        $data['content'] = $post['content'] ? $post['content'] : 0;
        $data['is_type'] = $post['is_type'] ? $post['is_type'] : 1;
        if ($post['id'] > 0) {
            $infoId = M('shares_about')->where(array('id' => $post['id']))->save($data);
        } else {
            $data['add_time'] = time();
            $infoId = M('shares_about')->add($data);
        }
        if (!$infoId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    /**
     * 卖出 撤回
     * @param type $userId
     * @param type $id
     */
    public function sellWithdraw($userId, $data) {
        $list = M('shares_sell')->where(array('id' => $data['id'], 'user_id' => $userId))->find();
//        if ($list['split'] == 1) {
        if ($list['status'] < 3 && $list['out_num'] > 0) {
            $model = new \Think\Model();
            $model->startTrans();
            $res = M('shares_sell')->where(array('id' => $data['id']))->save(array('status' => 3, 'return_time' => time(), 'return_num' => $list['out_num']));
            $ligid = sharesLog($userId, $list['shares_id'], $list['out_num'], 0, 9, '卖出撤回');
            if ($res && $ligid) {
                $model->commit();
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                $model->rollback();
                return array('status' => -1, 'msg' => '操作失败');
            }
        } else {
            return array('status' => -1, 'msg' => '请勿重复提交');
        }
//        } else {
//            return array('status' => -1, 'msg' => '自动出售的不能撤回');
//        }
    }

    /**
     * 股票涨价规则管理
     * @param type $post
     * @return type
     */
    public function sharesRiseInfo($post) {
        $data['shares_id'] = $post['shares_id'];
        $data['trade_num'] = $post['trade_num'];
        $data['out_num'] = $post['out_num'];
        $data['trade_price'] = $post['trade_price'];
        $data['rise_type'] = $post['rise_type'];
        $data['rose_price'] = $post['rose_price'];
        $data['cycle_num'] = $post['cycle_num'];
        if ($post['rise_id'] > 0) {
            $data['last_time'] = time();
            $res = D(NUOYILIANNAME.'.shares_rise')->where('rise_id=' . $post['rise_id'])->save($data);
            if ($res) {
                return array('status' => 1, 'msg' => '修改成功');
            } else {
                return array('status' => -1, 'msg' => '修改失败');
            }
        } else {
            if (D(NUOYILIANNAME.'.shares_rise')->where(array('shares_id' => $post['shares_id'], 'trade_num' => $post['trade_num'], 'trade_price' => $post['trade_price']))->count() > 0) {
                return array('status' => -1, 'msg' => '操作失败，原因：己存在当条规则记录');
            } else {
                $data['zf_time'] = time();
                $res = D(NUOYILIANNAME.'.shares_rise')->add($data);
                if ($res) {
                    return array('status' => 1, 'msg' => '添加成功');
                } else {
                    return array('status' => -1, 'msg' => '添加失败');
                }
            }
        }
    }

    /**
     * 修改会员可售股票数量
     * @param type $data
     * @return type
     */
    public function editUserShares($data) {
        $list = M('shares_user')->where(array('id' => $data['id'], 'user_id' => $data['user_id']))->find();
        if ($data['edit_num'] <= $list['money']) {
            $res = M('shares_user')->where(array('id' => $data['id']))->save(array('sure_num' => $data['edit_num']));
            if ($res) {
                $user = getUserInfo($user_id, 0);
                adminLogAdd('修改' . $user['account'] . '可售数量' . $list['sure_num'] . '为' . $data['edit_num']);
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                return array('status' => -1, 'msg' => '操作失败');
            }
        } else {
            return array('status' => -1, 'msg' => '修改后的可售数量不能大于当前可用数量' . $list['money']);
        }
    }

    /**
     * 添加  a  转  b
     */
    public function changeAddA($post, $user_id) {
        $toAccount = getUserInfo($post['toAccount'], 3);
        if ($user_id == $toAccount['user_id']) {
            return array('status' => -1, 'msg' => '不能自己转让自己');
        }
        if (!$toAccount) {
            return array('status' => -1, 'msg' => $post['toAccount'] . '账号不存在');
        }

        $user = userInfo($user_id);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码 ' . $post['secpwd'] . '验证失败!');
        }

        $userShares = M(NUOYILIANNAME.'.shares_user')->where(array('user_id' => $user_id, 'shares_id' => 1, 'pt' => PTVAL))->find();
        if ($post['num'] > $userShares['money']) {
            return array('status' => -1, 'msg' => '可用余额不足');
        }

        $data['is_type'] = 1; // 会员之间转账
        $data['sid'] = 1;
        $data['uid'] = $user_id;
        $data['to_uid'] = $toAccount['user_id'];
        $data['money'] = $post['num'];
        $data['zf_time'] = time();
        $data['poundage'] = $info['fee'] ? $info['fee'] : 0;
        $data['type_money'] = $post['num'];
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('shares_change_log')->add($data);
        $B = sharesLog($user_id, 1, '-'.$post['num'], 0, 10, '转出至' . $toAccount['account'], '', $toAccount['user_id']);
        $C = sharesLog($toAccount['user_id'], 1, $post['num'], 0, 11, $user['account'] . '转入', '', $user_id);
        if ($A && $B && $C) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

}
