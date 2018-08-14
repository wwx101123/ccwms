<?php

namespace Mobile\Controller;

class NoticeController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('typeInfo', M('auth_group')->where("status=1")->cache('typeInfo')->getField('id,title'));
    }

    public function index() {
        $this->display('index');
    }

    public function indexAjax() {
        $condition = array();
        $condition['statu'] = 1;
        $count = M('notice')->where($condition)->count();
        $p = I('p') > 0 ? I('p') : 0;
        $pSize = 10;
        $result = M('notice')->where($condition)->order('sort desc')->limit(($p * $pSize) . ',' . $pSize)->select();
        $this->assign('list', $result);
        if (IS_AJAX && I('is_ajax_list') == 1) {
            $this->display('indexAjaxList');
        } else {
            $this->assign('count', $count);
            $this->display('indexAjax');
        }
    }

    public function detail() {
        $id = I('id', '', 'intVal');
        $this->assign('info', M('article')->where(array('statu' => 1, 'id' => $id))->find());
        $this->display('ggdetail');
    }

}
