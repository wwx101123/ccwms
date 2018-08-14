<?php

namespace Zfuwl\Controller;

use Think\Verify;

class UserController extends CommonController {

    protected $userModel;

    public function _initialize() {
        parent::_initialize();
        $this->userModel = D('Users');
        $this->assign('agentInfo', M('agent')->where("statu=1")->cache('agentInfo')->getField('id,name_cn'));
        $this->assign('leaderInfo', M('leader')->where("statu=1")->cache('leaderInfo')->getField('id,name_cn'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
        $this->assign('serviceInfo', M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn'));
//        $this->assign('bankInfo', M('bank')->where("is_t=1")->cache('bankInfo')->getField('id,name_cn'));
        $this->assign('countryInfo', countryList());

        $this->assign('sexType', sexType());
    }

    /**
     * 会员列表
     */
    public function index() {
        if (IS_AJAX) {
            $condition = array();
            if (I('type') == 1) {
                I('account') && $condition['user_id'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 2) {
                I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 3) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('mobile' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 4) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('email' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 5) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('username' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 6) {
                I('account') && $condition['nickname'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 7) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('number' => trim(I('account'))))->getField('id') : false;
            }
            I('level') && $condition['level'] = I('level');
            I('leader') && $condition['leader'] = I('leader');
            I('agent') && $condition['agent'] = I('agent');
            I('service') && $condition['service'] = I('service');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $count = M('users')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $tjrIdArr = getArrColumn($result, 'tjr_id');
            $bdrIdArr = getArrColumn($result, 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('tjrList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('regionInfo', M('region')->cache('regionInfo')->getField('id, name_cn'));
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    /**
     * 新增会员列表
     */
    public function newsUser() {
        if (IS_AJAX) {
            $today = strtotime(date('Y-m-d', time()));
            $condition = array();
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            $condition['reg_time'] = array('egt', $today);
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $result = $this->userModel->selectAllUser($condition, $sort_order); // 查询会员数据
            $levelList = D('UsersLevel')->selectAllUsersLevelList('', array('level_id', 'name_cn')); // 查询会员等级信息
            $levels = recombinantArr($levelList, 'level_id', 'name_cn'); // 以等级做为键 名字做为值
            $tjrIdArr = getArrColumn($result['list'], 'tjr_id');
            $bdrIdArr = getArrColumn($result['list'], 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('tjrList', $this->userModel->selectUsers("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true));
            }
            $this->assign('page', $result['page']);
            $this->assign('regionInfo', M('region')->cache('regionInfo')->getField('id, name_cn'));
            $this->assign('list', $result['list']);
            $this->assign('levels', $levels);
            $this->display('newsUserAjax');
            die;
        }
        $this->display('newsUser');
    }

    /**
     * 未审会员列表
     */
    public function trial() {
        if (IS_AJAX) {
            $condition = array();
            $condition['activate'] = 2;
            if (I('type') == 1) {
                I('account') && $condition['user_id'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 2) {
                I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 3) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('mobile' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 4) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('email' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 5) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('username' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 6) {
                I('account') && $condition['nickname'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 7) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('number' => trim(I('account'))))->getField('id') : false;
            }
            I('level') && $condition['level'] = I('level');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $count = M('users')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $tjrIdArr = getArrColumn($result, 'tjr_id');
            $bdrIdArr = getArrColumn($result, 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('tjrList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('regionInfo', M('region')->cache('regionInfo')->getField('id, name_cn'));
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('trialAjax');
            die;
        }
        $this->display('trial');
    }

    /**
     * 己审会员列表
     */
    public function formal() {
        if (IS_AJAX) {
            $condition = array();
            $condition['activate'] = 1;
            if (I('type') == 1) {
                I('account') && $condition['user_id'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 2) {
                I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 3) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('mobile' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 4) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('email' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 5) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('username' => trim(I('account'))))->getField('id') : false;
            } elseif (I('type') == 6) {
                I('account') && $condition['nickname'] = array('like', '%' . trim(I('account') . '%'));
            } elseif (I('type') == 7) {
                I('account') ? $condition['data_id'] = $res = M('users_data')->where(array('number' => trim(I('account'))))->getField('id') : false;
            }
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['jh_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $count = M('users')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $tjrIdArr = getArrColumn($result, 'tjr_id');
            $bdrIdArr = getArrColumn($result, 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('userlist', $this->userModel->selectUsers("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('formalAjax');
            die;
        }
        $this->display('formal');
    }

    /**
     * 冻结会员列表
     */
    public function lock() {
        if (IS_AJAX) {
            $condition = array();
            $condition['frozen'] = 2;
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $result = $this->userModel->selectAllUser($condition, $sort_order); // 查询会员数据
            foreach ($result['list'] as &$v) {
                $user = M('users_lock')->where(array('user_id' => $v['user_id']))->order('lock_time desc')->limit('1')->find();
                $v['lock_time'] = $user['lock_time'];
                $v['lock_info'] = $user['log_info'];
                $v['lock_id'] = $user['log_id'];
            }
            $levelList = D('UsersLevel')->selectAllUsersLevelList('', array('level_id', 'name_cn')); // 查询会员等级信息
            $levels = recombinantArr($levelList, 'level_id', 'name_cn'); // 以等级做为键 名字做为值
            $tjrIdArr = getArrColumn($result['list'], 'tjr_id');
            $bdrIdArr = getArrColumn($result['list'], 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('tjrList', $this->userModel->selectUsers("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true));
            }
            $this->assign('page', $result['page']);
            $this->assign('userList', $result['list']);
            $this->assign('levels', $levels);
            $this->display('lockAjax');
            die;
        }
        $this->display('lock');
    }

    /**
     * 空单会员列表
     */
    public function emptyIndex() {
        if (IS_AJAX) {
            $condition = array();
            $condition['user'] = 2;
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $result = $this->userModel->selectAllUser($condition, $sort_order); // 查询会员数据
            $levelList = D('UsersLevel')->selectAllUsersLevelList('', array('level_id', 'name_cn')); // 查询会员等级信息
            $levels = recombinantArr($levelList, 'level_id', 'name_cn'); // 以等级做为键 名字做为值
            $tjrIdArr = getArrColumn($result['list'], 'tjr_id');
            $bdrIdArr = getArrColumn($result['list'], 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('tjrList', $this->userModel->selectUsers("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true));
            }
            $this->assign('page', $result['page']);
            $this->assign('list', $result['list']);
            $this->assign('levels', $levels);
            $this->display('emptyIndexAjax');
            die;
        }
        $this->display('emptyIndex');
    }

    /**
     * 回填单会员列表 表示先免费激活  有收入时 在扣除部分收入做为投资金额
     */
    public function backfill() {
        if (IS_AJAX) {
            $condition = array();
            $condition['user'] = 3;
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $result = $this->userModel->selectAllUser($condition, $sort_order); // 查询会员数据
            $levelList = D('UsersLevel')->selectAllUsersLevelList('', array('level_id', 'name_cn')); // 查询会员等级信息
            $levels = recombinantArr($levelList, 'level_id', 'name_cn'); // 以等级做为键 名字做为值
            $tjrIdArr = getArrColumn($result['list'], 'tjr_id');
            $bdrIdArr = getArrColumn($result['list'], 'bdr_id');
            $userIdArr = array_filter(array_merge($tjrIdArr, $bdrIdArr));
            if ($userIdArr) {
                $this->assign('tjrList', $this->userModel->selectUsers("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true));
            }
            $this->assign('page', $result['page']);
            $this->assign('list', $result['list']);
            $this->assign('levels', $levels);
            $this->display('backfillAjax');
            die;
        }
        $this->display('backfill');
    }

    /**
     * 添加会员
     */
    public function addUser() {
        $this->assign('levelList', M('level')->where("statu=1")->cache('levelList')->select());
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\RegLogic();
            $res = $model->addUser($data);
            if ($res['status'] == 1) {
                $this->success('注册成功', U('User/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
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
            $bankList = M('bank')->where(array('statu' => 1, 'is_t' => 1))->getField('id,name_cn');

            $tjrAccount = I('tjr');
            $jdrAccount = I('jdr');
            $pos = I('pos');

            $this->assign('tjrAccount', $tjrAccount);
            $this->assign('jdrAccount', $jdrAccount);
            $this->assign('pos', $pos);

            $this->assign('bankList', $bankList);
            $this->display('addUser');
        }
    }

    /**
     * 手机认证状态
     */
    public function saveIsMobile() {
        $data = dataInfo(I('id'));
        $val = $data['is_mobile'] == 1 ? 2 : 1;
        $res = M('users_data')->where(array('id' => I('id')))->save(array('is_mobile' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 身份证认证状态
     */
    public function saveIsNumber() {
        $data = dataInfo(I('id'));
        $val = $data['is_number'] == 1 ? 2 : 1;
        $res = M('users_data')->where(array('id' => I('id')))->save(array('is_number' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 邮箱认证状态
     */
    public function saveIsEmail() {
        $data = dataInfo(I('id'));
        $val = $data['is_email'] == 1 ? 2 : 1;
        $res = M('users_data')->where(array('id' => I('id')))->save(array('is_email' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 动态收入开关
     */
    public function editIsTrends() {
        $data = M('users')->where(array('user_id' => I('id')))->field('trends')->find();
        $val = $data['trends'] == 1 ? 2 : 1;
        $res = M('users')->where(array('user_id' => I('id')))->save(array('trends' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 静态收入变动
     */
    public function editIsStatic() {
        $data = M('users')->where(array('user_id' => I('id')))->field('static')->find();
        $val = $data['static'] == 1 ? 2 : 1;
        $res = M('users')->where(array('user_id' => I('id')))->save(array('static' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 提现状态变动
     */
    public function editIsTk() {
        $data = M('users')->where(array('user_id' => I('id')))->field('tk')->find();
        $val = $data['tk'] == 1 ? 2 : 1;
        $res = M('users')->where(array('user_id' => I('id')))->save(array('tk' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 充值状态变动
     */
    public function editIsPay() {
        $data = M('users')->where(array('user_id' => I('id')))->field('pay')->find();
        $val = $data['pay'] == 1 ? 2 : 1;
        $res = M('users')->where(array('user_id' => I('id')))->save(array('pay' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 会员收款 等级参数考核 的权与利
     */
    public function savePower() {
        $data = M('users')->where(array('user_id' => I('id')))->field('power')->find();
        $val = $data['power'] == 1 ? 2 : 1;
        $res = M('users')->where(array('user_id' => I('id')))->save(array('power' => $val));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 回填单转实单
     */
    public function editIsUserReal() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editIsUserReal($data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('users')->where(array('user_id' => I('id')))->find());
            $this->display('editIsUserReal');
        }
    }

    /**
     * 回填单转实单
     */
    public function editIsUserEmpty() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editIsUserEmpty($data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('users')->where(array('user_id' => I('id')))->find());
            $this->display('editIsUserEmpty');
        }
    }

    /**
     * 删除未审核的会员
     */
    public function delWeUsers() {
        $userLogic = new \Zfuwl\Logic\UserLogic();
        $data = I('post.');
        $res = $userLogic->delWeUsers($data['id']); // 执行删除
        if ($res['status'] == 1) {
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }
	
  	/**
     * 删除的会员
     */
    public function delUsers() {
        $model = new \Zfuwl\Logic\UserLogic();
        $data = I('post.');
        $res = $model->delUsers($data['id']); // 执行删除
        if ($res['status'] == 1) {
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }
  
    public function editPassword() {
        if (IS_POST) {
            $data = I('post.');
            $infoId = M('users')->where(array('user_id' => $data['id']))->save(array('password' => webEncrypt($data['name'])));
            if ($infoId) {
                $this->success('修改成功，新登录密码为：' . $data['name']);
                exit;
            } else {
                $this->error('操作失败，当前登录密码为：' . $data['name']);
            }
        }
    }

    public function editSecpwd() {
        if (IS_POST) {
            $data = I('post.');
            $infoId = M('users')->where(array('user_id' => $data['id']))->save(array('secpwd' => webEncrypt($data['name'])));
            if ($infoId) {
                $this->success('修改成功，新二级密码为：' . $data['name']);
                exit;
            } else {
                $this->error('操作失败，当前二级密码为：' . $data['name']);
            }
        }
    }

    /**
     * 修改会员密保问题
     */
    public function editSecurity() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editSecurity($data, $data['uid']);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $userInfo = M('users')->where(array('user_id' => I('id')))->find();
            $this->assign('securitylist', M('users_security')->where(array('uid' => $userInfo['user_id']))->find());
            $this->assign('userInfo', $userInfo);
            $this->assign('userSecurityList', userSecurityList());
            $this->display('editSecurity');
        }
    }

    /**
     * 冻结会员
     */
    public function addFrozen() {
        $user_id = I('id');
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->addFrozen($data);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        }
    }

    /**
     * 释放冻结的会员
     */
    public function releaseFrozen() {
        $user_id = I('id');
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->releaseFrozen($data);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $userInfo = M('users')->where(array('user_id' => I('id')))->field('user_id,account,level')->find();
            $lockinfo = M('users_lock')->where(array('uid' => $userInfo['user_id'], 'statu' => 2))->find();
            $this->assign('lockinfo', $lockinfo);
            $this->assign('userInfo', $userInfo);
            $this->display('releaseFrozen');
        }
    }

    /**
     * 修改会员收款信息
     */
    public function editBank() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editBank($data, $data['user_id']);
            if ($res['status'] == 1) {
                adminLogAdd('修改 会员的收款信息 id: ' . $data['user_id']);
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('bankList', D('UserView')->where(array('user_id' => I('id')))->field('user_id,account,nickname,username,wx_name,zfb_name,zfb_code,wx_code,opening_id,bank_address,bank_account,bank_name')->find());
            $this->assign('bankOpeningList', M('bank')->where("is_t=1 and statu=1")->cache('bankOpeningList')->getField('id,name_cn'));
            $this->display('editBank');
        }
    }

    /**
     * 修改会员基本资料
     */
    public function editData() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editData($data, $data['user_id']);
            if ($res['status'] == 1) {
                adminLogAdd('修改 会员的基本资料 id: ' . $data['user_id']);
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $userInfo = M('users')->where(array('user_id' => I('id')))->find();
            $userDataInfo = M('users_data')->where(array('id' => $userInfo['data_id']))->find();
            $this->assign('userDataInfo', M('users_data')->where(array('id' => $userInfo['data_id']))->find());
            $this->province = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
            $this->city = M('region')->where(array('parent_id' => $userDataInfo['province'], 'level' => 2))->select();
            $this->district = M('region')->where(array('parent_id' => $userDataInfo['city'], 'level' => 3))->select();
            $this->twon = M('region')->where(array('parent_id' => $userDataInfo['district'], 'level' => 4))->select();
            $this->assign('info', $userInfo);
            $this->assign('userDataInfo', $userDataInfo);
            $this->display('editData');
        }
    }

    /**
     * 登录会员中心
     */
    public function userLogin() {
        $user = M('users')->where('user_id=' . I('id'))->find();
        session('user', $user);
        setcookie('user_id', I('id'), null, '/');
        session('web_past_due_time', time());
        adminLogAdd('登录会员' . $user['account'] . '账户中心');
        echo "<script>window.location.href='/';</script>";
    }

    /**
     * 发送短信
     */
    public function sendSms() {
        if (IS_POST) {
            $userIdArr = I('post.user_id');
            $message = I('post.text');
            if (!empty($userIdArr)) {
                $userIdArr = implode(',', $userIdArr);
                $users = D("UserView")->where(array('user_id' => array('IN', $userIdArr)))->field('mobile,user_id')->select();
//                $users = $this->userModel->selectUsers(array('user_id' => array('IN', $userIdArr)), array('mobile'));
                $to = '';
                foreach ($users as $v) {
                    if (checkMobile($v['mobile'])) {
                        $to .= $v['mobile'] . ',';
                    }
                }
                $res = sendSms($to, $message);
                if ($res['status'] == 1) {
                    $this->success('发送成功!');
                } else {
                    $this->error('发送失败!');
                }
            } else {
                $this->error('没有会员信息!');
            }
        } else {
            $userIdArr = I('get.user_id');
            $users = array();
            if (!empty($userIdArr)) {
                $userWhere = array(
                    'user_id' => array('IN', $userIdArr),
                    'mobile' => array('neq', '')
                );
                $users = D("UserView")->where($userWhere)->field('mobile,user_id')->select();
//                $users = $this->userModel->selectUsers($userWhere);
                foreach ($users as $k => $v) {
                    if (!checkMobile($v['mobile'])) {
                        unset($users[$k]);
                    }
                }
            }
            $this->assign('users', $users);
            $this->display('sendSms');
        }
    }

    /**
     * 发送邮件
     */
    public function sendEmail() {
        if (IS_POST) {
            $userIdArr = I('post.user_id');
            $title = I('post.title');
            $theme = I('post.theme');
            $content = I('post.content');
            if (!empty($userIdArr)) {
                $userIdArr = implode(',', $userIdArr);
                $users = D("UserView")->where(array('user_id' => array('IN', $userIdArr)))->field('email,username')->select();
//                $users = $this->userModel->selectUsers(array('user_id' => array('IN', $userIdArr)), array('email,username'));
                $to = array();
                foreach ($users as $v) {
                    if (checkEmail($v['email'])) {
//                        $to[] = $v['email'];
                        $data = array(
                            'name' => $v['email'],
                            'zf_time' => time(),
                            'content' => $content,
                            'is_type' => 2,
                            'is_class' => 2,
                            'session_id' => session_id()
                        );
                        $smsRes = M('sms_log')->add($data);
                        $res = sendMail($v['email'], $v['username'], $title, $theme, $content);
                        if ($res) {
                            M('sms_log')->where("id = {$smsRes}")->save(array('is_type' => 1));
                        }
                    }
                }
                if ($res) {
                    $this->success('发送成功!');
                } else {
                    $this->error('发送失败!');
                }
            } else {
                $this->error('没有会员信息!');
            }
        } else {
            $userIdArr = I('get.user_id');
            $users = array();
            if (!empty($userIdArr)) {
                $userWhere = array(
                    'user_id' => array('IN', $userIdArr)
                );
                $users = D("UserView")->where($userWhere)->field('email,user_id')->select();
                foreach ($users as $k => $v) {
                    if (!checkEmail($v['email'])) {
                        unset($users[$k]);
                    }
                }
            }
            $this->assign('users', $users);
            $this->display('sendEmail');
        }
    }

    /**
     * 发送内部邮件
     */
    public function sendMessage() {
        if (IS_POST) {
            $userIdArr = I('post.user_id');
            $type = I('is_type');
            $content = I('post.content');
            if (!$content) {
                $this->error('请输入发送内容!');
            }
            if ($type == 2) {
                $userIdArr = implode(',', $userIdArr);
            } elseif ($type == 1) {
                $userWhere = array(
                    'tjr_id' => array('neq', 0)
                );
                $users = D("UserView")->where(array('user_id' => array('IN', $userIdArr)))->getField('account,user_id');
                $userIdArr = implode(',', $users);
            }
            if ($userIdArr) {
                $messageData = array(
                    'admin_id' => session('admin_id'),
                    'message' => $content,
                    'send_user_id' => $userIdArr,
                    'send_time' => time()
                );
                $res = M('web_message')->add($messageData);
                if ($res) {
                    $this->success('发送成功!');
                } else {
                    $this->error('发送失败!');
                }
            } else {
                $this->error('没有会员信息!');
            }
        } else {
            $userIdArr = I('get.user_id');
            $users = array();
            if (!empty($userIdArr)) {
                $userWhere = array(
                    'user_id' => array('IN', $userIdArr)
                );
                $users = $this->userModel->selectUsers($userWhere);
            }
            $this->assign('users', $users);
            $this->display('sendMessage');
        }
    }

    /**
     * 修改会员业绩
     */
    public function editUserBranch() {

        $id = I('id', '', 'intval');
        $where = array(
            'user_id' => $id
        );
        $info = M('users_branch')->where($where)->field('left_total, left_new, right_total, right_new')->find();

        $user = M('users')->where($where)->field('account, user_id')->find();
        $info = array_merge($user, $info); // 合并双轨业绩和会员数据
        if (!$info) {
            $this->error('未找到会员数据!');
        }

        if (IS_POST) {
            $post = I('post.');

            $leftTotal = floatval($post['leftTotal']);
            $leftNew = floatval($post['leftNew']);
            $rightTotal = floatval($post['rightTotal']);
            $rightNew = floatval($post['rightNew']);

            if ($leftTotal != $info['left_total'] || $leftNew != $info['left_new'] || $rightTotal != $info['right_total'] || $rightNew != $info['right_new']) {
                $data = array(
                    'left_total' => $leftTotal,
                    'left_new' => $leftNew,
                    'right_total' => $rightTotal,
                    'right_new' => $rightNew
                );
                $res = M("users_branch")->where($where)->save($data);
                if ($res) {
                    // 添加管理员操作日志
                    adminLogAdd('修改会员' . $info['account'] . '的业绩！' . '左区总业绩：' . $info['left_total'] . ' => ' . $leftTotal . '、左区新增业绩：' . $info['left_new'] . ' => ' . $leftNew . '、右区总业绩：' . $info['right_total'] . ' => ' . $rightTotal . '、右区新增业绩：' . $info['right_new'] . ' => ' . $rightNew);
                    $this->success('操作成功!');
                } else {
                    $this->error('操作失败!');
                }
            } else {
                $this->error('没有修改内容!');
            }
        } else {

            $this->assign('info', $info);

            $this->display("editUserBranch");
        }
    }

    public function actionIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('log_ip') && $condition['log_ip'] = array('like', '%' . trim(I('log_ip') . '%'));
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_action')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_action')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('actionIndexAjax');
            die;
        }
        $this->display('actionIndex');
    }

    public function delAction() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('users_action')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyActionLog() {
        $db = M('users_action');
        $dbconn = M();
        $tables = array(
            'users_action',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    /**
     * 根据ip获取地址
     */
    public function checkAddressByIp() {
        $ip = I('ip');
        $this->success('ip: ' . $ip . '     地址: ' . getcposition($ip));
    }

    public function locklog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('log_info') && $condition['log_info'] = array('like', '%' . trim(I('log_info') . '%'));
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['lock_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_lock')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_lock')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('locklogAjax');
            die;
        }
        $this->display('locklog');
    }

    public function dellocklog() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('users_lock')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptylockUserlog() {
        $db = M('users_lock');
        $dbconn = M();
        $tables = array(
            'users_lock',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    /**
     * 修改会员信誉
     */
    public function editXinyu() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editXinyu($data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 修改推荐人账号
     */
    public function editUserTjr() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editUserTjr($data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 修改报单人账号
     */
    public function editUserBdr() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editUserBdr($data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 出局会员列表
     */
    public function cjUserList()
    {
        if(IS_AJAX) {
            $condition = [
                'is_cj' => 2
            ];
            $model = M('users_branch');

            $count = $model->where($condition)->count();
            $page = ajaxGetPage($count, 10);
            $list = $model->where($condition)->limit($page->firstRow.','.$page->listRows)->order('branch_id desc')->select();

            $userIdArr = getArrColumn($list, 'uid');
            $userIdArr && $this->assign('userList', M('users')->where(['user_id' => ['in', $userIdArr]])->getField('user_id,account'));
            $this->assign('page', $page->show());
            $this->assign('list', $list);

            $this->display('cjUserListAjax');
        } else {
            $this->display('cjUserList');
        }
    }

    /**
     * 复投申请列表
     */
    public function recastLogList()
    {
        if(IS_AJAX) {
            $condition = [];
            I('account') && $condition['uid'] = M('users')->where(['account' => I('account')])->getField('user_id');
            $model = M('users_recast_log');

            $count = $model->where($condition)->count();
            $page = ajaxGetPage($count, 10);

            $list = $model->where($condition)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();

            $userIdArr = getArrColumn($list, 'uid');
            $userIdArr && $this->assign('userList', M('users')->where(['user_id' => ['in', $userIdArr]])->getField('user_id,account'));

            $this->assign('page', $page->show());
            $this->assign('list', $list);

            $this->display('recastLogListAjax');
        } else {
            $this->display('recastLogList');
        }
    }

    /**
     * 拒绝会员复投
     */
    public function refuseRecast()
    {
        if(IS_POST) {
            $post = I('post.');

            $id = intval($post['id']);
            $refuse = $post['refuse'];

            if($refuse == '') {
                $this->error('请输入拒绝理由');
            }

            $model = M('users_recast_log');

            $info = $model->where(['id' => $id])->find();
            if(!$info) {
                $this->error('未获取到数据');
            }
            if($info['status'] != 2) {
                $this->error('该数据已处理过了');
            }

            $data = [
                'status' => 3,
                'refuse_time' => time(),
                'refuse' => $refuse
            ];
            $res = $model->where(['id' => $info['id']])->save($data);
            if($res) {
                userMoneyLogAdd($info['uid'], $info['mid'], floatval($info['money']), '118', '拒绝申请复投退回');
                $this->success('拒绝成功');
            } else {
                $this->error('拒绝失败');
            }
        }
    }

    /**
     * 确认会员复投
     */
    public function confirmRecast()
    {
        if(IS_POST) {
            $post = I('post.');

            $id = intval($post['id']);

            $model = M('users_recast_log');

            $info = $model->where(['id' => $id])->find();
            if(!$info) {
                $this->error('未获取到数据');
            }
            if($info['status'] != 2) {
                $this->error('该数据已处理过了');
            }

            $data = [
                'status' => 1,
                'confirm_time' => time()
            ];
            $res = $model->where(['id' => $info['id']])->save($data);
            if($res) {
                $userInfo = M('users')->where(['user_id' => $info['uid']])->field('user_id,tjr_id,account')->find();
                addUserBranch($userInfo['user_id'], $userInfo['tjr_id']);
                $branchInfo = M('users_branch')->where(['uid' => $userInfo['user_id']])->order('branch_id desc')->field('branch_id,jdr_id')->find();
                bonus2Clear($branchInfo['jdr_id'], $userInfo['user_id'], $info['money'], $userInfo['account'].'复投');
                $this->success('确认成功');
            } else {
                $this->error('确认失败');
            }
        }
    }
	
  	/**
     * 管理员手动修改会员的投资金额
     */
    public function editInvestMoney() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\UserLogic();
            $res = $model->editInvestMoney($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Block/crowdIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

}
