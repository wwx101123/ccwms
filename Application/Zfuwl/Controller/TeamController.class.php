<?php

namespace Zfuwl\Controller;

class TeamController extends CommonController {

    /**
     * 直推树状图
     */
    public function ztNetwork() {
        $this->display('ztNetwork');
    }

    /**
     * 推荐网络图
     */
    public function tjNetworkTu() {
        $user = M('users');
        $zuihou = $user->order('user_id')->limit(1)->find();
        $account = urldecode(trim($_GET['account']));
        if ($account) {
            $dluser = $user->where(array('account' => $account))->find();
            if (!$dluser) {
                $this->error('该会员不存在！');
            }
        } else {
            $dluser = $zuihou;
        }
        if ($dluser) {
            $dluser['tjrAccount'] = $user->where(['user_id' => $dluser['tjr_id']])->getField('account');
            $this->assign('info', $dluser);
            // $this->assign('tjr', $user->where(array('user_id' => $dluser['tjr_id']))->find());
            $this->assign('level', M('level')->where(array('level_id' => $dluser['level']))->find());
            $this->assign('xjUser', M('users')->where(array("tjr_id" => $dluser['user_id']))->field('user_id')->select());
            $this->assign('zuihou', $zuihou);
        }

        $this->display('tjNetworkTu');
    }

    /**
     * 安置树状网络图
     */
    public function jdNetwork() {
        $this->display('jdNetwork');
    }

    public function jdNetworkTu() {
        $account = urldecode(trim($_GET['jdrAccount']));
        $user = M('users');
        $zuihou = $user->order('user_id')->limit(1)->find();
        if ($account) {
            $dluser = $user->where(array('account' => $account, 'is_type' => 1))->find();
            if (!$dluser) {
                $this->error('该会员不存在！');
            }
        } else {
            $dluser = $zuihou;
        }
        if ($dluser) {
            if ($_GET['type']) {
                $dluser = array_merge(M('users_branch')->where(array('uid' => $dluser['user_id']))->find(), $dluser);
                $userList = M('users_branch')->where("jdr_id = " . $dluser['branch_id'])->order('position')->select();
                $userList = convertArrKey($userList, 'position');
                for ($i = 1; $i <= 3; $i++) {
                    if ($i == $_GET['type'] && $userList[$i]) {
                        $res = jsPos($userList[$i]['branch_id'], $i);
                        if ($res) {
                            // $user = M('users_branch')->where(array('branch_id' => $res))->field('uid')->find();
                            $userInfo = M('users_branch')->where(array('branch_id' => $res))->field('uid')->find();
                            $dluser = M('users')->where(array('user_id' => $userInfo['uid']))->find();
                        }
                    }
                }
            }
            $dluser = array_merge(M('users_branch')->where(array('uid' => $dluser['user_id']))->find(), $dluser);
            $dluser['jdrAccount'] = M('users')->where("user_id = {$dluser['jdr_id']}")->getField('account');
            $this->assign('info', $dluser);
            $this->assign('tjr', $user->where(array('user_id' => $dluser['tjr_id'], 'is_type' => 1))->find());
            $level = M('level')->where(array('level_id' => $dluser['level']))->find();
            if(!$level){
                $level['color'] = '#53ffff';
            }
            $this->assign('level', $level);
            $this->assign('zuihou', $zuihou);
        }

        $this->assign('cs', intval(I('cs')) ? intval(I('cs')) : 3);
        $this->assign('branchRegion', branchRegion());

        $this->display('jdNetworkTu');
    }

    /**
     * 推荐人位置切换
     */
    public function referralSwitch() {
        if (IS_POST) {
            $post = I('post.');
            $userLogic = new \Zfuwl\Logic\UserLogic();
            $res = $userLogic->referralSwitch($post);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('referralSwitch');
        }
    }

    /**
     * 接点人位置切换
     */
    public function jdrPosSwitch()
    {
        if(IS_POST) {
            $post = I('post.');
            $userLogic = new \Zfuwl\Logic\UserLogic();
            $res = $userLogic->jdrPosSwitch($post);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('jdrPosSwitch');
        }
    }

}
