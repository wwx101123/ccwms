<?php

namespace Zfuwl\Controller;

use Zfuwl\Model\CommonModel;

class AboutController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('aboutType', aboutType());
        $this->assign('languageType', languageType());
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('type') ? $condition['type'] = I('type') : false;
            I('cn') ? $condition['cn'] = I('cn') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('About')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('About')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\AboutLogic();
            $res = $model->aboutInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('About/index'));
            }
        } else {
            $this->display('aboutInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\AboutLogic();
            $res = $model->aboutInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('About/index'));
            }
        } else {
            $this->assign('info', M('About')->where(array('id' => I('id')))->find());
            $this->display('aboutInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('About')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('About')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
