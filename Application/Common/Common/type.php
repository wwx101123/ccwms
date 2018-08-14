<?php

# ===========================
/**
 * 管理员操作记录
 * @param type $log_info  操作类型
 * @param type $isType  1  代表登录 2  激活
 * @param type $note  备注
 */

function adminLogAdd($log_info, $isType = '') {
    $add['log_time'] = time();
    $isType && $add['is_type'] = $isType;
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    $add['log_ip'] = getIP();
    $add['log_url'] = __ACTION__;
    $add['equipment'] = equipmentSystem();
    $res = M('admin_log')->add($add);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

/**
 * 会员状态 变动日志
 * @param type $uid
 * @param type $note 变动原因
 * @param type $isType  1  代表登录
 */
function userAction($uid, $note, $isType = '') {
    $data['uid'] = $uid;
    $isType && $data['is_type'] = $isType;
    $data['note'] = $note;
    $data['zf_time'] = time();
    $data['log_ip'] = getIP();
    $data['log_url'] = __ACTION__;
    $data['equipment'] = equipmentSystem();
    $res = M('users_action')->add($data);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

/**
 * 添加会员冻结日志
 * @param type $uid
 * @param type $note  冻结原因
 * @param type $isType  1  代表登录
 */
function userLockLog($uid, $note, $adminId = '') {
    $data['uid'] = $uid;
    $data['log_info'] = $note;
    $data['statu'] = 2;
    $data['lock_time'] = time();
    $adminId && $data['admin_id'] = $adminId;
    $data['log_ip'] = getIP();
    $data['log_url'] = __ACTION__;
    $data['equipment'] = equipmentSystem();
    $res = M('users_lock')->add($data);
    if ($res) {
        return M("users")->where(array('user_id' => $uid))->save(array('frozen' => 2));
    } else {
        return false;
    }
}

/**
 * 管理员操作记录
 * @param type $log_info  操作类型
 * @param type $log_info  商家 id
 * @param type $isType  1  代表登录 2  激活
 * @param type $note  备注
 */
function sellerLogAdd($log_info, $seller, $isType = '') {
    $add['log_time'] = time();
    $isType && $add['is_type'] = $isType;
    $add['seller_id'] = $seller;
    $add['log_info'] = $log_info;
    $add['log_ip'] = getIP();
    $add['log_url'] = __ACTION__;
    $add['equipment'] = equipmentSystem();
    $res = M('seller_log')->add($add);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

# ===========================

/**
 * 密码修改日志
 * @param type $user_id
 * @param type $name
 * @param type $is_type
 */
function userpPasswordLog($user_id, $name, $is_type) {
    $data['zf_time'] = time();
    $data['user_id'] = $user_id;
    $data['name'] = $name;
    $data['is_type'] = $is_type;
    $res = M('password_log')->add($data);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

function upgradeStatu($id) {
    $data = array();
    $data[1] = "己审核";
    $data[2] = "待确认";
    $data[3] = "己拒绝";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 会员级别变动日志
 * @param type $user_id
 * @param type $desc
 * @param type $note
 * @return boolean
 */
function userLevel($uId, $yid, $xid, $statu = 2, $note = '') {
    $data['uid'] = $uId;
    $data['y_id'] = $yid;
    $data['x_id'] = $xid;
    $data['statu'] = $statu;
    $data['note'] = $note;
    $data['zf_time'] = time();
    $note && $data['note'] = $note;
    $res = M('level_log')->add($data);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

/**
 * 报单等级变动日志
 * @param type $user_id
 * @param type $desc
 * @param type $note
 * @return boolean
 */
function userAgent($uId, $yid, $xid, $statu = 2, $note = '') {
    $data['uid'] = $uId;
    $data['y_id'] = $yid;
    $data['x_id'] = $xid;
    $data['statu'] = $statu;
    $data['note'] = $note;
    $data['zf_time'] = time();
    $note && $data['note'] = $note;
    $res = M('agent_log')->add($data);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

/**
 * 领导等级变动日志
 * @param type $user_id
 * @param type $desc
 * @param type $note
 * @return boolean
 */
function userLeader($uId, $yid, $xid, $statu = 2, $note = '') {
    $data['uid'] = $uId;
    $data['y_id'] = $yid;
    $data['x_id'] = $xid;
    $data['statu'] = $statu;
    $data['zf_time'] = time();
    $note && $data['note'] = $note;
    $res = M('leader_log')->add($data);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

/**
 * 代理等级变动日志
 * @param type $user_id
 * @param type $desc
 * @param type $note
 * @return boolean
 */
function userService($uId, $yid, $xid, $statu = 2, $note = '') {
    $data['uid'] = $uId;
    $data['y_id'] = $yid;
    $data['x_id'] = $xid;
    $data['statu'] = $statu;
    $data['zf_time'] = time();
    $note && $data['note'] = $note;
    $res = M('service_log')->add($data);
    if ($res) {
        return $res;
    } else {
        return false;
    }
}

function moneyLogType($id) {
    $data = array();
    $data = M('bonus')->where("statu=1")->cache('bonus')->getField('bonus_id,name_cn');
    $data[8] = '储存释放';
    $data[9] = '储存YML转出';
    $data[10] = '积分转出';
    $data[50] = '币释放';
   // $data[51] = '扫码付矿金';
   // $data[52] = '扫码付币';
    $data[53] = '转出';
    $data[54] = '存宝';
   // $data[55] = '众筹';
    $data[96] = "管理调整";
    $data[97] = "管理增加";
    $data[98] = "冻结释放";
    $data[99] = "冻结钱包";
    $data[100] = "管理扣减";
//    $data[101] = "在线充值";
//    $data[102] = "开通会员";
  //  $data[103] = "汇款充值";
 //   $data[104] = "申请提现";
    $data[105] = "钱包互转";
    $data[106] = "订单支付";
 //   $data[107] = "激活赠送";
    $data[108] = "积分兑换";
//    $data[109] = "静态投资";
    $data[110] = "交易";
//    $data[111] = "取消订单";
//    $data[112] = "排单扣除";
//    $data[113] = "排单提现";
    $data[114] = '管理费';
  //  $data[115] = '矿机';
   // $data[116] = '挖矿收益';
  //  $data[117] = '缴纳保证金';
  //  $data[118] = '转出方回赠';
  //  $data[119] = '静态收益';
 //   $data[120] = '扣除算力';
    $data[127] = '签到领奖';
  //  $data[129] = '交易';
 //   $data[155] = '赠送mil币';
 //   $data[156] = '存宝日息';
    $data[157] = '买入币';
  	    $data[158] = '领取';

//    $data[114] = "抽奖";
    // $data[115] = "股票交易";
    // $data[116] = "交易手续费";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function moneyLogType_en($id) {
    $data = array();
    $data = M('bonus')->where("statu=1")->cache('bonus')->getField('bonus_id,name_en');
    $data[50] = 'Currency release';
    $data[51] = 'Sweep gold';
    $data[52] = 'Sweep the code to pay money';
    $data[53] = 'roll out';
    $data[54] = 'Save the treasure';
    $data[55] = 'crowd funding';
    $data[96] = "Management to adjust";
    $data[97] = "Management to increase";
    $data[98] = "Freeze release";
    $data[99] = "Freeze the purse";
    $data[100] = "Manage deductions";
//    $data[101] = "在线充值";
//    $data[102] = "开通会员";
    $data[103] = "Remittance top-up";
    $data[104] = "request withdrawal";
    $data[105] = "The wallet transfers";
    $data[106] = "payment of an order";
    $data[107] = "Activate the present";
    $data[108] = "The wallet for";
//    $data[109] = "静态投资";
    $data[110] = "deal";
//    $data[111] = "取消订单";
//    $data[112] = "排单扣除";
//    $data[113] = "排单提现";
    $data[114] = 'administrative fee';
    $data[115] = 'mill';
    $data[116] = 'Mining earnings';
    $data[117] = 'deposit';
    $data[118] = 'Return the money to the sender';
    $data[119] = 'The static gains';
    $data[120] = 'Deducted to calculate force';
    $data[127] = 'Sign in to accept';
    $data[129] = 'deal';
    $data[155] = ' Presentedmilmoney ';
    $data[156] = 'Save treasure daily interest';
    $data[157] = 'To buy currency';

//    $data[114] = "抽奖";
    // $data[115] = "股票交易";
    // $data[116] = "交易手续费";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function moneyCarryTk($id) {
    $data = array();
    $data[1] = "日";
    $data[2] = "周";
    $data[3] = "月";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function moneyCarryLogStatu($id) {
    $data = array();
    $data[1] = "待审核";
    $data[2] = "己审核";
    $data[3] = "己拒绝";
    $data[9] = "己完成";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 奖项管理
 * @param type $monye_id  奖项 id
 * @param type $a  1 查询奖项名字  2  查询当条记录 3 查询是否启用
 * @return type
 */
function bonusList($bonus_id, $a) {
    $info = M('bonus')->where(array('bonus_id' => $bonus_id))->cache(true)->find();
    if ($a == 1) {
        return $info['name_cn'];
    }
    if ($a == 2) {
        return $info;
    }
    if ($a == 3) {
        return $info['is_type'];
    }
    if ($a == 4) {
        return $info['is_sj'];
    }
}

function bonusPer($id) {
    $data = array();
    $data[1] = "按比例";
    $data[2] = "按金额";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function bonusSj($id) {
    $data = array();
    $data[1] = "秒结";
    $data[2] = "日结";
    $data[3] = "周结";
    $data[4] = "月结";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 留言状态
 * @return string
 */
function consultType($id) {
    $data = array();
    $data[1] = "己回复";
    $data[2] = "待受理";

    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 *  提现状态
 * @return string
 */
function withdrawalsType($id) {
    $data = array();
    $data[1] = "己付款";
    $data[2] = "待审核";
    $data[3] = "己审核";
    $data[4] = "己拒绝";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 *  汇款充值状态
 * @return string
 */
function moneyAddType($id) {
    $data = array();
    $data[1] = "审核成功";
    $data[2] = "待审核";
    $data[3] = "己拒绝";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 *  静态投资装态
 * @return string
 */
function userInvesTzType($id) {
    $data = array();
    $data[1] = "首次投资";
    $data[9] = "重复投资";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 *  静态投资分红状态
 * @return string
 */
function userInvesStatu($id) {
    $data = array();
    $data[1] = "分红中";
    $data[2] = "暂停中";
    $data[9] = "己出局";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 会员密保问题
 * @param int|string $id
 * @return string|array
 */
function userSecurityList($id = '') {
    $data = array();
    $list = explode('｜', zfCache('securityInfo.security'));
    foreach ($list as $k => $v) {
        $data[$k + 1] = $v;
    }
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 单页属性分类
 * @return string
 */
function aboutType() {
    $data = array();
    $data[1] = "会员注册协议";
//    $data[2] = "售后服务保障";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function moneyConfigType($id) {
    $data = array();
    $data[1] = "即时转让";
//    $data[2] = "担保转让";
    $data[3] = "钱包转换";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function moneyConfigFeeType($id) {
    $data = array();
    $data[1] = "扣转出方";
    $data[2] = "扣转入方";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 激活状态
 * @param type $id
 * @return string
 */
function activateType($id) {
    $data = array();
    $data[1] = "己激活";
    $data[2] = "未激活";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 性别状态
 * @param type $id
 * @return string
 */
function sexType($id) {
    $data = array();
    $data[1] = "男";
    $data[2] = "女";
    $data[3] = "保密";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 奖金结算状态
 * @param type $id1 待结算 2 结算中 9 己结算
 * @return string
 */
function bonusLogStatu($id) {
    $data = array();
    $data[1] = "待结算";
    $data[2] = "结算中";
    $data[9] = "己结算";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 语言
 * @param type $id1 待结算 2 结算中 9 己结算
 * @return string
 */
function languageType($id) {
    $data = array();
    $data[1] = "中文";
    $data[2] = "英语";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 国家管理
 * @param type $monye_id  奖项 id
 */
function countryList($a) {
    $data = array();
    $data = M('country')->cache('country')->order('sort desc')->getField('id,name_cn');
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 在线充值状态
 */
function payStatu($id) {
    $data = array();
    $data[1] = "己支付";
    $data[2] = "己关闭";
    $data[3] = "待支付";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 汇款充值状态
 */
function addMoneyType($id) {
    $data = array();
    $data[1] = "己审核";
    $data[2] = "待审核";
    $data[3] = "己拒绝";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 订单状态
 * @param int|string $id
 * @return array|string
 */
function orderStatu($id = '') {
    $data = array();
    $data[1] = "待支付";
    $data[2] = "待发货";
    $data[3] = "待收货";
    $data[4] = "已完成";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 冻结状态
 * @param type $id 1 己释放 2 冻结中
 * @return string
 */
function lockStatu($id) {
    $data = array();
    $data[1] = "己释放";
    $data[2] = "冻结中";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 留言分类
 * @param int|string $id
 * @return string|array
 */
function messageType($id = '') {
    $data = array();
    $list = explode('｜', zfCache('webInfo.msctitle'));
    foreach ($list as $k => $v) {
        $data[$k + 1] = $v;
    }
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 留言状态
 * @param type $id 1 己释放 2 冻结中
 * @return string
 */
function messageStatu($id) {
    $data = array();
    $data[1] = "待回复";
    $data[2] = "已回复";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 夺宝订单中奖状态
 * @param type $id 1 己释放 2 冻结中
 * @return string
 */
function winsOrderwWinning($id) {
    $data = array();
    $data[1] = "己中奖";
    $data[2] = "未中奖";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 夺宝订单中奖状态
 * @param type $id 1 己释放 2 冻结中
 * @return string
 */
function winsOrderwStatu($id) {
    $data = array();
    $data[1] = "己开奖";
    $data[2] = "未开奖";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 矿机运行状态
 * @param type $id 状态 1运行中 2 己暂停 3已结束
 * @return string
 */
function millsStatu($id) {
    $data = array();
    $data[1] = "未使用";
    $data[2] = "正在运行";
    $data[3] = "已经结束";
    $data[4] = "复投";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 统一交易状态
 * @param type $id
 * @return string
 */
function tradeStatus($id) {
    $data = array();
    $data[1] = "待交易";
    $data[2] = "交易中";
    $data[3] = "己撤销";
    $data[9] = "己完成";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 配对打款状态
 * @param type $id   
 * @return string
 */
function pdIsType($id) {
    $data = array();
    $data[1] = "等待打款";
    $data[2] = "等待收款";
    $data[3] = "超时打款";
    $data[4] = "超时收款";
//    $data[5] = "假款投诉";
    $data[6] = "交易己完成";
    $data[7] = "交易己取消";
//    $data[8] = "提前出局";
//    $data[9] = "成功出局";
    $data[10] = "等待担保人收款";   //卖家打款给担保人
    $data[11] = "等待卖家确认交易";  //担保人确认收款
    $data[12] = "担保人待打款"; //卖家己确认交易
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 投诉处理状态
 * @param type $id   
 * @return string
 */
function tsIsType($id) {
    $data = array();
    $data[1] = "待处理";
    $data[2] = "己处理";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}
