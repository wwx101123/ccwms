<?php

namespace Zfuwl\Controller;

class ExcelController extends CommonController {

    public $condition; // 查询条件

    public function _initialize() {

        parent::_initialize();
        $this->condition = array();
        I('account') && $condition['user_id'] = M('users')->where("account = '" . trim(I('account')) . "'")->getField('user_id');
        I('money_id') && $condition['money_id'] = I('money_id');
        I('is_type') && $condition['is_type'] = I('is_type');
        I('bonus_id') && $condition['bonus_id'] = I('bonus_id');
        $addTime = strtotime(urldecode(trim(I('start_time'))));
        $outTime = strtotime(urldecode(trim(I('end_time'))));
        $timeName = I('timeName') ? I('timeName') : 'zf_time';
        if ($addTime && $outTime) {
            $condition[$timeName] = array('between', array($addTime, $outTime));
        } elseif ($addTime) {
            $condition[$timeName] = array('egt', $addTime);
        } elseif ($outTime) {
            $condition[$timeName] = array('elt', $outTime);
        }
        $this->condition = $condition;
    }

    /**
     * 导出会员余额excel
     */
    public function exportUserMoney() {

        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'user_id' => '会员账号',
            'money_id' => '钱包',
            'money' => '可用金额',
            'frozen' => '冻结金额',
            'total' => '账户总额',
        );

