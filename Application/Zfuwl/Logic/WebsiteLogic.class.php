<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class WebsiteLogic extends RelationModel {

    protected $tableName = 'country';

    public function countryInfo($post) {
        $post['name_cn'] && $data['name_cn'] = $post['name_cn'];
        $post['name_en'] && $data['name_en'] = $post['name_en'];
        $post['code'] && $data['code'] = $post['code'];
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        if ($post['id'] > 0) {
            $resId = M('country')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('country')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    public function regionInfo($post) {
        if ($post['parent_id'] > 0) {
            if (M('region')->where("parent_id = " . $post['parent_id'] . " and name_cn='" . $post['name_cn'] . "'")->find()) {
                return array('status' => -1, 'msg' => '该区域下已有该地区,请不要重复添加');
            }
            $info = M('region')->where(array('id' => $post['parent_id']))->find();
            if ($info['level'] >= 4) {
                return array('status' => -1, 'msg' => '最多添加 四级');
            } else {
                $data['level'] = $info['level'] + 1;
            }
        } else {
            if ($post['id'] <= 0) {
                $data['level'] = $post['level'] ? $post['level'] : 1;
            }
        }
        $post['name_cn'] && $data['name_cn'] = $post['name_cn'];
        $post['name_en'] && $data['name_en'] = $post['name_en'];
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        if ($post['id'] > 0) {
            $resId = M('region')->where(array('id' => $post['id']))->save($data);
        } else {
            $data['parent_id'] = $post['parent_id'] ? $post['parent_id'] : 0;
            $resId = M('region')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

}
