<?php

function tuopu_tree($jdr_id, $dtp, $dtp1, $showType = 1) {
    $dluser = $_SESSION['user'];

    $userJdr = M('users_branch')->where(array('branch_id' => $jdr_id))->field('br_num,uid')->find();
    $userJdr = array_merge($userJdr, M('users')->where(array('user_id' => $userJdr['uid']))->field('account')->find());
    if ($dtp >= $dtp1) {
        echo '<table align="center" cellpadding="0" cellspacing="0"><tr>';
        $i = 1;
        $posnum = $userJdr['br_num'];
        for ($m = 1; $m <= $posnum; $m++) {
            $userBranch = M('users_branch')->where(array('jdr_id' => $jdr_id, 'position' => $m))->field('uid, br_num,branch_id,is_cj')->find(); // 接点当前位置的会员
            $user = M('users')->where(array('user_id' => $userBranch['uid']))->field('level, activate, jh_time, account, tjr_id, reg_time')->find(); // 当前会员的信息
            $user = array_merge($user, $userBranch); // 合并会员信息
            $tjr = M('users')->where(array('user_id' => $user['tjr_id']))->field('account')->find(); // 获取推荐人信息
            $level = M('level')->where(array('level_id' => $user['level']))->field('name_cn, color')->find(); // 获取等级信息
            if (!$level) {
                $level['color'] = '#53ffff';
            }
            $c_num = $posnum;
            echo '<td valign="top">';
            if ($c_num > 1) {
                echo '<table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr align="center">
                            <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>';
                if ($posnum > 2) {
                    switch ($i) {
                        case 1:
                            echo '<td width="50%" height="1"></td><td width="50%" height="1" bgcolor="#003399"></td>';
                            break;
                        case $posnum:
                            echo '<td width="50%" height="1" bgcolor="#003399"></td><td width="50%" height="1" ></td>';
                            break;
                        default:
                            echo '<td width="50%" height="1" bgcolor="#003399"></td><td width="50%" height="1" bgcolor="#003399"></td>';
                            break;
                    }
                } else {
                    switch ($i) {
                        case 1:
                            echo '<td width="50%" height="1"></td><td width="50%" height="1" bgcolor="#003399"></td>';
                            break;
                        case 2:
                            echo '<td width="50%" height="1" bgcolor="#003399"></td><td width="50%" height="1" ></td>';
                            break;
                    }
                }
                echo '</tr>
                                </table>
                                <img style="WIDTH: 1px; HEIGHT: 20px" alt="" src="/Public/images/line.gif" border="0" />
                            </td>
                        </tr>
                    </table>
                    ';
            }
            echo '  <!---->';
            // dump($user);
            if (!empty($user)) {
                echo '<table border="0" cellpadding="0" cellspacing="1" bgcolor="#517DBF" align="center" style="margin:0px auto 0 auto;" width="120px">
                        <tr><td align="center" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">';
                echo '<tr><td align="center" bgcolor="' . $level['color'] . '"><a style="WIDTH: 80px;" href="?jdrAccount=' . $user['account'] . '"><font color="ffffff"><strong>' . $user['account'].'-'.$user['branch_id'] . '</strong></font></a></td></tr>';
                echo '<tr><td height="15" align="center" bgcolor="' . $level['color'] . '"><font color="ffffff">' . $tjr['account'] . '</font></td></tr>';
                // if($user['is_cj'] == 2) {
                //     echo '<tr><td align="center" bgcolor="' . $level['color'] . '">已出局</td></tr>';
                // }
                echo '<tr><td bgcolor="' . $level['color'] . '">' . date('Y-m-d H:i:s', $user['reg_time']) . '</td></tr>';

                echo '<tr>
                                    <td align="center" bgcolor="#B0E0E6">
                                        <table width="100%" cellspacing="1" cellpadding="0" border="0" bgcolor="#E7F2FB">
                                            <tbody bgcolor="#c9e8ec" align="center">
                                                <tr><td>&nbsp;</td>';
                for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                    echo '<td>' . branchRegion($j) . '</td>';
                }
                echo '</tr><tr><td width="30%">单</td>';
                for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                    echo '<td align="center" >' . count_man($userBranch['branch_id'], $j) . '</td>'; // 单
                }
                echo '</tr><tr><td width="30%">新</td>';
                for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                    echo '<td align="center" >' . countManForDay($userBranch['branch_id'], $j) . '</td>'; // 单
                }
                echo '</tr><tr><td>总</td>';
                for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                    echo '<td align="center">' . getYj($userBranch['branch_id'], 1, $j) . '</td>';
                }
                // echo '</tr><tr><td>碰</td>';
                // for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                //     echo '<td align="center">' . floatval(getYj($userBranch['branch_id'], 1, $j) - getYj($userBranch['branch_id'], 2, $j)) . '</td>';
                // }
                // echo '</tr><tr><td>余</td>';
                // for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                //     echo '<td align="center">' . getYj($userBranch['branch_id'], 2, $j) . '</td>';
                // }
                echo '</tr><tr><td>新</td>';
                for ($j = 1; $j <= $userBranch['br_num']; $j++) {
                    echo '<td align="center">' . getYjForDay($userBranch['branch_id'], $j) . '</td>';
                }
                echo '</tr>';
                echo '
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                              ';
                echo '<tr><td align="center" background="/Public/images/tab_19.gif">';
                if ($user['activate'] != 1) {
                    echo '<span style="color:red;">未激活</span>';
                } else {
                    echo date('Y-m-d H:i:s', $user['jh_time']);
                }
                echo '</td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    ';
            } else {
                if ($showType == 1) {
                    echo '<table width="50" border="0" cellpadding="0" cellspacing="1" bgcolor="#517DBF" align="center" style="margin:0px auto 0 auto;">
                                <tr>
                                    <td align="center" background="/Public/images/tab_19.gif" width="70">
                                        <a href="javaScript:void(0);" target="_blank"><font color="#0f743c"><strong>无</strong></font></a>
                                    </td>
                                </tr>
                            </table>
                        ';
                } else {
                    echo '<table width="50" border="0" cellpadding="0" cellspacing="1" bgcolor="#517DBF" align="center" style="margin:0px auto 0 auto;">
                                <tr>
                                    <td align="center" background="/Public/images/tab_19.gif" width="70">
                                        <a href="javaScript:void(0);" target="_blank"><font color="#0f743c"><strong>无</strong></font></a>
                                    </td>
                                </tr>
                            </table>
                        ';
                }
            }
            echo '    ';
            // $jdrList = M('users_branch')->where(array('jdr_id' => $userBranch['uid']))->field('uid')->select();
            echo '  <div style="display:" id="table_' . $user['uid'] . '">';
            if ($user['uid'] && $dtp1 < $dtp && $user['activate'] == 1) {
                echo '
                    <table align="center" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center"><img style="WIDTH: 1px; HEIGHT: 20px" alt="" src="/Public/images/line.gif" border="0" /></td>
                        </tr>
                    </table>
                ';
                tuopu_tree($user['branch_id'], $dtp, $dtp1 + 1, $showType);
            }
            echo '</div></td>';
            $i++;
        }
        echo '</tr></table>';
    }
}

