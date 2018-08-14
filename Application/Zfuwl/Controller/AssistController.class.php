<?php

namespace Zfuwl\Controller;

class AssistController extends CommonController {

    private $adminLogModel;
    private $adminUserModel;

    public function _initialize() {
        parent::_initialize();
        $this->adminLogModel = D('AdminLog');
        $this->adminUserModel = D('AdminUser');
    }

    /**
     * 管理员操作日志
     */
    public function adminLoglist() {
        if (IS_AJAX) {
            $condition = array();
            $condition['is_type'] = array('neq', DEL_STATUS);
            I('user_name') && $condition['admin_id'] = $this->adminUserModel->selectAll("user_name = '" . I('user_name') . "'", 'admin_id', 1);
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if ($startTime && $endTime) {
                $condition['log_time'] = array('between', array($startTime, $endTime));
            } elseif ($startTime > 0) {
                $condition['log_time'] = array('gt', $startTime);
            } elseif ($endTime > 0) {
                $condition['log_time'] = array('lt', $endTime);
            }
            $sort_order = I('order_by', 'log_id') . ' ' . I('sort', 'desc');
            $result = $this->adminLogModel->selectAllListAjax($condition, $sort_order);

            $adminIdArr = getArrColumn($result['list'], 'admin_id');
            if ($adminIdArr) {
                $adminList = $this->adminUserModel->selectAll("admin_id in(" . implode(',', $adminIdArr) . ")", array('admin_id', 'user_name'), true);
                $this->assign('adminList', $adminList);
            }
            $this->assign('page', $result['page']);
            $this->assign('adminLogList', $result['list']);
            $this->display('adminLogListAjax');
            die;
        }
        $this->display('adminLogList');
    }

