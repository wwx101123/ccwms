<?php

namespace Zfuwl\Controller;

use Zfuwl\Logic\GoodsLogic;

/**
 * Class GoodsController
 * @package Zfuwl\Controller
 */
class GoodsController extends CommonController {

    private $goodsCatModel;
    private $goodsModel;

    public function _initialize() {
        parent::_initialize();
        $this->goodsCatModel = new GoodsLogic('goods_cate');
        $this->goodsModel = new GoodsLogic();
    }

    public function catIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('name') && $condition['name'] = array('like', '%' . I('name') . '%');
            $catList = $this->goodsCatModel->selectAll($condition);
            $catList = getArticleCatColumn($catList);
            $this->assign('catList', $catList);
            $this->display('catIndexAjax');
        } else {
            $this->display('catIndex');
        }
    }

    /**
     * 添加商品分类
     */
    public function addCat() {
        if (IS_POST) {
            $post = I('post.');
            $res = $this->goodsCatModel->addGoodsCat($post);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $where = array(
                'statu' => 1,
                'parent_id' => 0
            );
            $catList = $this->goodsCatModel->selectAll($where);
            $this->assign('catList', $catList);
            $this->display('catInfo');
        }
    }

    public function editCat() {
        $catId = I('id', '', 'intVal');
        if (IS_POST) {
            $post = I('post.'); // $_POST数据
            $res = $this->goodsCatModel->editGoodsCat($post, $catId); // 执行修改
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $info = $this->goodsCatModel->findGoodsCatById($catId);
            $where = array(
                'statu' => 1,
                'parent_id' => 0
            );
            $catList = $this->goodsCatModel->selectAll($where);
            $this->assign('info', $info);
            $this->assign('catList', $catList);
            $this->display('catInfo');
        }
    }

    public function saveCatStatu() {
        if (IS_POST) {
            $res = M('goods_cate')->where(array('cat_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveCatTop() {
        if (IS_POST) {
            $res = M('goods_cate')->where(array('cat_id' => I('id')))->save(array('is_top' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delCat() {
        $catId = I('id', '', 'intVal');
        $res = $this->goodsCatModel->delGoodsCat($catId); // 执行删除
        if ($res['status'] == 1) {
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }

    /**
     * 产品列表
     */
    public function goodsIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('name') && $condition['name'] = array('like', '%' . trim(I('name')) . '%');
            $goodsResult = $this->goodsModel->selectAllListAjax($condition);
            $catList = $this->goodsCatModel->selectAll('', array('cat_id', 'name'), 1);
            $this->assign('catList', $catList);
            $this->assign('goodsList', $goodsResult['list']);
            $this->assign('page', $goodsResult['page']);
            $this->display('goodsIndexAjax');
        } else {
            $this->display('goodsIndex');
        }
    }

    /**
     * 添加产品
     */
    public function addGoods() {
        if (IS_POST) {
            $post = I('post.');
            $res = $this->goodsModel->addGoods($post); // 执行添加
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $where = array(
                'statu' => 1,
            );
            $catList = $this->goodsCatModel->selectALl($where);
            $catList = getArticleCatColumn($catList);
            $arr = M('GoodsType')->select();
            $this->assign('goodsType', $arr);
            $this->assign('catList', $catList);
//            $brand_data = (new BrandModel())->get_all_brand();
//            $this->assign('brand_data', $brand_data);
            $this->display('goodsInfo');
        }
    }

    /**
     * 编辑产品
     */
    public function editGoods() {
        $goodsId = I('id', '', 'intVal');
        if (IS_POST) {
            $post = I('post.');
            $res = $this->goodsModel->editGoods($post, $goodsId);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $info = $this->goodsModel->findDataByField('goods_id', $goodsId);
            $info['goods_img'] = explode(',', $info['goods_img']);
            $info['goods_img'] = unArrNull($info['goods_img']);
            $where = array(
                'statu' => 1,
            );
            $catList = $this->goodsCatModel->selectALl($where);
            $catList = getArticleCatColumn($catList);
            $this->assign('catList', $catList);
            $this->assign('info', $info);
            //获取商品模型
            $arr = M('GoodsType')->select();
            $this->assign('goodsType', $arr);
            // 获取品牌
//            $brand_data = (new BrandModel())->get_all_brand();
//            $this->assign('brand_data',$brand_data);
            $this->display('goodsInfo');
        }
    }

    public function saveGoodsStatu() {
        if (IS_POST) {
            $res = M('goods')->where(array('goods_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveGoodsTop() {
        if (IS_POST) {
            $res = M('goods')->where(array('goods_id' => I('id')))->save(array('is_top' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    /**
     * 删除商品
     */
    public function delGoods() {
        $goodsId = I('id', '', 'intVal');
        $res = $this->goodsModel->delGoods($goodsId); // 执行删除
        if ($res['status'] == 1) {
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }

}
