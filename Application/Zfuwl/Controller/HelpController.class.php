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

class HelpController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('helpCatInfo', M('help_cat')->where("statu=1")->cache('helpCatInfo')->getField('cat_id,title'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('title') && $condition['title'] = array('like', '%' . trim(I('title') . '%'));
            I('cat_id') ? $condition['cat_id'] = I('cat_id') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('help')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('help')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\HelpLogic();
            $res = $model->helpInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Help/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('helpInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\HelpLogic();
            $res = $model->helpInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Help/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('help')->where(array('id' => I('id')))->find());
            $this->display('helpInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('help')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('help')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
