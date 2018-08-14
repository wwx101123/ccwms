<?php

namespace Mobile\Controller;

class TeamController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $this->display('index');
    }

    /**
     * 添加会员
     */
    public function addUser() {
        $this->assign('levelList', M('level')->where(array('statu' => 1, 'reg' => 1))->cache('levelList')->select());
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\RegLogic();
            $res = $model->addUser($data, $this->user_id);
            if (!$data['regProtocol']) {
                $this->error('请先阅读并同意会员注册协义');
            }
            if ($res['status'] == 1) {
                $this->success('注册成功', U('Team/teamIndex'));
                exit;
            } else {
                $this->error($res['msg']);
            }
        } else {
            $a = range(0, 9);
            $regAccountNum = rand(zfCache('regInfo.account_mai'), zfCache('regInfo.account_max'));
            $account = zfCache('regInfo.account_start');
            for ($i = 0; $i < zfCache('regInfo.account_max') - (strlen(zfCache('regInfo.account_start')) + strlen(date('s'))); $i++) {
                $account .= array_rand($a); // 拼接会员帐号
            }
            $account .= date('s');  // 生成会员帐号
            if (zfCache('regInfo.auto_account') == 1) {
                $this->assign('account', $account);
            }

            A('Reg')->regProtocol();

            $this->display('addUser');
        }
    }

    /**
     * 直推列表
     */
    public function ztIndex() {
        $condition = array();
        $condition['tjr_id'] = $this->user_id;
        $count = M('users')->where($condition)->count();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 20;
            $list = M('Users')->where($condition)->limit($p * $pSize, $pSize)->order('reg_time desc')->select();

            $this->assign('list', $list);

            $this->display('ztIndexAjax');
        } else {
            // 查出直推人数
          	$this->assign('countDuan', count(teamAll($this->user_id)));
            $this->assign('tjrNum', M('users')->where(['tjr_id' => $this->user_id])->select());
            $this->assign('teamAllMoney', teamAllMoney($this->user_id));
            $this->display('ztIndex');
        }
    }

    /**
     * 会员推广页
     */
    public function extension() {

        $this->display('extension');
    }

    /**
     * 推荐网络图
     */
    public function tjwlt()
    {
        $this->display('tjwlt');
    }

    /*
     * 获取会员数据
     */

    public function getUser()
    {
        global $tjrList;
        $tjrList = array();
        $res5 = getTjrList($this->user_id, 2, 1) ;
        $userIdArr5 = getArrColumn($res5, 'user_id');
        $userIdArr5[] = $this->user_id;
        if ($_GET['account']) {
            $where['account'] = $_GET['account'];
            $user = M('users')->where(array('account' => $_GET['account'], 'is_type' => 1))->field('user_id')->find();
            $res = nexttd($this->user_id);
            $res[] = $this->user_id;
            if (!in_array($user['user_id'], $res)) {
                $arr[0]['id'] = '1';
                $arr[0]['text'] = '未找到会员信息';
                exit(json_encode($arr));
            }
        } else {
            I('id') ? $where['tjr_id'] = I('id') : $where['user_id'] = $this->user_id;
        }
        $where['is_type'] = 1;
        $userList = M('users')->where($where)->select();

        foreach($userList as $k => $v) {
            if(in_array($v['user_id'],$userIdArr5)) {
                $arr[$k]['id'] = $v['user_id'];
                $arr[$k]['text'] = '会员账号：'.$v['account'];
                $tjrList = M('users')->where(array('tjr_id' => $v['user_id'], 'is_type' => 1))->field('user_id')->select();
                $tjrNum = count($tjrList);
                if($tjrNum > 0) {
                    foreach($tjrList as $val) {
                        if(in_array($val['user_id'], $userIdArr5)) {
                            $arr[$k]['state'] = 'closed';
                        }
                    }
                }
            }
        }

        exit(json_encode($arr));
    }
    /**
     * 团队总览
     */
    public function teamPandect() {

        $userArr = array();

//        $totalUserIdArr = nexttd($this->user_id);
        $totalUserIdArr = getTjrList($this->user_id, 2, 1) ;
        $totalUserIdArr = getArrColumn($totalUserIdArr, 'user_id');
        $totalUserNum = count($totalUserIdArr);
        $oneUserIdArr = M('users')->where(array('tjr_id' => $this->user_id))->getField('account, user_id');
        $oneNum = count($oneUserIdArr);
        if (!$oneUserIdArr) {
            $oneUserIdArr = array(0);
            $oneNum = 0;
        } else {
            $twoUserIdArr = M('users')->where(array('tjr_id' => array('in', $oneUserIdArr)))->getField('account, user_id');
        }
        // $sql = 'select user_id from ' . C('DB_PREFIX') . 'users where tjr_id in((select user_id from ' . C('DB_PREFIX') . 'users where tjr_id=' . $this->user_id . '),0);';
        // $twoUserList = M()->query($sql);
        // $twoUserIdArr = getArrcolumn($twoUserList, 'user_id');

        $userArr[] = array(
            'name' => '会员总和',
            'total' => $totalUserNum,
            'one' => $oneNum,
            'two' => count($twoUserIdArr)
        );
        if (!$twoUserIdArr) {
            $twoUserIdArr = array(0);
        }
        if (!$totalUserIdArr) {
            $totalUserIdArr = array(0);
            $totalUserNum = 0;
        }
        $levelList = M('level')->field('level_id, name_cn')->where(array('statu' => 1))->select();
        $levelTotal = M('users')->where(array('user_id' => array('in', $totalUserIdArr)))->group('level')->getField('level, count(*) as num');
        $levelOne = M('users')->where(array('user_id' => array('in', $oneUserIdArr)))->group('level')->getField('level, count(*) as num');
        $levelTwo = M('users')->where(array('user_id' => array('in', $twoUserIdArr)))->group('level')->getField('level, count(*) as num');
        foreach ($levelList as $v) {
            $userArr[] = array(
                'name' => $v['name_cn'],
                'total' => $levelTotal[$v['level_id']],
                'one' => $levelOne[$v['level_id']],
                'two' => $levelTwo[$v['level_id']]
            );
        }

        $this->assign('userArr', $userArr);
        $this->assign('totalUserNum', $totalUserNum);
        $list = M('money_log')->where(array('uid' => array('in', $totalUserIdArr), 'money' => array('gt', 0), 'mid' => 4))->field('money')->select();
        $yj = 0;
        foreach($list as $v) {
            $yj += $v['money'];
        }

        $this->assign('yj', $yj);

        $this->display('teamPandect');
    }

    /**
     * 团队
     */
    public function teamIndex()
    {
        $this->assign('ztcount', M('users')->where(['tjr_id' => $this->user_id])->count());
        $levelInfo = levelInfo($this->user['level']);
        $this->assign('levelInfo', $levelInfo);
        $this->display('teamIndex');
    }
}
