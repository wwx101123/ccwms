<?php

namespace Mobile\Controller;

use Zfuwl\Logic\GoodsLogic;

class GoodsController extends BaseController {

    public $goodsLogic;
    public $goodsCatLogic;

    public function _initialize() {
        parent::_initialize();
        $this->goodsLogic = new GoodsLogic();
        $this->goodsCatLogic = new GoodsLogic('goods_cate');
    }

    public function shopIndex() {
        $this->display('shopIndex');
    }

    public function cateIndex() {
        $this->display('cateIndex');
    }

    public function goodsList() {
        $condition = $order = array();
        $condition['statu'] = 1;
        $condition['goods_state'] = 1;
        $catId = I('get.cid', '', 'intVal'); // 分类id
        if ($catId) {
            $cateWhere = array(
                'parent_id' => $catId
            );
            $categoryList = $this->goodsCatLogic->selectAll($cateWhere, array('name, cat_id'), 1);
            $condition['cat_id'] = array('in', implode(',', $categoryList) . ',' . $catId);
        }
        I('kwd') && $condition['name'] = array('like', "%" . I('kwd') . "%");

        $count = M('goods')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;

            if (I('order') && I('sort')) {
                $order[I('order')] = (I('sort') == 'desc' ? 'desc' : 'asc');
            } else {
                $order['goods_id'] = 'desc';
            }
            $order['sort'] = 'desc';
            $result = M('goods')->where($condition)->order($order)->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('list', $result);

            $this->display('goodsListAjax');
        } else {
            $this->assign('cateList', M('goods_cate')->where(array('statu' => 1, 'parent_id' => 0))->select());
            $this->assign('count', $count);
            $this->display('goodsList');
        }
    }

    /**
     * 积分商城列表
     */
    public function integralMall() {
        $condition = $order = array();
        $condition['statu'] = 1;
        $condition['goods_state'] = 1;
        $condition['integral'] = array('gt', 0);
        $catId = I('get.cid', '', 'intVal'); // 分类id
        if ($catId) {
            $cateWhere = array(
                'parent_id' => $catId
            );
            $categoryList = $this->goodsCatLogic->selectAll($cateWhere, array('name, cat_id'), 1);
            $condition['cat_id'] = array('in', implode(',', $categoryList) . ',' . $catId);
        }
        I('kwd') && $condition['name'] = array('like', "%" . I('kwd') . "%");

        $count = M('goods')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            if (I('order') && I('sort')) {
                $order[I('order')] = (I('sort') == 'desc' ? 'desc' : 'asc');
            } else {
                $order['goods_id'] = 'desc';
            }
            $order['sort'] = 'desc';
            $result = M('goods')->where($condition)->order($order)->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('list', $result);

            $this->display('integralMallAjax');
        } else {
            $this->assign('cateList', M('goods_cate')->where(array('statu' => 1, 'parent_id' => 0))->select());
            $this->assign('count', $count);
            $this->display('integralMall');
        }
    }

    /**
     * 爆款专区
     */
    public function manyMall() {
        $condition = $order = array();
        $condition['statu'] = 1;
        $condition['goods_state'] = 1;
        $catId = I('get.cid', '', 'intVal'); // 分类id
        if ($catId) {
            $cateWhere = array(
                'parent_id' => $catId
            );
            $categoryList = $this->goodsCatLogic->selectAll($cateWhere, array('name, cat_id'), 1);
            $condition['cat_id'] = array('in', implode(',', $categoryList) . ',' . $catId);
        }
        I('kwd') && $condition['name'] = array('like', "%" . I('kwd') . "%");

        $count = M('goods')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;

            if (I('order') && I('sort')) {
                $order[I('order')] = (I('sort') == 'desc' ? 'desc' : 'asc');
            } else {
                $order['pv'] = 'desc';
                $order['goods_id'] = 'desc';
            }
            $order['sort'] = 'desc';
            $result = M('goods')->where($condition)->order($order)->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('list', $result);
            $this->display('manyMallAjax');
        } else {
            $this->assign('cateList', M('goods_cate')->where(array('statu' => 1, 'parent_id' => 0))->select());
            $this->assign('count', $count);
            $this->display('manyMall');
        }
    }

    /**
     * 商品详情
     */
    public function goodsInfo() {
        $goodsId = I('id', '', 'intVal');

        if (intVal($goodsId) <= 0) {
            $this->error('该商品已下架!');
        }
        $info = $this->goodsLogic->findDataByField('goods_id', $goodsId);
        $attr_data = D("Zfuwl/GoodsAttr")->relation(true)->where(['goods_id' => $goodsId])->select();
        $spec_data = M("GoodsPriceStore")->where(['goods_id' => $goodsId])->field('spec_key,spec_name')->select();
        $spec_data = $this->goodsLogic->getFormatSpec($spec_data);

        $this->assign([
            'attr_data' => $attr_data,
            'spec_data' => $spec_data,
        ]);
        if (!$info || $info['statu'] != 1 || $info['goods_state'] != 1) {
            $this->error('该商品已下架!');
        }


        # 商品评论数
        // $commentCount = M('goods_comment')->where(array('gid' => $info['goods_id'], 'statu' => 1))->count();
        # 最新的两条商品评论
        // $commentList = M('goods_comment')->where(array('gid' => $info['goods_id'], 'statu' =>1))->order('id desc')->limit(2)->select();
        # 评论的会员
        // $commentUserIdArr = getArrColumn($commentList, 'uid');
        // $commentUserList = $commentUserIdArr ? M("users")->where(array('user_id' => array('in', $commentUserIdArr)))->getField('user_id,account') : false;

        // $this->assign('commentUserList', $commentUserList);
        // $this->assign('commentCount', $commentCount);
        // $this->assign('commentList', $commentList);

        $this->assign('navigateCat', navigateCat($info['cat_id']));
        $info['goods_img'] = $info['picture'] . ',' . $info['goods_img'];
        $info['goods_img'] = explode(',', $info['goods_img']);
        $info['goods_img'] = array_filter($info['goods_img']);

        if (session('user')) {
            $this->assign('collect', M('goods_collect')->where(array('uid' => session('user')['user_id'], 'gid' => $info['goods_id']))->find());
        }
        // $this->assign('storeInfo', M('seller')->where("seller_id={$info['seller_id']}")->field('name,logo,goods_num,seller_id')->find());
        // $this->assign('storeGoodsNum', M('goods')->where(array('seller_id' => $info['seller_id'], 'statu' => 1, 'goods_state' => 1))->count());
        $this->assign('goods', $info);

        $this->getRecommendGoodsList();
        $this->assign('aboutInfo', M('about')->where(array('statu' => 1, 'type' => 2, 'cn' => 1))->find());
        $this->display('goodsInfo');
    }

    /**
     * 获取推荐商品
     */
    public function getRecommendGoodsList() {
        $goodsWhere = array();
        $goodsWhere['statu'] = 1;
        if (I('cat_id') > 0) {
            $goodsWhere['cat_id'] = I('cat_id');
        }
        $goodsWhere['goods_state'] = 1;
        $goodsWhere['top'] = 1;
        $goodsWhere['goods_id'] = array('neq', I('id'));

        $count = M('goods')->where($goodsWhere)->count();

        $this->assign('recommendCount', $count);

        if (I('is_ajax_list') == 1) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('goods')->where($goodsWhere)->order('sort desc')->limit(($p * $pSize) . ',' . $pSize)->select();
            $this->assign('list', $result);
            $this->display('recommendGoodsList');
        }
    }

    /**
     * ajax
     * 根据商品规格获取 价格和库存
     */
    public function getPriceBySpec() {
        $post = I("post.");
        $condition['goods_id'] = $post['goods_id'];
        $condition['spec_key'] = implode('_', $post['spec_ids']);
        $data = M("GoodsPriceStore")->field('price,store_count as store,pv,integral')->where($condition)->find();
        if ($data) {
            $this->success($data);
        } else {
            $this->error('价格获取失败');
        }
    }

    /**
     * 会员收藏商品
     */
    public function addGoodsCollect() {
        if (IS_POST) {
            $user = session('user');
            # 判断会员是否登录
            $userId = intval($user['user_id']);
            if ($userId > 0) {
                # 商品id
                $goodsId = I('post.goods_id', '', 'intval');
                $goods = M('goods')->where(array('goods_id' => $goodsId, 'statu' => 1))->find();
                if (!$goods) {
                    $this->error('商品不存在');
                }
                $num = M('goods_collect')->where(array('uid' => $userId, 'gid' => $goodsId))->count();
                if ($num > 0) {
                    $this->error('已经收藏过了');
                }

                $data = array(
                    'uid' => $userId,
                    'gid' => $goodsId,
                    'add_time' => time()
                );

                $res = M('goods_collect')->add($data);
                if ($res) {
                    M('goods')->where(array('goods_id' => $goodsId))->setInc('collect_sum', 1); // 加
                    $this->success('收藏成功');
                } else {
                    $this->error('收藏失败');
                }
            } else {
                $this->error('请先登录');
            }
        }
    }

    /**
     * 收藏列表
     */
    public function goodsCollectList() {
        $user = session('user');
        if (!$user) {
            $this->error('请先登录');
        }
        $condition = array(
            'uid' => $user['user_id']
        );

        if (IS_AJAX) {
            $list = M('goods_collect')->where($condition)->select();
            foreach ($list as &$v) {
                $v['goods'] = M('goods')->where(array('goods_id' => $v['gid']))->find();
            }
            $this->assign('list', $list);
            $this->display('goodsCollectListAjax');
        } else {
            $this->display('goodsCollectList');
        }
    }

    /**
     * 删除收藏
     */
    public function delGoodsCollect() {
        if (IS_POST) {
            $id = I('post.id', '', 'intval');
            $res = M('goods_collect')->where(array('id' => $id))->delete();
            if ($res) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    /**
     * 商品评论列表
     */
    public function commentList()
    {
        $goodsId = I('goods_id', '', 'intval');

        $info = $this->goodsLogic->findDataByField('goods_id', $goodsId);
        if(!$info) {
            $this->error('商品不存在');
        }
        $condition = array(
            'statu' => 1,
            'goods_id' => $goodsId
        );
        $count = M('goods_comment')->where($condition)->count();
        if(IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;

            $list = M('goods_comment')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();
            $userIdArr = getArrColumn($list, 'uid');

            $userIdArr && $this->assign('userList', M('users')->where(array('user_id' => array('in', $userIdArr)))->getField('user_id,account'));

            $this->assign('list', $list);

            $this->display('commentListAjax');
        } else {
            $this->assign('count', $count);
            $this->display('commentList');
        }
    }

}
