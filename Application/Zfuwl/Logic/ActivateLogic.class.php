<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class ActivateLogic extends RelationModel {

    protected $tableName = 'users';


    /**
     * 激活会员
     * @param int $userId 会员id
     * @param int $type   激活类型 1 激活 2 激活并投资
     * @param int $jhrId 激活人id
     * @param int $jhType 激活类型 1 推荐人激活 2会员自己激活
     * @return array
     */
    public function activateUser($userId, $type, $jhrId, $jhType = 1)
    {

        $userId = (int)$userId;
        $type = (int)$type;
        $jhrId = (int)$jhrId;

        $jhUser = M('users')->where(['user_id' => $userId])->find();
        if(empty($jhUser)) {
            return ['status' => -1, 'msg' => '此会员不存在'];
        }
        if($jhUser['activate'] == 1) {
            return ['status' => -1, 'msg' => '请勿重复激活'];
        }

        $level = M('level')->where(['level_id' => $jhUser['level']])->find();

        $jhm = (int)$level['jhm_num'];
        $jhMoney = (float)$level['amount'];
        $xhMoney = 0;
        $isavtivateNote = '';
        $res = true;

        $jhrUser = M('users')->where(['user_id' => $jhrId])->find();

        if(usersMoney($jhrId, 3) < $jhm && $jhm > 0) {
            return ['status' => -1, 'msg' => moneyList(3).'余额不足'];
        }
        if($type == 2) {
            $xhMoney = $jhMoney * (float)zfCache('regInfo.ma_per') / 100;
            if(usersMoney($jhrId, zfCache('regInfo.ma_id')) < $xhMoney && $xhMoney > 0) {
                return ['status' => -1, 'msg' => moneyList(zfCache('regInfo.ma_id')).'余额不足'];
            }
        }
        if($jhm > 0) {
            $res = userMoneyLogAdd($jhrId, 3, '-' . $jhm, 102, '会员激活', session('admin_id'), $userId);
            usersDay($jhrId, 'out_3', $jhm, 0, 0);
            bochuDay('money_3', $jhm);
        }
        if($xhMoney > 0) {
            $res = userMoneyLogAdd($jhrId, zfCache('regInfo.ma_id'), '-' . $xhMoney, 102, '会员激活', session('admin_id'), $userId);
            usersDay($jhrId, 'out_'.zfCache('regInfo.ma_id'), $xhMoney, 0, 0);
            bochuDay('money_'.zfCache('regInfo.ma_id'), $xhMoney);
        }

        if($res) {
            $userSaveData = [
                'activate' => 1
                ,'user' => 1
                ,'jh_time' => time()
                ,'jh_type' => $jhType
                ,'jhr_id' => $jhrId
            ];
            if ($userId != $jhrId) {
                userAction($userId, $jhrUser['account'] . $isavtivateNote, 3);
            } else {
                userAction($userId, $isavtivateNote, 3);
            }

            if($type == 1) {
                $userSaveData['is_tz'] = 1;
            } else {
                $userSaveData['tz_time'] = time();
            }

            $jhUserId = M('users')->where(['user_id' => $userId])->save($userSaveData);
            if($jhUserId) {
                if($type == 2) {
                    # 添加分红记录 s
                    $investLogic = new InvesLogic();
                    $investLogic->addInvestLog($jhUser['user_id'], $level['b_1'],$level['amount'], $level['amount'], $level['b_1_day'], '注册');
                    # 添加分红记录 e

                    # 计算销售红利
                    bonus2Clear($jhUser['tjr_id'], $jhUser['user_id'], $jhMoney, $jhUser['account'] . '激活'); // 推荐奖
                    # 计算管理红利
                    bonus3Clear($jhUser['tjr_id'], $jhUser['user_id'], $jhMoney, $jhUser['account'] . '激活'); // 推荐奖
                }

                return ['status' => 1, 'msg' => '激活成功'];
            }
        }

        return ['status' => -1, 'msg' => '激活失败'];
    }


    /**
     * 激活会员列表
     * @param int $uId  会员id
     * @param int $isType
     * @param int $ldrId  领导人 ID
     * @param int $jhType  激活方式 3 管理员实单激活  4 推荐人激活   5  报单中心激活  6 服务中心激活   7  会员自己激活自己 8会员激活
     * @return array
     */
    public function userActivate($uId, $isType, $ldrId = 0, $jhType = 1) {
        $jhUser = M('users')->where(array('user_id' => $uId))->find();
        if ($jhUser['activate'] == 1) {
            return ['status' => -1, 'msg' => '请勿重复激活'];
        }

//        $jhMoney = floatval(zfCache('regInfo.jh_money'));


        $model = new \Think\Model();
        $model->startTrans();
        $level = M('level')->where(['level_id' => $jhUser['level']])->find();
        $jhMoney = floatval($level['amount']);
        if ($ldrId > 0) {
            $ldrUser = M('users')->where(['user_id' => $ldrId])->field('account')->find();
        }
        $giveaId = $givebId = 1;
        if ($isType == 1) {
            # 扣款方案一
            if ($ldrId > 0) {
                $A = $this->activateTypeA($ldrId, $jhMoney);
            } else {
                $A = $this->activateTypeA($uId, $jhMoney);
            }
            $isavtivateNote = '100 %' . moneyOne(zfCache('regInfo.ma_id'))['name_cn'] . $level['amount'] . '激活';
        } elseif ($isType == 2) {
            if ($ldrId > 0) {
                $A = $this->activateTypeB($ldrId, $jhMoney);
            } else {
                $A = $this->activateTypeB($uId, $jhMoney);
            }
            if (zfCache('regInfo.mb_id') > 0 && zfCache('regInfo.mb_per') > 0) {
                $isavtivateNote = zfCache('regInfo.mb_per') . '%' . moneyOne(zfCache('regInfo.mb_id'))['name_cn'] . ($jhMoney * zfCache('regInfo.mb_per') / 100);
            }
            if (zfCache('regInfo.mc_id') > 0 && zfCache('regInfo.mc_per') > 0) {
                $isavtivateNote .= '+' . zfCache('regInfo.mc_per') . '%' . moneyOne(zfCache('regInfo.mc_id'))['name_cn'] . ($jhMoney * zfCache('regInfo.mc_per') / 100);
            }
            if (zfCache('regInfo.md_id') > 0 && zfCache('regInfo.md_per') > 0) {
                $isavtivateNote .= '+' . zfCache('regInfo.md_per') . '%' . moneyOne(zfCache('regInfo.md_id'))['name_cn'] . ($jhMoney * zfCache('regInfo.md_per') / 100);
            }
            $isavtivateNote .= '激活';
            # 扣款方案二
        } elseif ($isType == 3) {
            # 扣款方案三
            if ($ldrId > 0) {
                $A = $this->activateTypeC($ldrId, $jhMoney);
            } else {
                $A = $this->activateTypeC($uId, $jhMoney);
            }
            $isavtivateNote = moneyOne(zfCache('regInfo.me_id'))['name_cn'] . zfCache('regInfo.me_per') . '个激活';
        }

        if($level['jhm_num'] > 0) {
            $money = $level['jhm_num'];
            if($money > 0) {
                if ($ldrId > 0) {
                    if(usersMoney($ldrId, 3) < $money) {
                        return ['status' => -1, 'msg' => moneyList(3).'余额不足'];
                    }
                    userMoneyLogAdd($ldrId, 3, '-' . $money, 102, '会员激活', session('admin_id'), $uId);
                } else {
                    if(usersMoney($uId, 3) < $money) {
                        return ['status' => -1, 'msg' => moneyList(3).'余额不足'];
                    }
                    userMoneyLogAdd($uId, 3, '-' . $money, 102, '会员激活', session('admin_id'), $uId);
                }
            }
        }
        if ($A['status'] <= 0) {
            return $A;
        }
        $userSaveData = [
            'activate' => 1
            ,'user' => 1
            ,'jh_time' => time()
            ,'jh_type' => $jhType
            ,'jhr_id' => ($ldrId ? $ldrId : $uId)
        ];
        if ($ldrId > 0) {
            userAction($uId, $ldrUser['account'] . $isavtivateNote, 3);
        } else {
            userAction($uId, $isavtivateNote, 3);
        }
        // giveUserShares($uId, '激活赠送');
        // if ($level['givea_m'] > 0 && $level['givea_p'] > 0) {
        //     $giveaId = userMoneyLogAdd($jhUser['user_id'], $level['givea_m'], $level['givea_p'], 107, '激活赠送');
        // }
        // if ($level['giveb_m'] > 0 && $level['giveb_p'] > 0) {
        //     $givebId = userMoneyLogAdd($jhUser['user_id'], $level['giveb_m'], $level['giveb_p'], 107, '激活赠送');
        // }
        $jhUserId = M('users')->where(['user_id' => $uId])->save($userSaveData);
        if ($A > 0 && $jhUserId) {

            # 添加分红记录 s
            $investLogic = new InvesLogic();
            /*$b1Arr = explode(',', $level['b_1']);
            $periodsTimeArr = explode(',', $level['periods_time']);
            for($i = 1; $i<=$level['periods']; $i++) {
                if($i == 1) {
                    $startTime = time();
                } else {
                    $startTime = 0;
                }
                $investLogic->addInvestLog($jhUser['user_id'], $b1Arr[$i-1], $periodsTimeArr[$i-1], $i, $startTime, '注册');
            }*/
            $investLogic->addInvestLog($jhUser['user_id'], $level['b_1'],$level['amount'], $level['amount'], $level['b_1_day'], '注册');
            # 添加分红记录 e

            # 计算销售红利
            bonus2Clear($jhUser['tjr_id'], $jhUser['user_id'], $jhMoney, $jhUser['account'] . '激活'); // 推荐奖
            # 计算管理红利
            bonus3Clear($jhUser['tjr_id'], $jhUser['user_id'], $jhMoney, $jhUser['account'] . '激活'); // 推荐奖
            $model->commit();
            return ['status' => 1, 'msg' => '激活成功'];
        } else {
            $model->rollback();
            return ['status' => -1, 'msg' => '激活失败'];
        }
    }

    /**
     * 扣款方案一
     * @param int $uId 会员ID
     * @param float $money 金额
     * @return array
     */
    public function activateTypeA($uId, $money) {
        $moneyId = zfCache('regInfo.ma_id');
        $per = zfCache('regInfo.ma_per');
        if ($moneyId > 0 && $per > 0) {
            $maPrice = $money * $per / 100;
            if (usersMoney($uId, $moneyId) < $maPrice) {
                return ['status' => -1, 'msg' => moneyOne($moneyId)[name_cn] . '余额不足'];
            } else {
                $maId = userMoneyLogAdd($uId, $moneyId, '-' . $maPrice, 102, '会员激活', session('admin_id'), $uId);
                $dayId = usersDay($uId, 'out_' . $moneyId, $maPrice, 0, 0);
                bochuDay('money_' . $moneyId, $maPrice);
                if ($maId && $dayId) {
                    return ['status' => 1, 'msg' => '扣除成功'];
                }
            }
        }
    }

    /**
     * 扣款方案二
     * @param int $uId 会员ID
     * @param int $money 金额
     * @return array
     */
    public function activateTypeB($uId, $money) {
        $mbId = $mcId = $mdId = $mbDayId = $mcDayId = $mdDayId = 1;
        if (zfCache('regInfo.mb_id') > 0 && zfCache('regInfo.mb_per') > 0) {
            $mbPrice = $money * zfCache('regInfo.mb_per') / 100;
            if (usersMoney($uId, zfCache('regInfo.mb_id')) < $mbPrice) {
                return ['status' => -1, 'msg' => moneyOne(zfCache('regInfo.mb_id'))[name_cn] . '余额不足'];
            } else {
                $mbId = userMoneyLogAdd($uId, zfCache('regInfo.mb_id'), '-' . $mbPrice, 102, '会员激活', session('admin_id'), $uId);
                $mbDayId = usersDay($uId, 'out_' . zfCache('regInfo.mb_id'), $mbPrice, 0, 0);
                bochuDay('money_' . zfCache('regInfo.mb_id'), $mbPrice);
            }
        }
        if (zfCache('regInfo.mc_id') > 0 && zfCache('regInfo.mc_per') > 0) {
            $mcPrice = $money * zfCache('regInfo.mc_per') / 100;
            if (usersMoney($uId, zfCache('regInfo.mc_id')) < $mcPrice) {
                return ['status' => -1, 'msg' => moneyOne(zfCache('regInfo.mc_id'))[name_cn] . '余额不足'];
            } else {
                $mcId = userMoneyLogAdd($uId, zfCache('regInfo.mc_id'), '-' . $mcPrice, 102, '会员激活', session('admin_id'), $uId);
                $mcDayId = usersDay($uId, 'out_' . zfCache('regInfo.mc_id'), $mcPrice, 0, 0);
                bochuDay('money_' . zfCache('regInfo.mc_id'), $mcPrice);
            }
        }
        if (zfCache('regInfo.md_id') > 0 && zfCache('regInfo.md_per') > 0) {
            $mdPrice = $money * zfCache('regInfo.md_per') / 100;
            if (usersMoney($uId, zfCache('regInfo.md_id')) < $mdPrice) {
                return ['status' => -1, 'msg' => moneyOne(zfCache('regInfo.md_id'))[name_cn] . '余额不足'];
            } else {
                $mdId = userMoneyLogAdd($uId, zfCache('regInfo.md_id'), '-' . $mdPrice, 102, '会员激活', session('admin_id'), $uId);
                $mdDayId = usersDay($uId, 'out_' . zfCache('regInfo.md_id'), $mdPrice, 0, 0);
                bochuDay('money_' . zfCache('regInfo.md_id'), $mdPrice);
            }
        }
        if ($mbId && $mcId && $mdId && $mbDayId && $mcDayId && $mdDayId) {
            return ['status' => 1, 'msg' => '扣除成功'];
        }
    }

    /**
     * 扣款方案三
     * @param int $uId 会员ID
     * @param int $money 金额
     * @return array|int
     */
    public function activateTypeC($uId, $money) {
        if (zfCache('regInfo.me_id') > 0 && zfCache('regInfo.me_per') > 0) {
            $mePrice = zfCache('regInfo.me_per');
            if (usersMoney($uId, zfCache('regInfo.me_id')) < $mePrice) {
                return ['status' => -1, 'msg' => moneyOne(zfCache('regInfo.me_id'))[name_cn] . '余额不足'];
            } else {
                $meId = userMoneyLogAdd($uId, zfCache('regInfo.me_id'), '-' . $mePrice, 102, '会员激活', session('admin_id'), $uId);
                $dayId = usersDay($uId, 'out_' . zfCache('regInfo.me_id'), $mePrice, 0, 0);
                $moneyInfo = moneyOne(zfCache('regInfo.me_id'));
                $bcId = bochuDay('money_' . zfCache('regInfo.me_id'), ($mePrice * $moneyInfo['c_pre']));
                if ($meId && $dayId && $bcId) {
                    return $meId;
                }
            }
        }
    }

    /**
     * 空单激活会员
     * @param type $uId 要激活的会员
     * @param type $adminId 管理员 ID
     */
    public function emptyActivate($uId) {
        $jhUserId = M('users')->where(array('user_id' => $uId))->save(array('activate' => 1, 'user' => 2, 'jh_time' => time()));
        $jhLogId = userAction($uId, session('admin_name') . '空单激活', 2);
        if ($jhUserId && $jhLogId) {
            return array('status' => 1, 'msg' => '激活成功');
        } else {
            return array('status' => -1, 'msg' => '激活失败');
        }
    }

    /**
     * 回填单激活会员
     * @param type $uId 要激活的会员
     * @param type $adminId 管理员 ID
     */
    public function backActivate($uId) {
        $jhUserId = M('users')->where(array('user_id' => $uId))->save(array('activate' => 1, 'user' => 3, 'jh_time' => time()));
        $jhLogId = userAction($uId, session('admin_name') . '回填单激活', 3);
        if ($jhUserId && $jhLogId) {
            return array('status' => 1, 'msg' => '激活成功');
        } else {
            return array('status' => -1, 'msg' => '激活失败');
        }
    }

}
