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

class TransformController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('money_id') ? $condition['money_id'] = I('money_id') : false;
            I('type_id') ? $condition['type_id'] = I('type_id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_transform')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_transform')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\TransformLogic();
            $res = $model->transFormInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Transform/index'));
            }
        } else {
            $this->display('transFormInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\TransformLogic();
            $res = $model->transFormInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Transform/index'));
            }
        } else {
            $this->assign('info', M('money_transform')->where(array('id' => I('id')))->find());
            $this->display('transFormInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('money_transform')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('money_transform')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function transformLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('money_id') ? $condition['money_id'] = I('money_id') : false;
            I('type_id') ? $condition['type_id'] = I('type_id') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_transform_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_transform_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('transformLogAjax');
            die;
        }
        $this->display('transformLog');
    }

    public function delTransformLog() {
        $where = array('id' => array('in', I('id')));
        $res = M('money_transform_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyTransformLog() {
        $db = M('money_transform_log');
        $dbconn = M();
        $tables = array(
            'money_transform_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
