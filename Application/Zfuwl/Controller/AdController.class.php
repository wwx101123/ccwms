<?php

namespace Zfuwl\Controller;

class AdController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('siteInfo', M('ad_site')->where("statu=1")->cache('siteInfo')->getField('id,name'));
    }

    public function siteIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('name') && $condition['name'] = array('like', '%' . trim(I('name') . '%'));
            I('site_where') && $condition['site_where'] = array('like', '%' . trim(I('site_where') . '%'));
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('ad_site')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('ad_site')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('siteIndexAjax');
            die;
        }
        $this->display('siteIndex');
    }

    public function addSite() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\AdLogic();
            $res = $model->addSiteInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Ad/siteIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('addSiteInfo');
        }
    }

    public function editSite() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\AdLogic();
            $res = $model->addSiteInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Ad/siteIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('ad_site')->where(array('id' => I('id')))->find());
            $this->display('addSiteInfo');
        }
    }

    public function sateSiteStatu() {
        if (IS_POST) {
            $res = M('ad_site')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delSite() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('ad_site')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function adIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('name') && $condition['name'] = array('like', '%' . trim(I('name') . '%'));
            I('site_id') ? $condition['site_id'] = I('site_id') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['start_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('ad')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('ad')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('adIndexAjax');
            die;
        }
        $this->display('adIndex');
    }

    public function addAd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\AdLogic();
            $res = $model->addAdInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Ad/adIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('adInfo');
        }
    }

    public function editAd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\AdLogic();
            $res = $model->addAdInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Ad/adIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('ad')->where(array('id' => I('id')))->find());
            $this->display('adInfo');
        }
    }

    public function saveAdStatu() {
        if (IS_POST) {
            $res = M('ad')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delAd() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('ad')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
