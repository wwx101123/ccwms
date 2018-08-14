<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class HelpLogic extends RelationModel {

    protected $tableName = 'help';

    public function helpInfo($post) {
        if ($post['cat_id'] <= 0) {
            return array('status' => -1, 'msg' => '分类不能为空');
        }
        if ($post['title'] == '') {
            return array('status' => -1, 'msg' => '标题不能为空');
        }
        if ($post['content'] == '') {
            return array('status' => -1, 'msg' => '内容不能为空');
        }
        $post['title'] && $data['title'] = $post['title'];
        $post['content'] && $data['content'] = $post['content'];
        $post['description'] && $data['description'] = $post['description'];
        $post['keywords'] && $data['keywords'] = $post['keywords'];
        $post['cat_id'] && $data['cat_id'] = $post['cat_id'];
        if ($post['id'] > 0) {
            $resId = M('help')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('help')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

}
