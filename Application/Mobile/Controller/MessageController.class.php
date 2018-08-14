<?php

namespace Mobile\Controller;

use Think\AjaxPage;

class MessageController extends CommonController {

    protected $messageLogic;

    public function _initialize() {
        parent::_initialize();

        $this->messageLogic = new \Zfuwl\Logic\MessageLogic();
    }

    public function messageIndex() {

        $this->checkPublicNotice(); // 检查信件

        $userMessageModel = M('web_users_message');
        $condition = array(
            'user_id' => $this->user_id,
            'status' => array('neq',3)
        );
        $count = $userMessageModel->where($condition)->count();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            // field 中不能带空格
            $messageList = D("UserMessageView")->where($condition)->field('rec_id,message,status,send_time')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('messageList', $messageList);

            $this->display('messageIndexAjax');
        } else {
            $this->assign('count', $count);
            $this->display('messageIndex');
        }
    }

    /**
     * 获取会员通知数
     */
    public function getUserMessageNum()
    {
        $this->checkPublicNotice();


        $condition = array(
            'user_id' => $this->user_id,
            'status' => 1
        );
        $count = M('web_users_message')->where($condition)->count();

        $this->ajaxReturn(array('status' => 1, 'num' => $count));
    }

    /**
     * 系统通知
     */
    public function notice() {
        if (IS_AJAX) {

            $this->checkPublicNotice(); // 检查信件

            $userMessageModel = M('web_users_message');
            $condition = array(
                'user_id' => $this->user_id,
                'status' => 1
            );
            $count = $userMessageModel->where($condition)->count();
            $page = new \Think\AjaxPage($count, 1);
            // field 中不能带空格
            $messageList = D("UserMessageView")->where($condition)->field('rec_id,message,status,send_time')->limit($page->firstRow . ',' . $page->listRows)->select();

            $this->assign('messageList', $messageList);
            $this->assign('page', $page->show());

            $this->display('noticeAjax');
        } else {
            $this->display('notice');
        }
    }

    /**
     * 检查该会员是否收到管理员发的通知
     */
    public function checkPublicNotice() {

        $user = $this->user;
        $messageModel = M('web_message');
        $userMessageModel = M('web_users_message');
        $userMessageList = $userMessageModel->where("user_id = {$user['user_id']}")->field('message_id')->select();

        $messageWhere = array(
            'send_time' => array('gt', $user['reg_time']), // 查询会员注册后管理员发送的信息
        );

        if (!empty($userMessageList)) {
            $messageIdArr = getArrColumn($userMessageList, 'message_id');
            $messageWhere['message_id'] = array('NOT IN', $messageIdArr);
        }
        $userSystemPublicNoRead = $messageModel->where($messageWhere)->field('message_id, send_user_id')->select();
        // 如果有管理员发送的消息并包括该会员就添加消息记录
        foreach ($userSystemPublicNoRead as $v) {
            $sendUserIdArr = explode(',', $v['send_user_id']);
            if (in_array($user['user_id'], $sendUserIdArr)) {
                $userMessageData = array(
                    'user_id' => $user['user_id'],
                    'message_id' => $v['message_id'],
                    'status' => 1
                );
                $userMessageModel->add($userMessageData); // 执行添加
            }
        }
    }

    /**
     * 查看系统通知详情
     */
    public function showUserMessageDetail() {
        $id = I('id', '', 'intVal');

        $res = M("web_users_message")->where("rec_id = {$id}")->save(array("status" => 2)); // 把信件标为已读

        $messageInfo = D("UserMessageView")->where("rec_id = {$id}")->field("message,send_time,rec_id")->find();
        $this->assign('messageInfo', $messageInfo);

        $this->display("showUserMessageDetail");
    }

    /**
     * 会员删除系统通知
     */
    public function delUserMessage()
    {
        $data = array(
            'status' => 3
        );
        $id = I('id','','intval');
        if($id <= 0) {
            $res = M('web_users_message')->where(array('user_id' => $this->user_id,'status' => 2))->save($data);
        } else {
            $res = M('web_users_message')->where(array('rec_id' => $id))->save($data);
        }
        if($res) {
            $this->success('删除成功', U('Message/messageIndex'));
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 会员给管理员留言
     */
    public function sendMessage() {

        if (IS_POST) {
            $post = I('post.');
            if ($post['title'] == '') {
                $this->error('请输入留言标题!');
            }
            if ($post['content'] == '') {
                $this->error('请输入留言内容!');
            }
            if(intval($post['type']) < 1) {
                $this->error('请选择留言类型');
            }
            $messageData = [
                'uid' => $this->user_id
                ,'zf_time' => time()
                ,'title' => $post['title']
                ,'content' => $post['content']
                ,'statu' => 1
                ,'type' => (intval($post['type']) ? intval($post['type']) : 1)
                ,'img' => $post['img']
            ];
            $res = M('users_message')->add($messageData);
            if ($res) {
                $this->success('留言成功! 等待管理员回复!', U('Message/messageList'));
            } else {
                $this->error('留言失败!');
            }
        } else {
            $this->assign('messageType', messageType());
            $this->display('sendMessage');
        }
    }

    /**
     * 会员留言列表
     */
    public function messageList() {
        $condition = [
            'uid' => $this->user_id
        ];
        $count = $this->messageLogic->where($condition)->count();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $list = $this->messageLogic->where($condition)->order("id desc")->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('list', $list);
            $this->display('messageListAjax');
        } else {
            $this->assign('count', $count);
            $this->display('messageList');
        }
    }

    /**
     * 查看会员留言详情
     */
    public function messageDetail() {
        $id = I('id', '', 'intVal');

        $messageInfo = $this->messageLogic->findDataByField('id', $id);
        if ($id <= 0 || !$messageInfo) {
            $this->error('未找到信息!');
        }
        $this->assign('messageInfo', $messageInfo);

        $this->display('messageDetail');
    }

    /**
     * 会员删除留言
     */
    public function delMessage() {
        $id = I('id', '', 'intVal');

        if ($id <= 0) {
            $this->error('操作失败!');
        }

        $res = $this->messageLogic->delMessage($id);
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
