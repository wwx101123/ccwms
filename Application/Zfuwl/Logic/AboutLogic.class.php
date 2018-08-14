<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class AboutLogic extends RelationModel {

    protected $tableName = 'about';

    public function aboutInfo($post) {
        $num = D('about')->where(array('type' => $post['type']))->count();
        if ($post['id'] > 0) {
            if ($num > 1) {
                return array('status' => -1, 'msg' => '己存在相同记录');
            }
        } else {
            if ($num > 0) {
                return array('status' => -1, 'msg' => '己存在相同记录');
            }
        }

        // $post['content'] && $data['content'] = $post['content'];
        $post['content'] && $data['content'] = htmlspecialchars($post['content']);
        $post['type'] && $data['type'] = $post['type'];
        $post['cn'] && $data['cn'] = $post['cn'];
        $post['statu'] && $data['statu'] = $post['statu'];
        if ($post['id'] > 0) {
            $resId = M('about')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('about')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

}
