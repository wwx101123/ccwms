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

class CarryController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyCarryTk', moneyCarryTk());
        $this->assign('moneyCarryLogStatu', moneyCarryLogStatu());
        $this->assign('moneyCarryInfo', M('money')->where(array('statu' => 1, 'is_t' => 1))->cache('moneyCarryInfo')->getField('money_id,name_cn'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
        $this->assign('bankInfo', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('level_id') ? $condition['level_id'] = I('level_id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_carry')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_carry')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\CarryLogic();
            $res = $model->carryInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Carry/index'));
            }
        } else {
            $this->display('carryInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\CarryLogic();
            $res = $model->carryInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Carry/index'));
            }
        } else {
            $this->assign('info', M('money_carry')->where(array('id' => I('id')))->find());
            $this->display('carryInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('money_carry')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('money_carry')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function carryLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_carry_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_carry_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('carryLogAjax');
            die;
        }
        $this->display('carryLog');
    }

    public function affirmCarryAdd() {
        $info = M('money_carry_log')->where(array('id' => $_POST['id']))->find();
        if (!$info || $info['statu'] > 2) {
            $this->error('网络失败，请刷新页面后重试');
        }
        if ($info['statu'] <= 2) {
            $res = M('money_carry_log')->where(array('id' => $info['id']))->save(array('statu' => 9, 'pay_time' => time(), 'affirm_time' => time(), 'admin_id' => session('admin_id')));
            if ($res) {
                $this->success('确认成功');
            } else {
                $this->error('操作失败，请刷新页面后重试');
            }
        }
    }

    public function toCarryAdd() {
        if ($_POST) {
            $res = M('money_carry_log')->where(array('id' => $_POST['id']))->save(array('statu' => 2, 'affirm_time' => time(), 'admin_id' => session('admin_id')));
            if ($res) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败，请刷新页面后重试');
            }
        }
    }

    public function refuseCarryAdd() {
        if ($_POST) {
            $info = M('money_carry_log')->where(array('id' => $_POST['id']))->find();
            if ($info['statu'] == 1) {
                $model = new \Think\Model();
                $model->startTrans();
                $res = M('money_carry_log')->where(array('id' => $info['id']))->save(array('statu' => 3, 'refuse_time' => time(), 'refuse' => $_POST['name'], 'admin_id' => session('admin_id')));
                $info = userMoneyLogAdd($info['uid'], $info['mid'], $info['add_money'], 104, $_POST['name'], session('admin_id'));
                if ($res && $info) {
                    $model->commit();
                    $this->success('确认成功');
                } else {
                    $model->rollback();
                    $this->error('操作失败，请刷新页面后重试');
                }
            } else {
                $this->error('网络失败，请刷新页面后重试');
            }
        }
    }

    public function delCarryLog() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('money_carry_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyCarryLog() {
        $db = M('money_carry_log');
        $dbconn = M();
        $tables = array(
            'money_carry_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function addCarrylist() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\CarryLogic();
            $res = $model->listCarryAdd($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Carry/index'));
            }
        } else {
            $this->display('addCarrylist');
        }
    }

}
