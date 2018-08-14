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
 * Date:2016-12-10 19:39  68
 */

namespace Zfuwl\Controller;

class ServiceController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('regions', M('region')->cache('region')->getField('id, name_cn'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
        $this->assign('serviceInfo', M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn'));
        $this->assign('upgradeStatu', upgradeStatu());
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('service')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('service')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $model = new \Zfuwl\Logic\ServiceLogic();
            $res = $model->addServiceConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Service/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('serviceInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ServiceLogic();
            $res = $model->addServiceConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Service/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('service')->where(array('id' => I('id')))->find());
            $this->display('serviceInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('service')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('service')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function upgradeLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('service_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('service_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $provinceIdArr = getArrColumn($result, 'province');
            if ($provinceIdArr) {
                $this->assign('provinceInfo', M('region')->where("id in (" . implode(',', $provinceIdArr) . ")")->getField('id,name_cn'));
            }
            $cityIdArr = getArrColumn($result, 'city');
            if ($cityIdArr) {
                $this->assign('cityInfo', M('region')->where("id in (" . implode(',', $cityIdArr) . ")")->getField('id,name_cn'));
            }
            $districtIdArr = getArrColumn($result, 'district');
            if ($districtIdArr) {
                $this->assign('districtInfo', M('region')->where("id in (" . implode(',', $districtIdArr) . ")")->getField('id,name_cn'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('upgradeLogAjax');
            die;
        }
        $this->display('upgradeLog');
    }

    public function refuseInfo() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ServiceLogic();
            $res = $model->refuseInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Service/upgradeLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function confirmInfo() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ServiceLogic();
            $res = $model->confirmInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Service/upgradeLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function delUpgrade() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('service_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyServiceLog() {
        $db = M('service_log');
        $dbconn = M();
        $tables = array(
            'service_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function userService() {
        if (IS_AJAX) {
            $condition = array();
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            $condition['service'] = array('egt', 1);
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $count = M('users')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('regionInfo', M('region')->cache('regionInfo')->getField('id, name_cn'));
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userServiceAjax');
            die;
        }
        $this->display('userService');
    }

    public function userServicelist() {
        if (IS_AJAX) {
            $condition = array();
            I('account') && $condition['account'] = array('like', '%' . trim(I('account') . '%'));
            I('level') && $condition['level'] = I('level');
            I('serviceid') && $condition['service'] = I('serviceid');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['reg_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'user_id') . ' ' . I('sort', 'desc');
            $count = M('users')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('regionInfo', M('region')->cache('regionInfo')->getField('id, name_cn'));
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userServicelistAjax');
            die;
        }
        $this->display('userServicelist');
    }

    public function editUserService() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ServiceLogic();
            $res = $model->editUserService($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('User/formal'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('userInfo', M('users')->where(array('user_id' => I('user_id')))->find());
            $this->province = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
            $this->display('editUserService');
        }
    }

}
