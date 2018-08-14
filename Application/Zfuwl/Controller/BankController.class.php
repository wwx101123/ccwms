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

class BankController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('is_t') ? $condition['is_t'] = I('is_t') : false;
            I('is_c') ? $condition['is_c'] = I('is_c') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('bank')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('bank')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function addBank() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BankLogic();
            $res = $model->bankInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bank/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('bankInfo');
        }
    }

    public function editBank() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BankLogic();
            $res = $model->bankInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bank/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('bank')->where(array('id' => I('id')))->find());
            $this->display('bankInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('bank')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIsc() {
        if (IS_POST) {
            $res = M('bank')->where(array('id' => I('id')))->save(array('is_c' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIst() {
        if (IS_POST) {
            $res = M('bank')->where(array('id' => I('id')))->save(array('is_t' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delBank() {
        $bankNum = M('users_bank')->where(array('opening_id' => I('id')))->count();
        if ($bankNum > 0) {
            $this->error('当前银行存在' . $bankNum . '条记录，请勿删除');
        }
        $where = array('id' => array('in', I('id')));
        $res = $row = M('bank')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
