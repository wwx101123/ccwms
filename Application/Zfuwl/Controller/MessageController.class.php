<?php

namespace Zfuwl\Controller;

class MessageController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('messageStatu', messageStatu());
        $this->assign('messageType', messageType());
    }

    /**
     * 会员留言列表
     */
    public function messageIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('title') && $condition['title'] = array('like', '%' . trim(I('title') . '%'));
            I('type') && $condition['type'] = I('type');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_message')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_message')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('messageIndexAjax');
            die;
        }
        $this->display('messageIndex');
    }

    /**
     * 回复会员
     */
    public function replyUser() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MessageLogic();
            $res = $model->replyUser($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Message/messageIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $info = M('users_message')->where(array('id' => I('id')))->find();
            $this->assign('info', $info);
            $this->assign('user', M('users')->where(array('user_id' => $info['uid']))->field('account')->find());
            $this->display('replyUser');
        }
    }

    /**
     * 删除会员留言
     */
    public function delMessage() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('users_message')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyMessage() {
        $db = M('users_message');
        $dbconn = M();
        $tables = array(
            'users_message',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