/*
 * 统计安置网络图团队人数
 * $v 会员id
 * $k 会员接点位置
 */

function count_man($v, $k) {
    $model = M('users_branch');
    $user = $model->where("branch_id='{$v}'")->field('branch_id, path')->find();
    $path = $user['path'] . ',' . $user['branch_id'];
    $man = $model->where("path='" . $path . "' and position=$k")->field('branch_id, path')->find();
    if (!$man) {
        return 0;
    }

    $paths = $man['path'] . ',' . $man['branch_id'];

    $ssb = $model->where("path like '" . $paths . "%'")->count();
    $res = 1 + $ssb;
    return $res;
}
/*
 * 统计安置网络图团队人数今天
 * $v 会员id
 * $k 会员接点位置
 */

function countManForDay($v, $k) {
    $model = M('users_branch');
    $user = $model->where(['branch_id' => $v])->field('branch_id, path')->find();
    $path = $user['path'] . ',' . $user['branch_id'];
    $man = $model->where(['path' => $path, 'position' => $k])->field('branch_id, path, add_time')->find();
    if (!$man) {
        return 0;
    }
    $count = 0;
    if($man['add_time'] >= strtotime(date('Ymd'))) {
        $count = 1;
    }

    $paths = $man['path'] . ',' . $man['branch_id'];

    $count += $model->where(['path' => ['like', '%'.$paths.'%'], 'add_time' => ['egt', strtotime(date('Ymd'))]])->count();
    return $count;
}

function getYj($branchId, $type, $position) {
    $info = M('users_branch_yj')->where(['b_id' => $branchId, 'type' => $position])->field('total, new')->find();
    if ($info) {
        switch ($type) {
            case 1:
                return floatval($info['total']);
                break;

            case 2:
                return floatval($info['new']);
                break;
            default:
                return 0;
                break;
        }
    }
    return 0;
}

/**
 * 获取今日新增业绩
 * @param  int   $branchId 网络图id
 * @param  int   $position 区域
 * @return float           今日新增业绩
 */
function getYjForDay($branchId, $position)
{
    $info = M('users_branch_yj_day')->where(['b_id' => $branchId, 'pos' => $position, 'zf_time' => strtotime(date('Ymd'))])->find();
    if(!$info) {
        return 0;
    }

    return floatval($info['money']);

}

/**
 * 计算接点人位置
 * @param $id 会员id
 * @param int $type 左中右
 * @return mixed
 */