    /**
     * 删除管理员操作日志
     */
    public function delAdminLog() {
        $where = array('log_id' => array('in', I('id')));
        $res = $row = M('admin_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 会员操作日志
     */
    public function userLog() {
        if (IS_AJAX) {
            $condition = array();
            $condition['is_type'] = array('neq', DEL_STATUS);
            I('log_info') && $condition['log_info'] = array('like', '%' . trim(I('log_info') . '%'));
            I('account') ? $condition['user_id'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if ($startTime && $endTime) {
                $condition['log_time'] = array('between', array($startTime, $endTime));
            } elseif ($startTime > 0) {
                $condition['log_time'] = array('gt', $startTime);
            } elseif ($endTime > 0) {
                $condition['log_time'] = array('lt', $endTime);
            }
            $sort_order = I('order_by', 'log_id') . ' ' . I('sort', 'desc');
            $count = M('users_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $userList = D('Users')->selectAll("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true);
                $this->assign('userList', $userList);
            }
            $this->assign('page', $Page->show());
            $this->assign('userLogList', $result);
            $this->display('userLogAjax');
            die;
        }
        $this->display('userLog');
    }

    /**
     * 删除会员操作日志
     */
    public function delUserLog() {
        $where = array('log_id' => array('in', I('id')));
        $res = $row = M('users_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 会员激活日志
     */
    public function userActivate() {
        if (IS_AJAX) {
            $condition = array();
            $condition['is_type'] = array('neq', DEL_STATUS);
            I('log_info') && $condition['log_info'] = array('like', '%' . trim(I('log_info') . '%'));
            I('account') ? $condition['user_id'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if ($startTime && $endTime) {
                $condition['log_time'] = array('between', array($startTime, $endTime));
            } elseif ($startTime > 0) {
                $condition['log_time'] = array('gt', $startTime);
            } elseif ($endTime > 0) {
                $condition['log_time'] = array('lt', $endTime);
            }
            $sort_order = I('order_by', 'log_id') . ' ' . I('sort', 'desc');
            $count = M('users_activation_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_activation_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $userList = D('Users')->selectAll("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true);
                $this->assign('userList', $userList);
            }
            $this->assign('page', $Page->show());
            $this->assign('ActivateList', $result);
            $this->display('userActivateAjax');
            die;
        }
        $this->display('userActivate');
    }

    /**
     * 删除会员激活日志
     */
    public function delUserActivate() {
        $where = array('log_id' => array('in', I('id')));
        $res = $row = M('users_activation_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 会员冻结日志
     */
    public function userLockLog() {
        if (IS_AJAX) {
            $condition = array();
            $condition['is_type'] = array('neq', DEL_STATUS);
            I('log_info') && $condition['log_info'] = array('like', '%' . trim(I('log_info') . '%'));
            I('account') ? $condition['user_id'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if ($startTime && $endTime) {
                $condition['log_time'] = array('between', array($startTime, $endTime));
            } elseif ($startTime > 0) {
                $condition['log_time'] = array('gt', $startTime);
            } elseif ($endTime > 0) {
                $condition['log_time'] = array('lt', $endTime);
            }
            $sort_order = I('order_by', 'log_id') . ' ' . I('sort', 'desc');
            $count = M('users_lock_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_lock_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $userList = D('Users')->selectAll("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true);
                $this->assign('userList', $userList);
            }
            $this->assign('page', $Page->show());
            $this->assign('userLockList', $result);
            $this->display('userLockLogAjax');
            die;
        }
        $this->display('userLockLog');
    }

    /**
     * 删除会员冻结日志
     */
    public function delUserLockLog() {
        $where = array('log_id' => array('in', I('id')));
        $res = $row = M('users_lock_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 会员变动日志
     */
    public function userActionLog() {
        if (IS_AJAX) {
            $condition = array();
            $condition['is_type'] = array('neq', DEL_STATUS);
            I('log_info') && $condition['log_info'] = array('like', '%' . trim(I('log_info') . '%'));
            I('account') ? $condition['user_id'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if ($startTime && $endTime) {
                $condition['log_time'] = array('between', array($startTime, $endTime));
            } elseif ($startTime > 0) {
                $condition['log_time'] = array('gt', $startTime);
            } elseif ($endTime > 0) {
                $condition['log_time'] = array('lt', $endTime);
            }
            $sort_order = I('order_by', 'action_id') . ' ' . I('sort', 'desc');
            $count = M('users_action')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_action')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $userList = D('Users')->selectAll("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), true);
                $this->assign('userList', $userList);
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userActionLogAjax');
            die;
        }
        $this->display('userActionLog');
    }

    /**
     * 删除变动日志
     */
    public function delUserActionLog() {
        $where = array('action_id' => array('in', I('id')));
        $res = $row = M('users_action')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }
    /**
     * ip黑名单列表
     */
    public function ipBlackList()
    {
        if(IS_AJAX) {

            $ipStr = file_get_contents('noShowIp.json');

            $ipArr = json_decode($ipStr, true);
            $count = count($ipArr);
            $Page = ajaxGetPage($count, 10);
            $list = array();
            $p = I('p') ? I('p')-1 : 0;

            foreach($ipArr as $k=>$v) {
                if($k >= $p*10 && $k < ($p*10+10)) {
                    $list[] = array(
                        'id' => $k,
                        'key' => $k+1,
                        'time' => date('Y-m-d H:i:s', $v['time']),
                        'ip' => $v['ip'],
                    );
                }
            }
            
            $this->assign('page',$Page->show());
            $this->assign('info',$list);
            $this->display('ipBlackListAJax');
//            exit(json_encode());
        } else {
            $this->display('ipBlackList');
        }
    }

    /**
     * 移除黑名单
     */
    public function delIpLog()
    {

        $ipStr = file_get_contents('noShowIp.json');
        $ipArr = json_decode($ipStr, true);

        $id = explode(',', I('id'));

        foreach($id as $v) {
            unset($ipArr[$v]);
        }


        $list = array();

        foreach($ipArr as $v) {

            $list[] = $v;
        }

        file_put_contents('noShowIp.json', json_encode($list));
        $this->success('移除成功!');
    }
    /**
     * 添加黑名单
     */
    public function addIpBlack()
    {

        if(IS_POST) {

            $ip = I('ip');
            $ipStr = file_get_contents('noShowIp.json');
            $ipArr = json_decode($ipStr, true);
            $ipArr[count($ipArr)] = array(
                'time' => time(),
                'ip' => $ip
            );
            file_put_contents('noShowIp.json', json_encode($ipArr));
            $this->success('添加成功!');
        } else {
            $this->display('addIpBlack');
        }
    }

}
