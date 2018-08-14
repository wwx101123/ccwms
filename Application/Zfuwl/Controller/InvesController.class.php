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
 * Date:2016-12-10 21:30  154
 */

namespace Zfuwl\Controller;

class InvesController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('userInvesTzType', userInvesTzType()); // 投资状态
        $this->assign('userInvesStatu', userInvesStatu()); // 分红状态
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('tz_type') ? $condition['tz_type'] = I('tz_type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_invest')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_invest')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('users_invest')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function switchOut() {
        if (IS_POST) {
            $res = M('users_invest')->where(array('id' => I('id')))->save(array('out' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function switchType() {
        if (IS_POST) {
            $res = M('users_invest')->where(array('id' => I('id')))->save(array('type' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function etitFhPer() {
        if (IS_POST) {
            $data = I('post.');
            $infoId = M('users_invest')->where(array('id' => $data['id']))->save(array('per' => $data['name']));
            if ($infoId) {
                adminLogAdd('修改ID' . $data['id'] . '分红' . $data['fhper'] . '为' . $data['name']);
                $this->success('修改成功，新比例或定额为：' . $data['name']);
                exit;
            } else {
                $this->error('操作失败');
            }
        }
    }

    public function editInvest() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\InvesLogic();
            $res = $model->investInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Invest/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $info = M('users_invest')->where(array('id' => I('id')))->find();
            $this->assign('info', $info);
            $this->assign('user', M('users')->where(array('user_id' => $info['uid']))->field('account')->find());
            $this->display('editInvest');
        }
    }

    public function delInvest() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('users_invest')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyInvestLog() {
        $db = M('users_invest');
        $dbconn = M();
        $tables = array(
            'users_invest',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
