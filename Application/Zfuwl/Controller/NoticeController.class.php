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

class NoticeController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('typeInfo', M('auth_group')->where("status=1")->cache('typeInfo')->getField('id,title'));
        $this->assign('languageType', languageType());
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('title') && $condition['title'] = array('like', '%' . trim(I('title') . '%'));
            I('type') ? $condition['type'] = I('type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('notice')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('notice')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\NoticeLogic();
            $res = $model->noticeInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Notice/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('noticeInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\NoticeLogic();
            $res = $model->noticeInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Notice/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('notice')->where(array('id' => I('id')))->find());
            $this->display('noticeInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('notice')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveTop() {
        if (IS_POST) {
            $res = M('notice')->where(array('id' => I('id')))->save(array('top' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('notice')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
