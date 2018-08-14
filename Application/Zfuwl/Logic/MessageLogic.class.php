<?php

namespace Zfuwl\Logic;

use Zfuwl\Model\CommonModel;

class MessageLogic extends CommonModel {

    protected $tableName = 'users_message';

    public function replyUser($post) {
        
        if ($post['id'] > 0) {
            $list = M('users_message')->where(array('id' => $post['id']))->find();
            if ($list['statu'] == 1) {
                $rId = M('users_message')->where(array('id' => $post['id']))->save(array('statu' => 2, 'reply_time' => time(), 'reply' => $post['reply']));
            } else {
                $rId = M('users_message')->where(array('id' => $post['id']))->save(array('reply_time' => time(), 'reply' => $post['reply']));
            }
            if ($rId) {
                adminLogAdd('回复留言ID' . $list['id']);
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                return array('status' => -1, 'msg' => '操作失败');
            }
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

}
