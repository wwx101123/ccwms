<?php

namespace Zfuwl\Controller;

class ArticleController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('catInfo', M('article_cat')->where("statu=1")->cache('catInfo')->getField('cat_id,title'));
    }

    public function catIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('title') && $condition['title'] = array('like', '%' . trim(I('title') . '%'));
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('type') ? $condition['type'] = I('type') : false;
            I('cn') ? $condition['cn'] = I('cn') : false;
            $sort_order = I('order_by', 'cat_id') . ' ' . I('sort', 'desc');
            $count = M('article_cat')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('article_cat')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('catIndexAjax');
            die;
        }
        $this->display('catIndex');
    }

    public function addCat() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ArticleLogic();
            $res = $model->catInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Article/catIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('catInfo');
        }
    }

    public function editCat() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ArticleLogic();
            $res = $model->catInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Article/catIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('article_cat')->where(array('id' => I('id')))->find());
            $this->display('catInfo');
        }
    }

    public function saveCatStatu() {
        if (IS_POST) {
            $res = M('article_cat')->where(array('cat_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delCat() {
        $where = array('cat_id' => array('in', I('id')));
        $res = $row = M('article_cat')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('title') && $condition['title'] = array('like', '%' . trim(I('title') . '%'));
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('cat_id') ? $condition['cat_id'] = I('cat_id') : false;
            I('cn') ? $condition['cn'] = I('cn') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('article')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('article')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\ArticleLogic();
            $res = $model->articleInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Article/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('articleInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ArticleLogic();
            $res = $model->articleInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Article/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('article')->where(array('id' => I('id')))->find());
            $this->display('articleInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('article')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('article')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
