<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class InvesLogic extends RelationModel {

    protected $tableName = 'users_invest';

    public function investInfo($post) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
        if ($post['edit_note'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        $note = ''; // 备注说明
        $info = M('users_invest')->where(array('id' => $post['id']))->find();
        $note = '静态投资ID' . $post['id'] . $post['edit_note'];
        if ($post['edit_num_total'] != $info['num_total']) {
            $note .= '总分红天数由' . $info['num_total'] . '修改为' . $post['edit_num_total'];
            $data['num_total'] = $post['edit_num_total'];
        }
        if ($post['edit_money_total'] != $info['money_total']) {
            $note .= '总分红金额由' . $info['money_total'] . '修改为' . $post['edit_money_total'];
            $data['money_total'] = $post['edit_money_total'];
        }
        if ($post['edit_fh_num'] != $info['fh_num']) {
            $note .='己分红天数由' . $info['fh_num'] . '修改为' . $post['edit_fh_num'];
            $data['fh_num'] = $post['edit_fh_num'];
        }
        if ($post['edit_total_money'] != $info['total_money']) {
            $note .='己分红金额由' . $info['total_money'] . '修改为' . $post['edit_total_money'];
            $data['total_money'] = $post['edit_total_money'];
        }
        $res = M('users_invest')->where(array('id' => $post['id']))->save($data);
        adminLogAdd($note);
        if ($res) {
            return array('status' => 1, 'msg' => '操作成功');
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

    /**
     * 会员加单
     * @param array  $post     会员提交的信息 [num:加单数量,money:金额,secpwd:二级密码]
     * @param array  $userInfo 会员信息 [user_id:会员id,secpwd:会员二级密码,level:会员等级,tjr_id:推荐人id,jhr_id:激活人id,jh_type:激活类型]
     * @return array           返回操作信息 [status:-1为失败、1为成功,msg:提示信息]
     */
    public function addOrder($post, $userInfo)
    {
        if(empty($userInfo)) {
            return ['status' => -1, 'msg' => '请先登陆'];
        }

        if($post['secpwd'] == '') {
            return ['status' => -1, 'msg' => '请输入二级密码'];
        }
        if(webEncrypt($post['secpwd']) != $userInfo['secpwd']) {
            return ['status' => -1, 'msg' => '二级密码验证失败'];
        }

        # 会员本次加单量
        $num = intval($post['num']);
        if($num <= 0) {
            return ['status' => -1, 'msg' => '请输入加单数量'];
        }
        if($num > $this->determineAddNum($userInfo)) {
            return ['status' => -1, 'msg' => '本次最多允许加单'.($this->determineAddNum($userInfo))];
        }
        # 每单价格
        $price = floatval($post['money']);
        if($price <= 0) {
            return ['status' => -1, 'msg' => '请输入金额'];
        }

        # 总金额
        $moneyTotal = $price * $num;

        $mid = 2;
        if(usersMoney($userInfo['user_id'], $mid) < $moneyTotal) {
            return ['status' => -1, 'msg' => moneyList($mid).'余额不足'];
        }

        $res = userMoneyLogAdd($userInfo['user_id'], $mid, '-'.$moneyTotal, 124, '加单', 0, $userInfo['user_id']);
        if($res) {


            # 计算动态收益
            bonus2Clear($userInfo['tjr_id'], $userInfo['user_id'], $moneyTotal, $userInfo['account'] . '加单'); // 推荐奖
            $branchInfo = M('users_branch')->where(['uid' => $userInfo['user_id']])->field('branch_id,jdr_id')->find();
            # 统计业绩
            branchYjCount($branchInfo['branch_id'], $userInfo['user_id'], $moneyTotal, $userInfo['account'].'加单');

            
            # 计算级差奖
            bonus3Clear($branchInfo['jdr_id'], $userInfo['user_id'], $moneyTotal, $userInfo['account'].'加单');

            # 计算服务中心奖
            if($userInfo['jh_type'] == 6) {
                bonus4Clear($userInfo['bdr_id'], $userInfo['user_id'], $moneyTotal, $userInfo['account'] . '加单', $userInfo['jhr_id']);
            } else {
                bonus4Clear($userInfo['bdr_id'], $userInfo['user_id'], $moneyTotal, $userInfo['account'] . '加单');
            }

            $levelInfo = M('level')->where(['level_id' => $userInfo['level']])->field('b_1, b_1_total,amount')->find();
//            $this->addInvestLog($userInfo['user_id'], $num, $price, $levelInfo['b_1'], $levelInfo['amount'] * $levelInfo['b_1_total'], '加单', 1, 2);

            # 静态分红处理 s
            $investWhere = [
                'uid' => $userInfo['user_id']
                ,'statu' => ['neq', 9]
            ];
            $investInfo = $this->where($investWhere)->find();
//            $maxMoney = $levelInfo['b_1_total']-$userLevelInfo['b_1_total'];
            $maxMoney = (($investInfo['money_total']-$investInfo['total_money'])+$moneyTotal) * $levelInfo['b_1_total'];
            $per = $levelInfo['b_1'];
            # 判断注册那单是否已经分红完成 如果完成新增一条记录 否则就修改原记录
            if($investInfo) {
                $investSaveData = [
                    'num' => 1
                    ,'price' => $investInfo['price']+$moneyTotal
                    ,'price_total' => $investInfo['price_total']+$moneyTotal
                    ,'money_total' => $maxMoney
                ];
                $this->where(['id' => $investInfo['id']])->save($investSaveData);

            } else {
                $this->addInvestLog($userInfo['user_id'], 1, $moneyTotal, $per, $maxMoney, '加单', 1, 2);
            }

            return ['status' => 1, 'msg' => '加单成功'];
        } else {
            return ['status' => -1, 'msg' => '加单失败'];
        }
    }

    /**
     * 会员复投
     * @param array  $post     会员提交的信息 [levle_id:等级id,secpwd:二级密码]
     * @param array  $userInfo 会员信息 [user_id:会员id,secpwd:会员二级密码,level:会员等级,tjr_id:推荐人id,jhr_id:激活人id,jh_type:激活类型]
     * @return array           返回操作信息 [status:-1为失败、1为成功,msg:提示信息]
     */
    public function recast($post, $userInfo)
    {
        if(empty($userInfo)) {
            return ['status' => -1, 'msg' => '请先登陆'];
        }

        if($post['secpwd'] == '') {
            return ['status' => -1, 'msg' => '请输入二级密码'];
        }
        if(webEncrypt($post['secpwd']) != $userInfo['secpwd']) {
            return ['status' => -1, 'msg' => '二级密码验证失败'];
        }

        $levelId = intval($post['level_id']);
        if($levelId <= 0) {
            return ['status' => -1, 'msg' => '请选择复投金额'];
        }

        $levelInfo = M('level')->where(['level_id' => $levelId])->find();
        if($levelId < $userInfo['level']) {
            return ['status' => -1, 'msg' => '复投金额必须大于注册金额'];
        }
        $money = $levelInfo['amount'];


        $mid = (int)zfCache('regInfo.ma_id');
        $jsMoney = $money * (float)zfCache('regInfo.ma_per') / 100;

        if(usersMoney($userInfo['user_id'], $mid) < $jsMoney) {
            return ['status' => -1, 'msg' => moneyList($mid).'余额不足'];
        }
        if($levelInfo['jhm_num'] > 0) {
            $jrmNum = $levelInfo['jhm_num'];
            if($jrmNum > 0) {
                if(usersMoney($userInfo['user_id'], 3) < $jrmNum) {
                    return ['status' => -1, 'msg' => moneyList(3).'余额不足'];
                }
            }
        }
        if($jsMoney > 0) {
            $res = userMoneyLogAdd($userInfo['user_id'], $mid, '-'.$jsMoney, 118, '会员激活', 0, $userInfo['user_id']);
        }
        if($jrmNum > 0) {
            $res = userMoneyLogAdd($userInfo['user_id'], 3, '-' . $jrmNum, 118, '会员激活', 0, $userInfo['user_id']);
        }
        if($res) {
            $userUpData = [
                'is_cj' => 2
                ,'ft_time' => time()
            ];
            M('users')->where(['user_id' => $userInfo['user_id']])->save($userUpData);
            $this->addInvestLog($userInfo['user_id'], $levelInfo['b_1'],$levelInfo['amount'], $levelInfo['amount'], $levelInfo['b_1_day'], '复投', 2);
            # 添加分红记录 e

            # 计算销售红利
            bonus2Clear($userInfo['tjr_id'], $userInfo['user_id'], $money, $userInfo['account'] . '激活'); // 推荐奖
            # 计算管理红利
            bonus3Clear($userInfo['tjr_id'], $userInfo['user_id'], $money, $userInfo['account'] . '激活'); // 推荐奖
            return ['status' => 1, 'msg' => '复投成功'];
        } else {
            return ['status' => -1, 'msg' => '复投失败'];
        }
    }

    /**
     * 会员投资
     * @param array  $post     会员提交的信息 [levle_id:等级id,secpwd:二级密码]
     * @param array  $userInfo 会员信息 [user_id:会员id,secpwd:会员二级密码,level:会员等级,tjr_id:推荐人id,jhr_id:激活人id,jh_type:激活类型]
     * @return array           返回操作信息 [status:-1为失败、1为成功,msg:提示信息]
     */
    public function investment($post, $userInfo)
    {
        if(empty($userInfo)) {
            return ['status' => -1, 'msg' => '请先登陆'];
        }

        if($post['secpwd'] == '') {
            return ['status' => -1, 'msg' => '请输入二级密码'];
        }
        if(webEncrypt($post['secpwd']) != $userInfo['secpwd']) {
            return ['status' => -1, 'msg' => '二级密码验证失败'];
        }

        $levelId = intval($post['level_id']);
        if($levelId <= 0) {
            return ['status' => -1, 'msg' => '请选择投资金额'];
        }

        $levelInfo = M('level')->where(['level_id' => $levelId])->find();
        if($levelId < $userInfo['level']) {
            return ['status' => -1, 'msg' => '投资金额必须大于注册金额'];
        }
        $money = $levelInfo['amount'];


        $mid = (int)zfCache('regInfo.ma_id');
        $jsMoney = $money * (float)zfCache('regInfo.ma_per') / 100;

        if(usersMoney($userInfo['user_id'], $mid) < $jsMoney) {
            return ['status' => -1, 'msg' => moneyList($mid).'余额不足'];
        }
        if($jsMoney > 0) {
            $res = userMoneyLogAdd($userInfo['user_id'], $mid, '-'.$jsMoney, 126, '投资', 0, $userInfo['user_id']);
        }
        if($res) {
            $userUpData = [
                'is_tz' => 2
                ,'tz_time' => time()
            ];
            M('users')->where(['user_id' => $userInfo['user_id']])->save($userUpData);
            $this->addInvestLog($userInfo['user_id'], $levelInfo['b_1'],$levelInfo['amount'], $levelInfo['amount'], $levelInfo['b_1_day'], '投资');
            # 添加分红记录 e

            # 计算销售红利
            bonus2Clear($userInfo['tjr_id'], $userInfo['user_id'], $money, $userInfo['account'] . '激活'); // 推荐奖
            # 计算管理红利
            bonus3Clear($userInfo['tjr_id'], $userInfo['user_id'], $money, $userInfo['account'] . '激活'); // 推荐奖
            return ['status' => 1, 'msg' => '投资成功'];
        } else {
            return ['status' => -1, 'msg' => '投资失败'];
        }
    }

    /**
     * 判断会员最多还能加多少单
     * @param  array $userInfo 会员信息
     * @return int             加单数量
     */
    public function determineAddNum($userInfo)
    {
        if($userInfo['level'] < 3) {
            return 0;
        }

        $where = [
            'uid' => $userInfo['user_id']
            ,'tz_type' => ['in', [1,2,4]]
        ];
        # 只统计加单和注册和升级
        $investNum = intval($this->where($where)->sum('num'));

        # 会员最多加单量
        $maxAddNum = intval(zfCache('securityInfo.user_add_order_num'));

        return ($investNum < $maxAddNum ? ($maxAddNum-$investNum) : 0);
    }

    /**
     * 添加静态分红记录
     * @param int    $userId     会员id
     * @param float  $per        释放比例
     * @param float  $investTotal  投资金额
     * @param float  $outTotal  封顶金额
     * @param float  $outNum   天数
     * @param string $note       备注说明
     * @param string $note       备注说明
     * @param int $tzType       投资类型 1注册激活 2复投激活
     * @return bool              添加结果
     */
    public function addInvestLog($userId, $per, $investTotal, $outTotal, $outNum, $note = '', $tzType = 1)
    {
        $data = [
            'uid' => $userId
            ,'per' => $per
            ,'invest_total' => $investTotal
            ,'out_total' => $outTotal
            ,'note' => $note
            ,'add_time' => time()
            ,'statu' => 1
            ,'out_num' => $outNum
            ,'tz_type' => $tzType
        ];

        return $this->add($data);
    }
    /**
     * 添加静态分红记录
     * @param int    $userId     会员id
     * @param float  $price      金额/单
     * @param string $note       备注说明
     * @return bool              添加结果
     */
    public function addInvestLog_bak($userId, $price, $periodsTime, $periods, $startTime = 0, $note = '')
    {

        $data = [
            'uid' => $userId
            ,'price' => $price
            ,'periods_time' => $periodsTime
            ,'periods' => $periods
            ,'start_time' => $startTime
            ,'note' => $note
            ,'add_time' => time()
            ,'statu' => 1
        ];

        return $this->add($data);
    }

}
