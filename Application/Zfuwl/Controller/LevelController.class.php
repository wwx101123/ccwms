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

class LevelController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('bonusList', M('bonus')->where("statu=1")->cache('bonusList')->select());
        $this->assign('moneylist', M('money')->where("statu=1")->cache('money')->getField('money_id,name_cn'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
        $this->assign('upgradeStatu', upgradeStatu());
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            $sort_order = I('order_by', 'level_id') . ' ' . I('sort', 'desc');
            $count = M('level')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('level')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            foreach($result as &$v) {
                $tj = explode(',', $v['b_2_tj']);
                $arr = [];
                foreach($tj as $key=>$val) {
                    $tj2 = explode('|', $val);
                    $arr[$key]['zt_num'] = $tj2[0];
                    $arr[$key]['dai'] = $tj2[1];
                }
                $v['b_2_tj'] = $arr;
            }
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
            $model = new \Zfuwl\Logic\LevelLogic();
            $res = $model->addLevelConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Level/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('levelInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LevelLogic();
            $res = $model->addLevelConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Level/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('level')->where(array('level_id' => I('id')))->find());
            $this->display('levelInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('level')->where(array('level_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveReg() {
        if (IS_POST) {
            $res = M('level')->where(array('level_id' => I('id')))->save(array('reg' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('level_id' => array('in', I('id')));
        $res = $row = M('level')->where($where)->delete();
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
            $count = M('level_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('level_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
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
            $model = new \Zfuwl\Logic\LevelLogic();
            $res = $model->refuseInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Level/upgradeLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function confirmInfo() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LevelLogic();
            $res = $model->confirmInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Level/upgradeLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    public function delUpgrade() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('level_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyLevelLog() {
        $db = M('level_log');
        $dbconn = M();
        $tables = array(
            'level_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function editUserLevel() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\LevelLogic();
            $res = $model->editUserLevel($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('User/formal'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('userInfo', M('users')->where(array('user_id' => I('user_id')))->find());
            $this->display('editUserLevel');
        }
    }

}
