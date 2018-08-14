<?php

# ===========================
/**
 * 管理员操作记录
 * @param string $log_info  操作类型
 * @param int $isType  1  代表登录 2  激活
 * @return bool
 */

function adminLogAdd($log_info, $isType = 0) {
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
 * @param int $uid
 * @param string $note 变动原因
 * @param int $isType  1  代表登录
 * @return bool
 */
function userAction($uid, $note, $isType = 0) {
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
function userService($uId, $yid, $xid, $statu = 2, $note = '', $province = '', $city = '', $district = '') {
    $data['uid'] = $uId;
    $data['y_id'] = $yid;
    $data['x_id'] = $xid;
    $data['statu'] = $statu;
    $data['province'] = $province > 0 ? $province : FALSE;
    $data['city'] = $city > 0 ? $city : FALSE;
    $data['district'] = $district > 0 ? $district : FALSE;
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
    $data[96] = "管理调整";
    $data[97] = "管理增加";
    $data[98] = "冻结释放";
    $data[99] = "冻结钱包";
    $data[100] = "管理扣减";
    $data[101] = "在线充值";
    $data[102] = "开通会员";
    $data[103] = "汇款充值";
    $data[104] = "申请提现";
    $data[105] = "钱包互转";
   $data[106] = "订单支付";
//    $data[107] = "激活赠送";
//    $data[108] = "钱包互换";
//    $data[109] = "静态投资";
//    $data[110] = "担保交易";
//    $data[111] = "取消订单";
//    $data[112] = "排单扣除";
//    $data[113] = "排单提现";
//    $data[114] = '管理费';

//    $data[114] = "抽奖";
//    $data[115] = "NYC";
//    $data[116] = "交易手续费";
//    $data[117] = '出局赠送';
    $data[118] = '激活';
//    $data[119] = "买NYC撤回";
//    $data[120] = "价格变动调整";
//    $data[121] = "申请合伙人";
//    $data[122] = '奖励';
    $data[123] = '升级';
    $data[124] = '收款';
    $data[125] = '赠送';
    $data[126] = '投资';
//    $data[124] = '加单';
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
//    $data[2] = "暂停中";
    $data[9] = "已分红";
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
    $data[1] = "待释放";
    $data[2] = "释放中";
    $data[9] = "己释放";
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
 * 系统名词列表
 */
function nounsList($id)
{
    $data = [
        '1' => '活跃值'
        ,'2' => '乐享值'
        ,'3' => '活动值'
        ,'4' => '幸福值'
        ,'5' => '服务值'
    ];

    return ($id ? $data[$id] : $data);
}
