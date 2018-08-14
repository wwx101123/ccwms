<?php

/**
 *  众福网络直销系统管理软件
 * ============================================================================
 * 版权所有 2015-2027 深圳市众福网络软件有限公司，并保留所有权利。
 * 网站地址: http://www.zfuwl.com   http://www.jiafuw.com
 * 联系方式：qq:1845218096 电话：15899929162
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author:  众福团队
 * Date:2016-12-10 19:39  68
 */

namespace Zfuwl\Controller;

class LeaderController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
        $this->assign('leaderInfo', M('leader')->where("statu=1")->cache('leaderInfo')->getField('id,name_cn'));
        $this->assign('upgradeStatu', upgradeStatu());
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('leader')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('leader')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LeaderLogic();
            $res = $model->addLeaderConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Leader/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('LeaderInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LeaderLogic();
            $res = $model->addLeaderConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Leader/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('leader')->where(array('id' => I('id')))->find());
            $this->display('LeaderInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('leader')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('leader')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function upgradeLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('leader_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('leader_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('upgradeLogAjax');
            die;
        }
        $this->display('upgradeLog');
    }

    public function refuseInfo() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LeaderLogic();
            $res = $model->refuseInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Leader/upgradeLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function confirmInfo() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LeaderLogic();
            $res = $model->confirmInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Leader/upgradeLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function delUpgrade() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('leader_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyLeaderLog() {
        $db = M('leader_log');
        $dbconn = M();
        $tables = array(
            'leader_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function userLeader() {
        if (IS_AJAX) {
            $condition = array();
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            $condition['leader'] = array('egt', 1);
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
            $this->assign('regionInfo', M('region')->cache('regionInfo')->getField('id, name_cn'));
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userLeaderAjax');
            die;
        }
        $this->display('userLeader');
    }

    public function editUserLeader() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LeaderLogic();
            $res = $model->editUserLeader($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('User/formal'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('userInfo', M('users')->where(array('user_id' => I('user_id')))->find());
            $this->display('editUserLeader');
        }
    }

}