function jsPos($id, $type = 0) {
    $user_branch = M('users_branch');
    $jdr = M()->table('__USERS_BRANCH__ ub')->join('__USERS__ u on u.user_id = ub.uid')->where("ub.jdr_id = {$id}")->field('ub.position, ub.branch_id')->order('ub.position')->select();
    $jdr = convertArrKey($jdr, 'position');
    if ($jdr[$type]['branch_id']) {
        return jsPos($jdr[$type]['branch_id'], $type);
    } else {
        return $id;
    }
}

/**
 * 获取接点人
 * @param string $findAccount 查询账号 如果有接点人就是接点人否则就是推荐人 如果都没有就取公司第一个会员
 * @param int $pos
 */
function getJdrList($findAccount, $pos) {

    // 如果没有推荐人和接点人就查出平台第一个会员
    if (!$findAccount) {
        $findAccount = M()->table('__USERS_BRANCH__ ub')->join('__USERS__ u on ub.uid = u.user_id')->order('ub.uid asc')->limit(1)->getField('u.account');
    }

    $tjrUser = M('users')->where("account = '{$findAccount}'")->field('user_id, account')->limit(1)->find(); // 查出接点人信息

    if ($pos) {
        $jdrList[$pos] = $tjrUser;
    } else {

        $jdrBranchList = M('users_branch')->where("jdr_id = {$tjrUser['user_id']}")->order('position')->limit(2)->select(); // 查出接点人下面的接点人

        $jdrBranchList = convertArrKey($jdrBranchList, 'position');
        $tjrUser && $tjrNum = M('users')->where("tjr_id = {$tjrUser['user_id']}")->count();
        $jdrList = array();
        for ($i = 1; $i <= 2; $i++) {
            if ($i == 2 && $tjrNum <= 0 && $tjrUser) {
                continue;
            }
            if ($jdrBranchList[$i]) {
                $res = jsPos($jdrBranchList[$i]['user_id'], 0);
                $jdrList[$i] = M('users')->where("user_id = {$res}")->field('user_id, account')->limit(1)->find();
            } else {
                $json = json_encode($jdrList);
                // 判断是否已经存在
                if (!stristr($json, $tjrUser['account'])) {
                    $jdrList[$i] = $tjrUser;
                }
            }
        }
    }

    return $jdrList;
}

function branchRegion($id = 0) {
    $branchRegion = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
    $arr = array();
    foreach (explode(',', $branchRegion) as $k => $v) {
        $arr[$k + 1] = $v;
    }

    return ($id > 0 ? $arr[$id] : $arr);
}

/**
 * 添加会员接点位置
 * @param int $userId 报单会员id
 * @return int 会员id
 */
function addUserBranch($userId)
{
    $ubModel = M('users_branch');

    # 获取层
    $paths = $ubModel->order('path_num desc')->group('path_num')->lock(true)->field('path_num')->select();

    $paths = getArrColumn($paths, 'path_num');

    $lastJdrs = [];
    foreach ($paths as $v) {
        $jdrs = $ubModel->where(['path_num' => $v])->order('cast(sy_num as decimal) asc')->field('branch_id,jdr_id')->select();
        # 判断层有没有满
        if (count($jdrs) == pow(5, $v - 1)) {
            foreach ($jdrs as $value) {
                $count = count($lastJdrs[$value['branch_id']]);
                if ($count < 5) {
                    $jdrId = $value['branch_id'];
                    goto addBranch;
                }
            }
        } else {
            $lastJdrs = [];
            foreach ($jdrs as $val) {
                $lastJdrs[$val['jdr_id']][] = $val;
            }
        }
    }
    return true;
    addBranch:
    $branch = $ubModel->where(['branch_id' => $jdrId])->find();
    if ($branch) {
        $branchData = [
            'uid' => $userId
            , 'jdr_id' => $branch['branch_id']
            , 'path' => $branch['path'] . ',' . $branch['branch_id']
            , 'path_num' => $branch['path_num'] + 1
            , 'position' => $count + 1
            , 'sy_num' => $branch['sy_num'] * 5 - (5 - ($count + 1 + 1))
        ];
        $branchId = $ubModel->add($branchData);
        $upData = array();
        for ($i = 1; $i <= 5; $i++) {
            $upData[] = [
                'type' => $i
                , 'add_time' => time()
                , 'uid' => $userId
                , 'b_id' => $branchId
            ];
        }
        M('users_branch_yj')->addAll($upData);
        return $branchId;
    }
    return false;
}


/**
 * 打款
 * @param int $branchId 会员接点id
 * @return bool
 */
