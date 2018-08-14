<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class ArticleLogic extends RelationModel {

    protected $tableName = 'article_cat';

    public function catInfo($post) {
        if ($post['title'] == '') {
            return array('status' => -1, 'msg' => '分类名称不能为空');
        }
        $post['title'] && $data['title'] = $post['title'];
        $post['keywords'] && $data['keywords'] = $post['keywords'];
        $post['desc'] && $data['desc'] = $post['desc'];
        $post['statu'] ? $data['statu'] = $post['statu'] : 1;
        $post['cn'] ? $data['cn'] = $post['cn'] : 1;
        if ($post['id'] > 0) {
            $resId = M('article_cat')->where(array('cat_id' => $post['id']))->save($data);
        } else {
            $resId = M('article_cat')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    public function articleInfo($post) {
        if ($post['cat_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择分类');
        }
        if ($post['title'] == '') {
            return array('status' => -1, 'msg' => '标题不能为空');
        }
        $post['title'] && $data['title'] = $post['title'];
        $post['cat_id'] && $data['cat_id'] = $post['cat_id'];
        $post['content'] && $data['content'] = $post['content'];
        $post['keywords'] && $data['keywords'] = $post['keywords'];
        $post['description'] && $data['description'] = $post['description'];
        $post['statu'] ? $data['statu'] = $post['statu'] : 1;
        $post['thumb'] && $data['thumb'] = $post['thumb'];
        if ($post['id'] > 0) {
            $resId = M('article')->where(array('id' => $post['id']))->save($data);
        } else {
            $data['add_time'] = time();
            $resId = M('article')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

}
