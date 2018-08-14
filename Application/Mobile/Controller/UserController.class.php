<?php

namespace Mobile\Controller;

use Zfuwl\Logic\UserLogic;

class UserController extends CommonController {

    protected $userLogic;

    public function _initialize() {
        parent::_initialize();
        $this->assign('countryInfo', countryList());
        $this->assign('userSecurityList', userSecurityList());

        $this->userLogic = new UserLogic();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
    }

    public function test() {
      	leaderS($this->user_id);exit;
     	leaderMinus($this->user_id);
      
        // bonus12Clear(1, 2, 1000, '测试');
        // dump($res);
        // addUserBranch(10010,10000);
        // leaderClear(0, 10000, 1000, '测试');
    }

    public function userIndex() {
      	
//        judgeFt(1);
        # 系统公告
//        $this->assign('noticeIndex', M('notice')->where("statu=1")->field('id,title,content,add_time')->order('add_time desc')->limit(6)->select());
        // $this->assign('userMoney', M('users_money')->where(array('uid' => $this->user_id, 'mid' => 1))->find());
        // $this->assign('regMoney', M('users_money')->where(array('uid' => $this->user_id, 'mid' => 2))->find());
        // $this->assign('integral', M('users_money')->where(array('uid' => $this->user_id, 'mid' => 3))->find());
        // $this->assign('goodsMoney', M('users_money')->where(array('uid' => $this->user_id, 'mid' => 4))->find());
        // $this->assign('userShares', M(NUOYILIANNAME . '.shares_user')->where(array('user_id' => $this->user_id, 'shares_id' => 1, 'pt' => 2))->find());
        // $levelSj = M('level_log')->where("statu=1")->order('zf_time desc')->limit(20)->select();
        // $userIdArr = getArrColumn($levelSj, 'uid');
        // if ($userIdArr) {
        // $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
        // }
//        $cjBranchCount = M('users_branch')->where(['uid' => $this->user_id, 'is_cj' => 2])->count();
//        $this->assign('cjBranchCount', $cjBranchCount);
      	$this->assign('recovery', M('recovery_log')->where(['b_uid' => $this->user_id])->order('id desc')->find());
        $this->assign('notice_top', M('article')->where(['cat_id' => 1])->order('add_time desc')->limit(1)->find());
        $this->assign('notice', M('article')->where(['cat_id' => 1])->order('add_time desc')->limit(10)->select());
        $this->assign('news', M('article')->where(['cat_id' => 2])->order('add_time desc')->limit(10)->select());

//        $this->assign('serviceInfo', M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn'));
//        $this->assign('bonus2Total', floatval(M('bonus_log')->where(['uid' => $this->user_id, 'bonus_id' => ['in',[2,3]]])->sum('money')));
//        $this->assign('bonus2TotalYsf', floatval(M('bonus_log')->where(['uid' => $this->user_id, 'bonus_id' => ['in',[2,3]]])->sum('out_money')));
//        $this->assign('bonus2TotalDsf', floatval(M('bonus_log')->where(['uid' => $this->user_id, 'bonus_id' => ['in',[2,3]]])->field('sum(money-out_money) as daishifang')->find()['daishifang']));
//        $this->assign('serviceTotal', M('users_day')->where(array('uid' => $this->user_id))->sum('bonus_13'));
        // $this->assign('tjrTotal', M('users_day')->where(array('uid' => $this->user_id))->sum('bonus_1'));
//        $this->assign('branchCount', M('users_branch')->where(['uid' => $this->user_id, 'is_cj' => 1])->count());

        $shares_price = M('block_price')->order('id desc')->limit(7)->select();
        $timeArr = array();
        $priceArr = array();
        foreach ($shares_price as $v) {
            $time = date("m/d", $v['zf_time']);
            $timeArr[] = $time;
            $priceArr[] = $v['after_price'];
        }
        $timeArr = json_encode(array_reverse($timeArr));
        $priceArr = json_encode(array_reverse($priceArr));
        $this->assign('timeArr', $timeArr);
        $this->assign('priceArr', $priceArr);
        dayAutoPrice();
      	leaderS($this->user_id);
       	leaderMinus($this->user_id);
      	levelS($this->user_id);
		$this->assign('moneyAll', M('bonus_log')->where(['uid' => $this->user_id])->sum('money'));
      	$this->assign('releaseMoney', M('block_release_money')->where(['uid' => $this->user_id, 'status' => 1])->sum('stay_money'));
      	$this->assign('receiveMoney', M('block_release_money')->where(['uid' => $this->user_id, 'status' => 1])->sum('fh_money'));
        $this->display('userIndex');
    }

