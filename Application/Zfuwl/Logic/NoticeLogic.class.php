<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class NoticeLogic extends RelationModel {

    protected $tableName = 'notice';

    public function noticeInfo($post) {
        if ($post['type'] <= 0) {
            return array('status' => -1, 'msg' => '部门选择不能为空');
        }
        $post['title'] && $data['title'] = $post['title'];
        $post['content'] && $data['content'] = $post['content'];
        $post['thumb'] ? $data['thumb'] = $post['thumb'] : FALSE;
        $post['type'] ? $data['type'] = $post['type'] : 1;
        $post['statu'] ? $data['statu'] = $post['statu'] : 1;
        $post['top'] ? $data['top'] = $post['top'] : 2;
        $post['cn'] ? $data['cn'] = $post['cn'] : 1;
        if ($post['id'] > 0) {
            $data['edit_time'] = time();
            $resId = M('notice')->where(array('id' => $post['id']))->save($data);
        } else {
            $data['add_time'] = time();
            $resId = M('notice')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

}