        $userMoneyLog = M('users_money')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($userMoneyLog, 'user_id');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        foreach ($userMoneyLog as $key => $val) {
            $userMoneyLog[$key]['key'] = $key + 1;
            $userMoneyLog[$key]['user_id'] = $userList[$val['user_id']];
            $userMoneyLog[$key]['money_id'] = moneyList($val['money_id']);
        }
        $this->exportExcel($title, $userMoneyLog, '会员余额列表');
    }

    /**
     * 导出会员充值记录excel
     */
    public function exportUserMoneyAddList() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'bank_id' => '汇款银行',
            'user_id' => '会员账号',
            'money_id' => '充值钱包',
            'add_time' => '充值时间',
            'add_money' => '充值金额',
            'money_per' => '汇率',
            'actual_money' => '实际金额',
            'is_type' => '状态',
            'note' => '备注',
            'sh_type' => '审核结果',
            'admin_id' => '审核员',
        );
        $condition = $this->condition;
        I('bank_id') ? $condition['bank_id'] = I('bank_id') : false;
        $userMoneyAddList = M('users_money_add')->where($condition)->order('id desc')->select();
        $userIdArr = getArrColumn($userMoneyAddList, 'user_id');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $bankList = M('admin_bank')->where("is_type=1")->cache('adminBank')->getField('bank_id,bank_opening');
        foreach ($userMoneyAddList as $k => &$v) {
            $adminUser = D("AdminUser")->findUser($v['admin_id']);
            $v['key'] = $k + 1;
            $v['money_id'] = moneyList($v['money_id']);
            $v['bank_id'] = $bankList[$v['bank_id']];
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['user_id'] = $userList[$v['user_id']];
            $v['sh_type'] = $v['is_type'] == 3 ? date('Y-m-d H:i:s', $v['refuse_time']) . '已拒绝 - ' . $v['refuse'] : $v['is_type'] == 1 ? date('Y-m-d H:i:s', $v['affirm_time']) . '- 已确认' : '';
            $v['is_type'] = moneyAddType($v['is_type']);
            $v['admin_id'] = $adminUser['user_name'];
        }
        $this->exportExcel($title, $userMoneyAddList, '会员充值记录');
    }

    /**
     * 导出会员数据excel
     */
    public function exportUserList() {

        $title = array(
            'key' => '序号',
            'user_id' => 'ID',
            'account' => '会员账号',
            'nickname' => '昵称',
            'username' => '姓名',
            'mobile' => '手机号',
            'email' => '邮箱',
            'pass_number' => '身份证号',
            'level' => '等级',
            'tjr_account' => '推荐人',
            'zmd_account' => '报单中心',
            'reg_time' => '注册时间',
            'jh_time' => '激活时间',
            'is_trends' => '动态奖金',
            'is_static' => '静态奖金',
            'is_tk' => '提现状态',
            'is_card' => '认证状态',
            'address' => '联系地址',
            'frozen' => '登录状态',
            'activate' => '激活状态',
            'is_kd' => '空单',
            'is_ht' => '回填',
        );
        $condition = $this->condition;
        $condition['is_type'] = array('neq', DEL_STATUS);
        I('mobile') && $condition['mobile'] = array('like', '%' . trim(I('mobile') . '%'));
        I('email') && $condition['email'] = array('like', '%' . trim(I('email') . '%'));
        $userList = D('users')->where($condition)->order('user_id desc')->select();
        $region = M('region')->getField('id, name_cn');
        $level = M("level")->getField('level_id, name');
        $users = M('users')->getField('user_id, account');
        foreach ($userList as $k => &$v) {
            $user_data = M('users_data')->where(array('id' => $v['data_id']))->find();
            $v['key'] = $k + 1;
            $v['user_id'] = $v['user_id'];
            $v['nickname'] = $v['nickname'];
            $v['username'] = $user_data['username'];
            $v['mobile'] = $user_data['mobile'];
            $v['email'] = $user_data['email'];
            if ($user_data['is_number'] == 2) {
                $number = '未审';
            } else {
                $number = $user_data['pass_number'];
            }
            $v['pass_number'] = $user_data['pass_number'] == '' ? '暂无填写' : $number;
            $v['level'] = $level[$v['level']];
            $v['tjr_account'] = $users[$v['tjr_id']];
            $v['zmd_account'] = $users[$v['bdr_id']];
            $v['reg_time'] = date("Y-m-d H:i:s", $v['reg_time']);
            $v['jh_time'] = $v['is_activate'] == 1 ? date("Y-m-d H:i:s", $v['jh_time']) : '未激活';
            $v['is_trends'] = $v['is_trends'] == 1 ? '允许动态' : '禁止动态';
            $v['is_static'] = $v['is_static'] == 1 ? '允许静态' : '禁止静态';
            $v['is_tk'] = $v['is_static'] == 1 ? '允许提现' : '禁止提现';
            $v['is_card'] = $v['is_card'] == 1 ? '已认证' : '未认证';
            $v['address'] = $region[$v['province']] . ' - ' . $region[$v['city']] . ' - ' . $region[$v['district']] . ' - ' . $region[$v['twon']] . $v['address'];
            $v['frozen'] = $v['frozen'] == 1 ? '允许登录' : '禁止登录';
            $v['activate'] = $v['activate'] == 1 ? '己经激活' : '暂未激活';
            $v['is_kd'] = $v['is_user'] == 2 ? '空单账号' : '否';
            $v['is_ht'] = $v['is_user'] == 3 ? '回填账号' : '否';
        }
        $this->exportExcel($title, $userList, '会员列表');
    }

    /**
     * 导出会员提现列表excel
     */
    public function exportWithdrawalsList() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'account' => '会员账号',
            'add_money' => '申请提现金额',
            'poundage' => '手续费',
            'money_name' => '提现钱包',
            'out_money' => '实际到账金额',
            'add_time' => '申请时间',
            'opening_name' => '银行',
            'bank_name' => '户名',
            'bank_account' => '账号',
            'wx_img' => '微信收款码',
            'zfb_img' => '支付宝收款码',
            'is_type' => '审核结果',
            'admin_name' => '审核员'
        );

        $condition = $this->condition;
        I('opening_id') ? $condition['opening_id'] = I('opening_id') : false;

        $userWithdrawalsList = M('withdrawals')->where($condition)->order('id desc')->select();
        $userIdArr = getArrColumn($userWithdrawalsList, 'user_id');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        foreach ($userWithdrawalsList as $k => &$v) {
            $v['key'] = $k + 1;
            $adminUser = D("AdminUser")->findUser($v['admin_id']);
            $v['account'] = $userList[$v['user_id']];
            $v['money_name'] = moneyList($v['money_id']);
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['opening_name'] = bankOpeningList($v['opening_id']);
            $v['wx_img'] = "<img src='http://" . $_SERVER['HTTP_HOST'] . $v['wx_img'] . "' height='80' />";
            $v['zfb_img'] = "<img src='http://" . $_SERVER['HTTP_HOST'] . $v['zfb_img'] . "' height='80' />";
            $v['admin_name'] = $adminUser['user_name'];
            switch ($v['is_type']) {
                case 1:
                    $v['is_type'] = '已付款';
                    break;
                case 2:
                    $v['is_type'] = '待审核';
                    break;
                case 3:
                    $v['is_type'] = '审核成功' . date('Y-m-d H:i:s', $v['affirm_time']);
                    break;
                case 4:
                    $v['is_type'] = date('Y-m-d H:i:s', $v['refuse_time']) . ' 审核失败! 原因' . $v['refuse'];
                    break;
            }
        }
        $this->exportExcel($title, $userWithdrawalsList, '会员提现列表', '80px');
    }

    # ==================================  #

    /**
     * 导出股票拆分日志
     */
    public function exportSharesSplitLog() {
        $title = array(
            'key' => '序号',
            'sid' => '股票名称',
            'zf_time' => '拆分时间',
            'add_total' => '拆分前总量',
            'add_price' => '拆分前价格',
            'per' => '拆分倍数',
            'out_total' => '拆分后总量',
            'out_price' => '拆分后价格',
            'user_total' => '参与会员数量',
            'note' => '备注'
        );

        $splitLog = M('shares_split')->where($this->condition)->order("id desc")->select();

        $sharesInfo = M('shares')->cache('sharesInfo')->getField('id, name_cn');
        foreach ($splitLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['sid'] = $sharesInfo[$v['sid']];
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
        }
        $this->exportExcel($title, $splitLog, '拆分记录日志');
    }

    /**
     * 导出会员钱包变动日志
     */
    public function exportSharesLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'sid' => '股票名称',
            'zf_time' => '变动时间',
            'money' => '变动金额',
            'type' => '变动类型',
            'total' => '变动后余额',
            'come_uid' => '相关会员',
            'note' => '说明'
        );

        $sharesLogLog = M('shares_log')->where($this->condition)->order("id desc")->select();
        $userA = getArrColumn($sharesLogLog, 'uid');
        $userB = getArrColumn($sharesLogLog, 'come_uid');
        $userIdArr = array_filter(array_merge($userA, $userB));
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $sharesInfo = M('shares')->cache('sharesInfo')->getField('id, name_cn');
        foreach ($sharesLogLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['money'] = $v['money'];
            $v['total'] = $v['total'];
            $v['sid'] = $sharesInfo[$v['sid']];
            $v['type'] = sharesLogType($v['type']);
            $v['come_uid'] = $userList[$v['come_uid']];
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $sharesLogLog, '股票变动明细');
    }

    /**
     * 导出静态投资表excel
     */
    public function exportUserInvestLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'account' => '会员账号',
            'add_time' => '投资时间',
            'price' => '金额',
            'num' => '份数',
            'price_total' => '总金额',
            'type' => '分红方式',
            'zf_time' => '上次分红时间',
            'fh_price' => '上次分红金额',
            'fh_num' => '累计分红天数',
            'total_money' => '累计分红金额',
            'tz_type' => '投资状态',
            'statu' => '分红状态',
        );

        $condition = $this->condition;
        I('statu') ? $condition['statu'] = I('statu') : false;
        I('tz_type') ? $condition['tz_type'] = I('tz_type') : false;

        $userInvestList = M('users_invest')->where($condition)->order('id desc')->select();
        $userIdArr = getArrColumn($userInvestList, 'uid');
        $userIdArr && $userList = M('users')->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        foreach ($userInvestList as $k => &$v) {
            $v['key'] = $k + 1;
            $v['account'] = $userList[$v['user_id']];
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['fh_price'] = $v['zf_time'] > 0 ? $v['fh_price'] : '待分红';
            $v['type'] = $v['type'] == 1 ? '比例' . $v['per'] . '%' : '定额' . $v['per'];
            $v['zf_time'] = $v['zf_time'] > 0 ? date('Y-m-d H:i:s', $v['zf_time']) : '待分红';
            $v['tz_type'] = userInvesTzType($v['tz_type']);
            $v['statu'] = userInvesStatu($v['statu']);
        }
        $this->exportExcel($title, $userInvestList, '静态投资');
    }

    /**
     * 导出会员冻结日志
     */
    public function exportUsersLockLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'lock_time' => '冻结时间',
            'log_info' => '冻结原因',
            'statu' => '状态',
        );
        $condition = $this->condition;
        I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
        $lockUserList = M('users_lock')->where($condition)->order('id desc')->select();
        $userIdArr = getArrColumn($lockUserList, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        foreach ($lockUserList as $k => &$v) {
            $v['key'] = $k + 1;
            $v['lock_time'] = date('Y-m-d H:i:s', $v['lock_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['statu'] = $v['statu'] == 9 ? date('Y-m-d H:i:s', $v['sj_time']) . '已释放' : '待释放 ';
        }
        $this->exportExcel($title, $lockUserList, '会员冻结日志');
    }

    /**
     * 导出奖金明细记录  2017 12 23 修改
     */
    public function exportBonusLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'user_id' => '会员账号',
            'bonus_id' => '奖金名称',
            'add_time' => '发放时间',
            'sj' => '结算方式',
            'money' => '应发金额',
            'come_user_id' => '来源于会员',
            'statu' => '状态',
            'note' => '备注',
        );
        $condition = $this->condition;
        I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
        $userBonusList = M('bonus_log')->where($condition)->order('id desc')->select();
        $userA = getArrColumn($userBonusList, 'user_id');
        $userB = getArrColumn($userBonusList, 'come_user_id');
        $userIdArr = array_filter(array_merge($userA, $userB));
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        foreach ($userBonusList as $k => &$v) {
            $v['key'] = $k + 1;
            $v['sj'] = bonusSj($v['sj']);
            $v['bonus_id'] = moneyLogType($v['bonus_id']);
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['user_id'] = $userList[$v['user_id']];
            $v['statu'] = $v['statu'] == 9 ? date('Y-m-d H:i:s', $v['sj_time']) . '已结算 ' : '待结算 ';
            $v['come_user_id'] = $userList[$v['come_user_id']];
        }
        $this->exportExcel($title, $userBonusList, '会员奖金记录');
    }

    /**
     * 导出 转账 日志  2017 12 23 
     */
    public function exportChangeLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'zf_time' => '转账时间',
            'uid' => '转出会员',
            'money' => '转出金额',
            'money_id' => '钱包',
            'to_uid' => '转入会员',
            'to_money' => '到账金额',
            'fee' => '手续费',
            'note' => '备注',
        );
        $condition = $this->condition;
        I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
        $userChangeList = M('money_change_log')->where($condition)->order('id desc')->select();
        $userA = getArrColumn($userChangeList, 'uid');
        $userB = getArrColumn($userChangeList, 'to_uid');
        $userIdArr = array_filter(array_merge($userA, $userB));
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $moneyInfo = M('money')->cache('moneyInfo')->getField('money_id,name_cn');
        foreach ($userChangeList as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['money'] = $v['money'];
            $v['money_id'] = $moneyInfo[$v['money_id']] . '=>' . $moneyInfo[$v['type_id']];
            $v['fee'] = $v['fee'] . '%' . '(' . $v['fee_money'] . ')';
            $v['to_money'] = $v['to_money'];
            $v['note'] = $v['note'];
            $v['to_uid'] = $userList[$v['to_uid']];
        }
        $this->exportExcel($title, $userChangeList, '会员转账记录');
    }

    /**
     * 导出 转换 日志  2017 12 23 
     */
    public function exportTransformLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'zf_time' => '转换时间',
            'uid' => '转出会员',
            'money' => '转出金额',
            'money_id' => '钱包',
            'to_money' => '到账金额',
            'fee' => '手续费',
            'note' => '备注',
        );
        $condition = $this->condition;
        I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
        $userTransformList = M('money_transform_log')->where($condition)->order('id desc')->select();
        $userIdArr = getArrColumn($userTransformList, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $moneyInfo = M('money')->cache('moneyInfo')->getField('money_id,name_cn');
        foreach ($userTransformList as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['money'] = $v['money'];
            $v['money_id'] = $moneyInfo[$v['money_id']] . '=>' . $moneyInfo[$v['type_id']];
            $v['fee'] = $v['fee'] . '%' . '(' . $v['fee_money'] . ')';
            $v['to_money'] = $v['to_money'];
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $userTransformList, '会员转换记录');
    }

    /**
     * 导出会员钱包变动日志
     */
    public function exportMoneyLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'mid' => '钱包',
            'zf_time' => '变动时间',
            'money' => '变动金额',
            'is_type' => '变动类型',
            'total' => '变动后余额',
            'come_uid' => '相关会员',
            'note' => '说明'
        );

        $userMoneyLog = M('money_log')->where($this->condition)->order("id desc")->select();
        $userA = getArrColumn($userMoneyLog, 'uid');
        $userB = getArrColumn($userMoneyLog, 'come_uid');
        $userIdArr = array_filter(array_merge($userA, $userB));
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $moneyInfo = M('money')->cache('moneyInfo')->getField('money_id,name_cn');
        foreach ($userMoneyLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['money'] = $v['money'];
            $v['total'] = $v['total'];
            $v['mid'] = $moneyInfo[$v['mid']];
            $v['come_uid'] = $userList[$v['come_uid']];
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $userMoneyLog, '会员钱包变动明细');
    }

    /**
     * 导出日收入明细
     */
    public function exportUserDay() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'zf_time' => '日期',
            'uid' => '会员账号',
        );
        $bonusList = M('bonus')->where("statu=1")->cache('bonusList')->select();
        foreach ($bonusList as $v) {
            $title['bonus_' . $v['bonus_id']] = $v['name_cn'];
        }
        $title['total'] = '应发';
        $bonusTaxList = M('bonus_tax')->where("statu=1")->cache('bonusTaxList')->select();
        foreach ($bonusTaxList as $v) {
            $title['tax_' . $v['tax_id']] = $v['name_cn'];
        }
        $title['money'] = '实发';
        $moneyList = M('money')->where("statu=1")->cache('moneyList')->select();
        foreach ($moneyList as $v) {
            $title['out_' . $v['money_id']] = $v['name_cn'];
        }
        $userDayList = M('users_day')->where($this->condition)->order('id desc')->select();
        foreach ($userDayList as $k => &$v) {
            $user = M('users')->where("user_id = {$v['uid']}")->field('account')->find();
            $v['key'] = $k + 1;
            $v['uid'] = $user['account'];
            $v['zf_time'] = date("Y-m-d", $v['zf_time']);
        }
        $this->exportExcel($title, $userDayList, '日收入明细');
    }

    /**
     * 导出平台日收入统计excel
     */
    public function exportBochuDay() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'zf_time' => '年-月-日',
        );
        $bonusList = M('bonus')->where("statu=1")->cache('bonusList')->select();
        foreach ($bonusList as $v) {
            $title['bonus_' . $v['bonus_id']] = $v['name_cn'];
        }
        $bonusTaxList = M('bonus_tax')->where("statu=1")->cache('bonusTaxList')->select();
        foreach ($bonusTaxList as $v) {
            $title['tax_' . $v['tax_id']] = $v['name_cn'];
        }
        $moneyList = M('money')->where("statu=1")->cache('moneyList')->select();
        foreach ($moneyList as $v) {
            $title['money_' . $v['money_id']] = $v['name_cn'];
        }
        $levelList = M('level')->where("statu=1")->cache('levelList')->select();
        foreach ($levelList as $v) {
            $title['level_' . $v['level_id']] = $v['name_cn'];
        }
        $bochuDayList = M('bochu_day')->where($this->condition)->order('id desc')->select();
        foreach ($bochuDayList as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d', $v['zf_time']);
//            $v['kz'] = clear_bochu(1, $v['id']) . '%';
        }
        $this->exportExcel($title, $bochuDayList, '公司日收支统计');
    }

    /**
     * 导出会员在线充值日志
     */
    public function exportPayLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'mid' => '钱包',
            'add_time' => '充值时间',
            'pay_time' => '支付时间',
            'add' => '充值金额',
            'per' => '汇率',
            'money' => '实际到账',
            'pay_code' => '充值方式',
            'statu' => '状态',
            'note' => '说明'
        );
        $userPayLog = M('pay_recharge')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($userPayLog, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $moneyInfo = M('money')->cache('moneyInfo')->getField('money_id,name_cn');
        foreach ($userPayLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['mid'] = $moneyInfo[$v['mid']];
            $v['statu'] = payStatu($v['statu']);
            $v['pay_code'] = $v['pay_code'] . '/' . $v['pay_name'];
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $userPayLog, '会员在线充值明细');
    }

    /**
     * 导出会员等级变动的日志
     */
    public function exportLelvllog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'zf_time' => '申请时间',
            'y_id' => '申请前级别',
            'x_id' => '申请后级别',
            'statu' => '当前状态',
            'note' => '说明'
        );
        $levelLogLog = M('level_log')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($leaderLogLog, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $levelInfo = M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn');
        foreach ($levelLogLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['y_id'] = $levelInfo[$v['y_id']];
            $v['x_id'] = $levelInfo[$v['x_id']];
            $v['statu'] = upgradeStatu($v['statu']);
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $levelLogLog, '会员等级申请日志');
    }

    /**
     * 导出申请领导的日志
     */
    public function exportLeaderLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'zf_time' => '申请时间',
            'y_id' => '申请前级别',
            'x_id' => '申请后级别',
            'statu' => '当前状态',
            'note' => '说明'
        );
        $leaderLogLog = M('leader_log')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($leaderLogLog, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $leaderInfo = M('leader')->where("statu=1")->cache('leaderInfo')->getField('id,name_cn');
        foreach ($leaderLogLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['y_id'] = $leaderInfo[$v['y_id']];
            $v['x_id'] = $leaderInfo[$v['x_id']];
            $v['statu'] = upgradeStatu($v['statu']);
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $leaderLogLog, '领导申请日志');
    }

    /**
     * 导出申请报单中心的日志
     */
    public function exportAgentLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'zf_time' => '申请时间',
            'y_id' => '申请前级别',
            'x_id' => '申请后级别',
            'statu' => '当前状态',
            'note' => '说明'
        );
        $agentLogLog = M('agent_log')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($agentLogLog, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $agenInfo = M('agent')->where("statu=1")->cache('agentInfo')->getField('id,name_cn');
        foreach ($agentLogLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['y_id'] = $agenInfo[$v['y_id']];
            $v['x_id'] = $agenInfo[$v['x_id']];
            $v['statu'] = upgradeStatu($v['statu']);
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $agentLogLog, '报单中心申请日志');
    }

    /**
     * 导出代理领导的日志
     */
    public function exportServiceLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'zf_time' => '申请时间',
            'y_id' => '申请前级别',
            'x_id' => '申请后级别',
            'statu' => '当前状态',
            'note' => '说明'
        );
        $serviceLogLog = M('service_log')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($serviceLogLog, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        $serviceInfo = M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn');
        foreach ($serviceLogLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['y_id'] = $serviceInfo[$v['y_id']];
            $v['x_id'] = $serviceInfo[$v['x_id']];
            $v['statu'] = upgradeStatu($v['statu']);
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $serviceLogLog, '代理申请日志');
    }

    /**
     * 导出会员动态日志
     */
    public function exportUserActionLog() {
        $title = array(
            'key' => '序号',
            'id' => 'ID',
            'uid' => '会员账号',
            'zf_time' => '变动时间',
            'log_ip' => '登录ip',
            'equipment' => '操作设备',
            'note' => '说明'
        );
        $actionLogLog = M('users_action')->where($this->condition)->order("id desc")->select();
        $userIdArr = getArrColumn($actionLogLog, 'uid');
        $userIdArr && $userList = M("users")->where("user_id in(" . implode(',', $userIdArr) . ")")->getField('user_id, account');
        foreach ($actionLogLog as $k => &$v) {
            $v['key'] = $k + 1;
            $v['zf_time'] = date('Y-m-d H:i:s', $v['zf_time']);
            $v['uid'] = $userList[$v['uid']];
            $v['note'] = $v['note'];
        }
        $this->exportExcel($title, $actionLogLog, '会员动态日志');
    }

    /**
     * 把数据整成table并导出
     * @param $tableTitle 标题
     * @param $tableData 内容
     * @param $fileName 文件名
     */
    public function exportExcel($tableTitle, $tableData, $fileName, $height = '25px') {
        $strTable = '<table border="1" style="border-spacing: 0;border-collapse: collapse;">';
        $strTable .= '<tr style="text-align:center;font-size:15px;height:28px;line-height:28px;">';
        foreach ($tableTitle as $k => $v) {
            $strTable .= '<th>' . $v . '</th>';
        }
        $strTable .= '</tr>';
        foreach ($tableData as &$val) {
            $strTable .= '<tr style="text-align:center;font-size:14px;height:' . $height . ';line-height:' . $height . ';">';
            foreach ($tableTitle as $k2 => $v2) {
                $strTable .= '<td>' . $val[$k2] . '</td>';
            }
            $strTable .= '</tr>';
        }
        $strTable .='</table>';
        $this->downloadExcel($strTable, $fileName);
        exit();
    }

    /**
     * 导出excel
     * @param $strTable    表格内容
     * @param $filename 文件名
     */
    public function downloadExcel($strTable, $filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=" . $filename . "_" . date('Y-m-d') . ".xls");
        header('Expires:0');
        header('Pragma:public');
        echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $strTable . '</html>';
    }

}
