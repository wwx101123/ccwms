<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class BankLogic extends RelationModel {

    public function bankInfo($post) {
        $post['name_cn'] && $data['name_cn'] = $post['name_cn'];
        $post['name_en'] && $data['name_en'] = $post['name_en'];
        $post['address'] && $data['address'] = $post['address'];
        $post['account'] && $data['account'] = $post['account'];
        $post['username'] && $data['username'] = $post['username'];
        $post['img'] && $data['img'] = $post['img'];
        $post['code'] && $data['code'] = $post['code'];
        $data['is_c'] = $post['is_c'] ? $post['is_c'] : 1;
        $data['is_t'] = $post['is_t'] ? $post['is_t'] : 1;
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        $data['sort'] = $post['sort'] ? $post['sort'] : 50;
        if ($post['id'] > 0) {
            $resId = M('bank')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('bank')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

}
