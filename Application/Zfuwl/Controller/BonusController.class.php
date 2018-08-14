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

class BonusController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('bonusPerList', bonusPer());
        $this->assign('bonusSingle', M('bonus')->where("statu=1")->cache('bonus')->getField('bonus_id,name_cn'));
        $this->assign('bonusSjList', bonusSj());
        $this->assign('moneylist', M('money')->where("statu=1")->cache('money')->getField('money_id,name_cn'));
        $this->assign('blockInfo', M('block')->where("statu=1")->getField('id,name_cn'));
        $this->assign('bonusTaxlist', M('bonus_tax')->where("statu=1")->cache('bonusTaxlist')->getField('tax_id,name_cn'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            $sort_order = I('order_by', 'bonus_id') . ' ' . I('sort', 'desc');
            $count = M('bonus')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('bonus')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BonusLogic();
            $res = $model->addBonusConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bonus/bonusList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('bonus')->where(array('bonus_id' => I('id')))->find());
            $this->display('bonusInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('bonus')->where(array('bonus_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function taxIndex() {
        if (IS_AJAX) {
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'tax_id') . ' ' . I('sort', 'desc');
            $count = M('bonus_tax')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('bonus_tax')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('taxIndexAjax');
            die;
        }
        $this->display('taxIndex');
    }

    public function addBonusTax() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BonusLogic();
            $res = $model->addBonusTaxConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bonus/taxIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('taxInfo');
        }
    }

    public function taxEdti() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BonusLogic();
            $res = $model->addBonusTaxConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bonus/taxIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('bonus_tax')->where(array('tax_id' => I('id')))->find());
            $this->display('taxInfo');
        }
    }

    public function saveStatuTax() {
        if (IS_POST) {
            $res = M('bonus_tax')->where(array('tax_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delTax() {
        $where = array('tax_id' => array('in', I('id')));
        $res = $row = M('bonus_tax')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function bonusLog() {
        if (IS_AJAX) {
            $condition = array();
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('come_account') ? $condition['come_uid'] = $res = M('users')->where(array('account' => trim(I('come_account'))))->getField('user_id') : false;
            I('bonus_id') ? $condition['bonus_id'] = I('bonus_id') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('bonus_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('bonus_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userA = getArrColumn($result, 'uid');
            $userB = getArrColumn($result, 'come_uid');
            $userIdArr = array_filter(array_merge($userA, $userB));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('bonusLogAjax');
            die;
        }
        $this->display('bonusLog');
    }

    public function editBonusLog() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BonusLogic();
            $res = $model->editBonusLogInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bonus/bonusLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('bonus_log')->where(array('id' => I('id')))->find());
            $this->display('bonusLogInfo');
        }
    }

    public function bonusLogClear() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\BonusLogic();
            $res = $model->bonusLogClear($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Bonus/bonusLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function delBonusLog() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('bonus_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyBonusLog() {
        $db = M('bonus_log');
        $dbconn = M();
        $tables = array(
            'bonus_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
