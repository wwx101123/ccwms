<?php

namespace Mobile\Controller;

class ShopController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }

    public function shopIndex() {
        $this->assign('navlist', M('mobile_nav')->where('statu=1')->order('sort desc')->limit(10)->select());
        $goodsWhere = array();
        $goodsWhere['statu'] = 1;
        $goodsWhere['integral'] = array('elt', 0);
        $count = M('goods')->where($goodsWhere)->count();
        if (IS_AJAX && I('is_ajax_list') == 1) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('goods')->where($goodsWhere)->order('sort desc')->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('list', $result);
            $this->display('shopIndexAjax');
        } else {

            # 推荐商品
            $topGoodsArr = M('goods')->where(array('is_top' => 1, 'statu' => 1, 'goods_state' => 1))->limit(9)->select();
            $topGoodsList = array();
            for ($i = 1; $i <= ceil(count($topGoodsArr) / 3); $i++) {
                $topGoodsList[$i] = array();
                foreach ($topGoodsArr as $k => $v) {
                    if ($k >= ($i - 1) * 3 && $k < $i * 3) {
                        $topGoodsList[$i][$k] = $v;
                    }
                }
            }
            $this->assign('topGoodsList', $topGoodsList);
            $this->assign('topPageNum', ceil(count($topGoodsArr) / 3));

            # 最新商品
            $newGoodsArr = M('goods')->where(array('is_new' => 1, 'statu' => 1, 'goods_state' => 1))->limit(9)->select();
            $newGoodsList = array();
            for ($i = 1; $i <= ceil(count($newGoodsArr) / 3); $i++) {
                $newGoodsList[$i] = array();
                foreach ($newGoodsArr as $k => $v) {
                    if ($k >= ($i - 1) * 3 && $k < $i * 3) {
                        $newGoodsList[$i][$k] = $v;
                    }
                }
            }
            $this->assign('newGoodsList', $newGoodsList);
            $this->assign('newPageNum', ceil(count($newGoodsArr) / 3));

            $noticeList = M('notice')->where(array('top' => 1, 'is_class' => 1))->order('sort desc')->limit(3)->select();
            $this->assign('noticeList', $noticeList);
            $this->assign('count', $count);
            $this->display('shopIndex');
        }
    }

    public function cateIndex() {
        if (IS_AJAX) {
            $catId = I('catId', '', 'intval');
            if ($catId <= 0) {
                $this->error('操作失败');
            }
            $cateInfo = M('goods_cate')->where(array('cat_id' => $catId))->find();
            $catList = M('goods_cate')->where(array('parent_id' => $catId))->select();
            $catList[] = $cateInfo;
            foreach ($catList as &$v) {
                if ($v['cat_id'] != $cateInfo['cat_id']) {
                    $idArr = M('goods_cate')->where(array('parent_id' => $v['cat_id']))->getField('name, cat_id');
                }
                $idArr[$v['name']] = $v['cat_id'];
                $v['goods'] = M('goods')->where(array('cat_id' => array('in', $idArr), 'statu' => 1))->order('sort desc')->limit(12)->select();
            }
            $this->assign('catList', $catList);
            $data = array(
                'status' => 1,
                'msg' => '操作成功',
                'data' => $this->fetch('cateIndexAjax')
            );
            $this->ajaxReturn($data);
        } else {
            $catList = M('goods_cate')->where('parent_id = 0')->order('sort desc')->select();
            $this->assign('catList', $catList);
            $this->display('cateIndex');
        }
    }

    public function goodsList() {
        $this->display('goodsList');
    }

    /**
     * 搜索商品页
     */
    public function searchGoods() {
        $this->display('searchGoods');
    }

    /**
     * 添加搜索记录
     */
    public function addSearchLog() {
        if (IS_POST) {
            $post = I("post.");
            // $getMacAddr = new \Common\Org\GetMacAddr();
            // $macAddr = $getMacAddr->GetMacAddr();
            $macAddr = session_id();

            $fileName = md5($macAddr);
            $kwd = $post['kwd'];

            $str = file_get_contents('Public/Log/searchLog/' . $fileName . '.json');
            $arr = json_decode($str, true);
            $kwdArr = getArrColumn($arr, 'kwd');
            if (!in_array($kwd, $kwdArr)) {
                $arr[] = array(
                    'date' => date('Y-m-d H:i:s'),
                    'kwd' => $kwd
                );
            } else {
                $key = array_search($kwd, $kwdArr);
                $arr[$key]['date'] = date('Y-m-d H:i:s');
            }
            $str = json_encode($arr);
            file_put_contents('Public/Log/searchLog/' . $fileName . '.json', $str);

            $this->success('操作成功');
        }
    }

    /**
     * 获取搜索历史
     */
    public function getSearchLog() {
        // $getMacAddr = new \Common\Org\GetMacAddr();
        // $macAddr = $getMacAddr->GetMacAddr();
        $macAddr = session_id();

        $fileName = md5($macAddr);

        $str = file_get_contents('Public/Log/searchLog/' . $fileName . '.json');
        $arr = json_decode($str, true);

        $data = array(
            'status' => 1,
            'data' => $arr
        );

        $this->ajaxReturn($data);
    }

    /**
     * 删除搜索历史
     */
    public function clearSearchLog() {
        // $getMacAddr = new \Common\Org\GetMacAddr();
        // $macAddr = $getMacAddr->GetMacAddr();
        $macAddr = session_id();

        $fileName = md5($macAddr);

        unlink('Public/Log/searchLog/' . $fileName . '.json');

        $this->success('清除成功');
    }

}
