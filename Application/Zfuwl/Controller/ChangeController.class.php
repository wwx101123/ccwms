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

class ChangeController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyConfigFeeType', moneyConfigFeeType());
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('money_id') ? $condition['money_id'] = I('money_id') : false;
            I('type_id') ? $condition['type_id'] = I('type_id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_change')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_change')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\ChangeLogic();
            $res = $model->changeInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Change/index'));
            }
        } else {
            $this->display('changeInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ChangeLogic();
            $res = $model->changeInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Change/index'));
            }
        } else {
            $this->assign('info', M('money_change')->where(array('id' => I('id')))->find());
            $this->display('changeInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('money_change')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIsUpper() {
        if (IS_POST) {
            $res = M('money_change')->where(array('id' => I('id')))->save(array('is_upper' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIslower() {
        if (IS_POST) {
            $res = M('money_change')->where(array('id' => I('id')))->save(array('is_lower' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIsAbove() {
        if (IS_POST) {
            $res = M('money_change')->where(array('id' => I('id')))->save(array('is_above' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIsBelow() {
        if (IS_POST) {
            $res = M('money_change')->where(array('id' => I('id')))->save(array('is_below' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIsAgent() {
        if (IS_POST) {
            $res = M('money_change')->where(array('id' => I('id')))->save(array('is_agent' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('money_change')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function changeLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('to_account') ? $condition['to_uid'] = $res = M('users')->where(array('account' => trim(I('to_account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('money_id') ? $condition['money_id'] = I('money_id') : false;
            I('type_id') ? $condition['type_id'] = I('type_id') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_change_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_change_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $A = getArrColumn($result, 'uid');
            $B = getArrColumn($result, 'to_uid');
            $userIdArr = array_filter(array_merge($A, $B));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('changeLogAjax');
            die;
        }
        $this->display('changeLog');
    }

    public function delChangeLog() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('money_change_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyChangeLog() {
        $db = M('money_change_log');
        $dbconn = M();
        $tables = array(
            'money_change_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