function playMoney($branchId)
{

    $userBranch = D('UsersBranch')->getBranchById($branchId);


    if($userBranch['jdr_id'] > 0) {
        $user = M('users')->where(['user_id' => $userBranch['uid']])->field('level')->find();
        $upLevel = levelInfo($user['level']+1);
        $userLevel = levelInfo($user['level']);

        $num = $userLevel['level_id']-1;
        $num = ($num <= 0 ? 1 : $num);
        $jdrId = $userBranch['jdr_id'];
        $cs = 0;
        for($i = 0; $i < $num; $i++) {
            $yJdrBranch = $jdrBranch = D("UsersBranch")->getBranchById($jdrId);
            $jdrId = intval($jdrBranch['jdr_id']);
            $cs += 1;
        }

        if(!$jdrBranch) {
            return true;
        }
        if($jdrBranch['uid'] != 1) {
            $jdrUser = M('users')->where(['user_id' => $jdrBranch['uid']])->field('level,is_need_tj')->find();
            if($jdrUser['level'] < $upLevel['level_id'] || $jdrUser['is_need_tj'] == 1) {
                $jdrBranch = D("UsersBranch")->getBranchByUid(1);
            }
        }
        $num = M('users_play_money')->where(['to_uid' => $userBranch['uid'], 'level_id' => $userLevel['level_id']])->count();
        if($num >= $userLevel['sk_num']) {
            if(usersMoney($userBranch['uid'], 1) >= $upLevel['amount']) {
                # 升级 s
                $data = [
                    'level' => $upLevel['level_id']
                ];

                M('users')->where(['user_id' => $userBranch['uid']])->save($data);
                (new \Zfuwl\Logic\LevelLogic())->addUpgradeLog($userBranch['uid'], $userLevel['level_id'], $upLevel['level_id'], '达到条件自动升级');
                # 升级 e
                # 扣款并添加打款记录 s
                userMoneyLogAdd($userBranch['uid'], 1, '-'.$upLevel['amount'], 123, '给上'.$cs.'层转积分');
                userMoneyLogAdd($jdrBranch['uid'], 1, $upLevel['amount'], 124, '下'.$cs.'层转积分');
                $playMoneyData = [
                    'uid' => $userBranch['uid']
                    ,'to_uid' => $jdrBranch['uid']
                    ,'add_time' => time()
                    ,'money' => $upLevel['amount']
                    ,'level_id' => $upLevel['level_id']
                    ,'confirm_time' => time()
                    ,'status' => 2
                ];
                M('users_play_money')->add($playMoneyData);
                # 扣款并添加打款记录 e
                # 判断收款人推荐人 s
                $tjrNum = M('users')->where(['tjr_id' => $jdrBranch['uid']])->count();
                if($tjrNum <= $upLevel['level_id']-1 && $jdrBranch['uid'] != 1) {
                    $jdrUserData = [
                        'is_need_tj' => 1
                        ,'need_tj_time' => time()
                    ];
                    M('users')->where(['user_id' => $jdrBranch['uid']])->save($jdrUserData);
                }
                # 推荐收款人推家人 e

                playMoney($yJdrBranch['branch_id']);
            }
        }
    }
    return false;
}

/**
 * 判断解除推荐
 */
function detectUnTj($userId)
{
    $where = [
        'user_id' => (int)$userId
    ];
    $user = M('users')->where($where)->find();
    if($user) {
        if($user['is_need_tj'] == 1 && $user['need_tj_time'] + 86400*7 > time() && $user['tj_cs_time'] <= 0) {
            $tjrNum = M('users')->where(['tjr_id' => $user['user_id']])->count();
            if($tjrNum >= $user['level']-1) {
                $data = [
                    'is_need_tj' => 1
                    ,'need_tj_time' => 0
                ];

                M('users')->where($where)->save($data);
            }
        }
    }
}

/**
 * 判断推荐人超时
 */
function detectTimeout()
{
    $userList = M('users')->where(['is_need' => 1])->select();
    foreach($userList as $v) {
        if($v['need_tj_time'] + 86400*7 <= time() && $v['tj_cs_time'] <= 0) {
            $data = [
                'tj_cs_time' => time()
            ];

            $where = [
                'user_id' => $v['user_id']
            ];

            M('users')->where($where)->save($data);
        }
    }

    return true;
}

/**
 * 推荐人解冻
 */
function unLockForTjr($userId)
{
    $where = [
        'id' => (int)$userId
        ,'tj_cs_time' => ['gt', 0]
    ];
    $user = M('users')->where($where)->find();
    if($user) {

        if($user['need_tj_time'] + 86400*7 > time()) {
            $tjrNum = M('users')->where(['tjr_id' => $user['user_id'], 'reg_time' => ['egt', $user['tj_cs_time']]])->count();

            if($tjrNum >= 2) {
                $data = [
                    'is_need_tj' => 2
                    ,'tj_cs_time' => 0
                ];
                M('users')->where($where)->save($data);
            }
        }
    }
}