    /**
     * 修改会员基本资料
     */
    public function editData() {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->userLogic->saveUserInfo($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error($res['msg']);
            }
        } else {
            $bankList = M('bank')->where(array('statu' => 1, 'is_t' => 1))->group('name_cn')->field('name_cn,id')->select();
            $bankList = convertArrKey($bankList, 'id');
            $jsArr = array();
            $i = 0;
            foreach ($bankList as $k => $v) {
                $jsArr[$i] = array(
                    'value' => $v['id'],
                    'text' => $v['name_cn']
                );
                $i++;
            }
            $this->assign('jsStr', json_encode($jsArr));
            $this->assign('bankList', $bankList);
            $this->assign('userBank', D("UserView")->where(array('user_id' => $this->user_id))->field('qq_name,nickname,number')->find());
            $this->display('editData');
        }
    }

    /**
     * 个人资料
     */
    public function lookData() {
        $this->assign('userBank', D("UserView")->where(array('user_id' => $this->user_id))->field('username,account,level,qq_name,number')->find());
        $this->display('lookData');
    }

    /**
     * 修改会员登录密码
     */
    public function editPassword() {
        if (IS_POST) {
            $user = $this->user;
            $data = I('post.');
            $res = smsCodeVerify($user['account'], $data['mobileCode'], session_id());
            if ($res['status'] != 1) {
                $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
            }
            $res = $this->userLogic->webEditPass($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('User/editPassword'));
                exit;
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('editPassword');
        }
    }

    /**
     * 修改会员二级密码
     */
    public function editSecpwd() {
        if (IS_POST) {
            $user = $this->user;
            $data = I('post.');
            $res = smsCodeVerify($user['account'], $data['mobileCode'], session_id());
            if ($res['status'] != 1) {
                $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
            }
            $res = $this->userLogic->webEditSecpwd($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('User/editSecpwd'));
                exit;
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('editSecpwd');
        }
    }

    /**
     * 修改会员密保问题
     */
    public function editSecurity() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editSecurity($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $where = array(
                'uid' => $this->user_id
            );
            $this->assign('security', M('users_security')->where($where)->find());
            $this->assign('userSecurityList', userSecurityList());
            $this->display('editSecurity');
        }
    }

    /**
     * 安全设置
     */
    public function securityInfo() {
        $this->assign('dataInfo', D("UserView")->where(array('user_id' => $this->user_id))->field('account,bank_account,wx_name')->find());
        $this->display('securityInfo');
    }

    /**
     * 修改会员头像
     */
    public function saveUserHeadImg() {
        if (IS_POST) {
            $src = I('post.src');
            $data = array(
                'head' => $src
            );
            $res = $this->userLogic->saveUserInfo($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        }
    }

    /**
     * 转账管理
     */
    public function changeInfo() {
        $this->assign('changelist', M('money_change')->where(array('statu' => 1))->select());
        $this->assign('transformlist', M('money_transform')->where(array('statu' => 1))->select());
        $this->display('changeInfo');
    }

    /**
     * 会员打款升级
     */
    public function upgrade() {

        $user = $this->user;
        if ($user['is_jh'] != 1) {
            $this->redirect('User/playMoneyByTjr');
            die;
        }

        if (IS_POST) {

            $post = I('post.');

            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->upgrade($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {

            $levelInfo = M('level')->where(array('level_id' => array('gt', $user['level'])))->order('level_id asc')->field('level_id, name_cn, amount')->find();
            $this->assign('level', $levelInfo);

            # 接点会员
            $jdrId = getJdrPrevId($this->user_id, (($levelInfo['level_id'] - 1) <= 0 ? 1 : ($levelInfo['level_id'] - 1)));
            if ($jdrId == $this->user_id) {
                $this->error('你不能升级');
            }


            $jdrUser = D("UserView")->where(array('user_id' => $jdrId))->field('level,mobile,username,bank_address,opening_id,bank_account,bank_name,zfb_name,wx_name,ylh_name,yft_name,yhy_name')->find();
            $this->assign('jdrUser', $jdrUser);

            $this->assign('bankOpeningList', M('bank')->where("is_t=1 and statu=1")->cache('bankOpeningList')->getField('id,name_cn'));

            $user = $this->user;
            if ($user['level'] == 11) {
                $this->error('你己是最高级别，无需升级');
            }
            $this->display('upgrade');
        }
    }

    /**
     * 升级日志
     */
    public function levelLogList() {
        $condition['uid'] = $this->user_id;
        $levelLogList = M('level_log')->where($condition)->select();
        $this->assign('list', $levelLogList);

        $this->assign('levelList', M('level')->getField('level_id, name_cn'));

        $this->display('levelLogList');
    }

    /**
     * 会员申请使用奖金激活
     */
    public function upJjSy() {
        $user = $this->user;
        if ($user['jiangjin_jihuo_status'] == 1) {
            $this->error('无需重复申请');
        }
        if ($user['jiangjin_jihuo_status'] == 3) {
            $this->error("审核中");
        }
        $res = M('users')->where(['user_id' => $user['user_id']])->save(['jiangjin_jihuo_status' => 3]);
        if ($res) {
            $this->success('申请成功');
        } else {
            $this->error('申请失败');
        }
        die;
    }

    /**
     * 会员公排列表
     */
    public function branchList() {

        $list = M('users_branch')->where(['uid' => $this->user_id])->select();
        foreach ($list as &$v) {
            $v['xs_time'] = ($v['add_time'] + zfCache('regInfo.cj_sj') * 3600 - $v['js_time']) - (time() - $v['add_time']) - time();
            $v['hour'] = intval($v['xs_time'] / 3600) < 10 ? '0' . intval($v['xs_time'] / 3600) : intval($v['xs_time'] / 3600);
            $v['minute'] = intval(($v['xs_time'] - $v['hour'] * 3600) / 60) < 10 ? '0' . intval(($v['xs_time'] - $v['hour'] * 3600) / 60) : intval(($v['xs_time'] - $v['hour'] * 3600) / 60);
            $v['second'] = $v['xs_time'] - ($v['hour'] * 3600) - ($v['minute'] * 60) < 10 ? '0' . $v['xs_time'] - ($v['hour'] * 3600) - ($v['minute'] * 60) : $v['xs_time'] - ($v['hour'] * 3600) - ($v['minute'] * 60);
        }

        $this->assign('list', $list);

        $this->display('branchList');
    }

    /**
     * 会员申请复投
     */
    public function recast() {
        $mid = 3;

        $levelInfo = M('level')->where(['level_id' => $this->user['level']])->find();
        $levelInfo['out_reg'] = floatval($levelInfo['out_reg']);
        if (IS_POST) {

            $post = I('post.');
            $model = M('users_recast_log');

            if ($post['secpwd'] == '') {
                $this->error('请输入二级密码');
            }
            if (webEncrypt($post['secpwd']) != $this->user['secpwd']) {
                $this->error('二级密码验证失败');
            }

            $branchCount = M('users_branch')->where(['uid' => $this->user_id, 'is_cj' => 1])->count();
            if ($branchCount) {
                $this->error('暂时不能申请复投');
            }

            $count = $model->where(['uid' => $this->user_id, 'status' => 2])->count();
            if ($count > 0) {
                $this->error('你还有申请在处理中');
            }
            $res = true;
            if ($levelInfo['out_reg'] > 0) {
                if (usersMoney($this->user_id, $mid) < $levelInfo['out_reg']) {
                    $this->error(moneyList($mid) . '余额不足');
                }

                $res = userMoneyLogAdd($this->user_id, $mid, '-' . $levelInfo['out_reg'], '118', '申请复投');
            }
            if ($res) {
                $data = [
                    'uid' => $this->user_id,
                    'mid' => $mid,
                    'money' => $levelInfo['out_reg'],
                    'add_time' => time(),
                ];
                $model->data($data)->add();
                $this->success('申请成功');
            } else {
                $this->error('申请失败');
            }
        } else {

            $this->assign('levelInfo', $levelInfo);

            $this->display('recast');
        }
    }

    /**
     * 会员申请复投记录
     */
    public function recastLogList() {
        $model = M('users_recast_log');

        $where = [
            'uid' => $this->user_id
        ];

        $list = $model->where($where)->order('id desc')->select();

        $this->assign('list', $list);

        $this->display('recastLogList');
    }

}
