<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class AdLogic extends RelationModel {

    protected $tableName = 'ad_site';

    public function addSiteInfo($post) {
        if ($post['name'] == '') {
            return array('status' => -1, 'msg' => '广告位名称不能为空');
        }
        if ($post['width'] <= 0) {
            return array('status' => -1, 'msg' => '广告位宽度输入错误');
        }
        if ($post['height'] <= 0) {
            return array('status' => -1, 'msg' => '广告位高度输入错误');
        }
        $num = D('ad_site')->where(array('site_where' => $post['site_where']))->count();
        if ($post['id'] > 0) {
            if ($num > 1) {
                return array('status' => -1, 'msg' => '己存在相同标识广告位');
            }
        } else {
            if ($num > 0) {
                return array('status' => -1, 'msg' => '己存在相同标识广告位');
            }
        }
        $post['name'] && $data['name'] = $post['name'];
        $post['width'] && $data['width'] = $post['width'];
        $post['height'] && $data['height'] = $post['height'];
        $post['site_where'] && $data['site_where'] = $post['site_where'];
        $post['statu'] ? $data['statu'] = $post['statu'] : 1;
        if ($post['id'] > 0) {
            $resId = M('ad_site')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('ad_site')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    public function addAdInfo($post) {
        if ($post['name'] == '') {
            return array('status' => -1, 'msg' => '广告名称不能为空');
        }
        if ($post['ad_code'] == '') {
            return array('status' => -1, 'msg' => '请上传广告图片');
        }
        if ($post['site_id'] <= 0) {
            return array('status' => -1, 'msg' => '所属广告位选择错误');
        }
        if ($post['start_time'] <= 0) {
            return array('status' => -1, 'msg' => '请选择投放开始时间');
        }
        if ($post['end_time'] <= 0) {
            return array('status' => -1, 'msg' => '请选择投放结束时间');
        }
        if ($post['end_time'] < $post['start_time']) {
            return array('status' => -1, 'msg' => '结束时间不能小于开始时间');
        }
        if ($post['target'] > 0) {
            if ($post['ad_link'] == '') {
                return array('status' => -1, 'msg' => '链接地址不能为空');
            }
        }
        $post['name'] && $data['name'] = $post['name'];
        $post['site_id'] && $data['site_id'] = $post['site_id'];
        $post['target'] && $data['target'] = $post['target'];
        $post['ad_link'] && $data['ad_link'] = $post['ad_link'];
        $post['start_time'] && $data['start_time'] = strtotime($post['start_time']);
        $post['end_time'] && $data['end_time'] = strtotime($post['end_time']);
        $post['ad_code'] && $data['ad_code'] = $post['ad_code'];
        $post['bgcolor'] && $data['bgcolor'] = $post['bgcolor'];
        $post['statu'] ? $data['statu'] = $post['statu'] : 1;
        if ($post['id'] > 0) {
            $resId = M('ad')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('ad')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

}
