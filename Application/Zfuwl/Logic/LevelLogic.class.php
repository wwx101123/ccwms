<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class LevelLogic extends RelationModel {

    protected $tableName = 'level';

    /**
     * 添加会员
     * @param $post
     * @return array
     */
    public function addLevelConfig($post) {
        if ($post['name_cn'] == '') {
            return ['status' => -1, 'msg' => '等级名称不能为空'];
        }
        if ($post['amount'] == '') {
            return ['status' => -1, 'msg' => '报单金额不能为空'];
        }
        $data = [
            'name_cn' => $post['name_cn']
            ,'amount' => $post['amount']
            ,'b_1' => $post['b_1']
            ,'b_2' => $post['b_2']
            ,'color' => $post['color']
        ];
        if ($post['level_id'] > 0) {
            $levelId = $this->where(['level_id' => $post['level_id']])->save($data);
        } else {
            $levelId = $this->add($data);
        }
        if (!$levelId) {
            return ['status' => -1, 'msg' => '操作失败'];
        } else {
            return ['status' => 1, 'msg' => '操作成功'];
        }
    }

    public function editUserlevel($post) {
        if ($post['user_id'] <= 0) {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
        if ($post['note'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        $user = M('users')->where(array('user_id' => $post['user_id']))->field('user_id,level,account')->find();
        if ($post['level_id'] == $user['level']) {
            return array('status' => -1, 'msg' => '调整后级别不能与当前相同');
        } else {
            $model = new \Think\Model();
            $model->startTrans();
            $logId = userAction($user['user_id'], $post['note'] . session('admin_name') . '调整会员等级');
            $AgentId = userLevel($user['user_id'], $user['level'], $post['level_id'], 1, $post['note'] . session('admin_name') . '调整');
            $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('level' => $post['level_id']));
            if ($logId && $AgentId && $userId) {
                $levelInfo = M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn');
                adminLogAdd($post['note'] . '调整' . $user['account'] . $levelInfo[$user[level]] . '为' . $levelInfo[$post[level_id]]);
                $model->commit();
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                $model->rollback();
                return array('status' => -1, 'msg' => '操作失败');
            }
        }
    }

    public function refuseInfo($post) {
        if ($post['id'] > 0) {
            if ($post['name'] == '') {
                return array('status' => -1, 'msg' => '备注不能为空');
            }
            $logInfo = M('level_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('level_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 3, 'refuse' => $post['name'], 'refuse_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account')->find();
                $levelInfo = M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn');
                $Bid = adminLogAdd('因' . $post['name'] . '拒绝' . $user['account'] . $levelInfo[$logInfo[x_id]] . '升级申请');
                if ($AId && $Bid) {
                    $model->commit();
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    $model->rollback();
                    return array('status' => -1, 'msg' => '操作失败');
                }
            } else {
                return array('status' => -1, 'msg' => '请勿重复操作');
            }
        } else {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
    }

    public function confirmInfo($post) {
        if ($post['id'] > 0) {
            if ($post['name'] == '') {
                return array('status' => -1, 'msg' => '备注不能为空');
            }
            $logInfo = M('level_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('level_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 1, 'confirm' => $post['name'], 'confirm_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account,agent')->find();
                $levelInfo = M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn');
                $Bid = adminLogAdd($post['name'] . '确认' . $user['account'] . $levelInfo[$logInfo[x_id]] . '升级申请');
                $Cid = M('users')->where(array('user_id' => $logInfo['uid']))->save(array('level' => $logInfo[x_id]));
                if ($AId && $Bid && $Cid) {
                    $model->commit();
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    $model->rollback();
                    return array('status' => -1, 'msg' => '操作失败');
                }
            } else {
                return array('status' => -1, 'msg' => '请勿重复操作');
            }
        } else {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
    }

    /**
     * 会员升级
     * @param array $post 提交的数据 [level_id:要升级的等级, secpwd:二级密码,sj_type:升级扣款方式]
     * @param array $user 会员信息 [user_id:会员id,secpwd:会员二级密码,level:会员原等级,tjr_id:推荐人id,bdr_id:报单人id,jh_type:激活类型,jhr_id:激活人,activate:1已激活 2未激活]
     * @return array 操作结果
     */
    public function upgrade($post, $user)
    {
        $userWhere = [
            'user_id' => $user['user_id']
        ];
        if(empty($user)) {
            return ['status' => -1, 'msg' => '请先登陆'];
        }
        if($user['activate'] != 1) {
            return ['status' => -1, 'msg' => '请先激活'];
        }

        if($post['secpwd'] == '') {
            return ['status' => -1, 'msg' => '请输入二级密码'];
        }
        if(webEncrypt($post['secpwd']) != $user['secpwd']) {
            return ['status' => -1, 'msg' => '二级密码验证失败'];
        }

        $levelId = intval($post['level_id']);
        if($levelId <= 0) {
            return ['status' => -1, 'msg' => '请选择等级'];
        }
        if($user['level'] >= $levelId) {
            return ['status' => -1, 'msg' => '升级等级必须大于原等级'];
        }

        # 会员原等级信息
        $userLevelInfo = M('level')->where(['level_id' => $user['level']])->field('amount,b_1,b_1_total')->find();
        # 会员升级等级信息
        $levelInfo = M('level')->where(['level_id' => $levelId])->field('amount,b_1,b_1_total')->find();

        $money = $levelInfo['amount']-$userLevelInfo['amount'];

        $sjType = intval($post['sj_type']);
        if($sjType <= 0) {
            return ['status' => -1, 'msg' => '请选择扣款方式'];
        }
        $moneyId2 = $money2 = 0;
        switch($sjType) {
            case 2:
                $moneyId1 = intval(zfCache('securityInfo.user_upgrade2_money1_id'));
                $moneyId2 = intval(zfCache('securityInfo.user_upgrade2_money2_id'));

                $money1 = $money * floatval(zfCache('securityInfo.user_upgrade2_money1_per')) / 100;
                $money2 = $money * floatval(zfCache('securityInfo.user_upgrade2_money2_per')) / 100;
                break;
            default:

                $moneyId1 = intval(zfCache('securityInfo.user_upgrade1_money1_id'));
                $money1 = $money * floatval(zfCache('securityInfo.user_upgrade1_money1_per')) / 100;
                break;
        }

        if($moneyId1 > 0 && $money1 > 0) {
            if(usersMoney($user['user_id'], $moneyId1) < $money1) {
                return ['status' => -1, 'msg' => moneyList($moneyId1).'余额不足'];
            }
        }
        if($moneyId2 > 0 && $money2 > 0) {
            if(usersMoney($user['user_id'], $moneyId2) < $money2) {
                return ['status' => -1, 'msg' => moneyList($moneyId2).'余额不足'];
            }
        }
        $res = true;
        if($moneyId1 > 0 && $money1 > 0) {
            $res = userMoneyLogAdd($user['user_id'], $moneyId1, '-'.$money1, 123, '升级', 0, $user['user_id']);
        }
        if($moneyId2 > 0 && $money2 > 0) {
            $res = userMoneyLogAdd($user['user_id'], $moneyId2, '-'.$money2, 123, '升级', 0, $user['user_id']);
        }
        if($res) {
            $data = [
                'level' => $levelId
            ];
            M('users')->where($userWhere)->save($data);


            # 计算动态收益
            bonus2Clear($user['tjr_id'], $user['user_id'], $money, $user['account'] . '升级'); // 推荐奖
            $branchInfo = M('users_branch')->where(['uid' => $user['user_id']])->field('branch_id,jdr_id')->find();
            # 统计业绩
            branchYjCount($branchInfo['branch_id'], $user['user_id'], $money, $user['account'].'升级');

            # 计算级差奖
            bonus3Clear($branchInfo['jdr_id'], $user['user_id'], $money, $user['account'].'升级');
            

            # 计算服务中心奖
            if($user['jh_type'] == 6) {
                bonus4Clear($user['bdr_id'], $user['user_id'], $money, $user['account'] . '升级', $user['jhr_id']);
            } else {
                bonus4Clear($user['bdr_id'], $user['user_id'], $money, $user['account'] . '升级');
            }

            # 静态分红处理 s
            $investLogic = new InvesLogic();
            $investWhere = [
                'uid' => $user['user_id']
                ,'statu' => ['neq', 9]
            ];
            $investInfo = $investLogic->where($investWhere)->find();
            $per = $levelInfo['b_1']-$userLevelInfo['b_1'];
//            $maxMoney = $levelInfo['b_1_total']-$userLevelInfo['b_1_total'];
            $maxMoney = (($investInfo['money_total']-$investInfo['total_money']) / $userLevelInfo['b_1_total'] + $money) * $levelInfo['b_1_total'];
            # 判断注册那单是否已经分红完成 如果完成新增一条记录 否则就修改原记录
            if($investInfo) {
                $investSaveData = [
                    'num' => 1
                    ,'price' => $investInfo['price']+$money
                    ,'price_total' => $investInfo['price_total']+$money
                    ,'per' => $investInfo['per']+$per
//                    ,'money_total' => $investInfo['money_total']+$maxMoney
                    ,'money_total' => $maxMoney+$investInfo['total_money']
                ];
                $investLogic->where(['id' => $investInfo['id']])->save($investSaveData);

            } else {
                $investLogic->addInvestLog($user['user_id'], 1, $money, $per, $maxMoney, '补差价升级', 1, 4);
            }

            # 添加升级记录
            $this->addUpgradeLog($user['user_id'], $user['level'], $levelId, '升级');

            return ['status' => 1, 'msg' => '升级成功'];
        } else {
            return ['status' => -1, 'msg' => '升级失败'];
        }
    }

    /**
     * 添加升级记录
     * @param int $userId 会员id
     * @param int $yLevelId 会员原等级id
     * @param int $xLevelId 会员升级等级
     * @param string note 备注
     * @param int $status 状态
     * @return bool 添加状态
     */
    public function addUpgradeLog($userId, $yLevelId, $xLevelId, $note = '', $status = 1)
    {
        $data = [
            'uid' => $userId
            ,'y_id' => $yLevelId
            ,'x_id' => $xLevelId
            ,'zf_time' => time()
            ,'note' => $note
            ,'statu' => $status
        ];

        return M('level_log')->add($data);

    }

}
