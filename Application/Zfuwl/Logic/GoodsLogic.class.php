<?php

namespace Zfuwl\Logic;

use Zfuwl\Model\CommonModel;
use Zfuwl\Model\GoodsAttrModel;
use Zfuwl\Model\GoodsPriceStoreModel;

class GoodsLogic extends CommonModel {

    protected $tableName;

    public function __construct($tableNmae = 'goods') {
        $this->tableName = $tableNmae;
        parent::__construct();
    }

    /**
     * 添加商品分类
     * @param array $data 添加的数据
     * @return array
     */
    public function addGoodsCat($data) {
        $catData = array(
            'name' => $data['name'],
            'name_mobile' => $data['name_mobile'],
            'parent_id' => $data['parent_id'],
            'level_num' => $this->getLevel($data['parent_id']),
            'add_time' => time()
        );
        $nameInfo = $this->findDataByField('name', $data['name']);
        if ($nameInfo) {
            return array('status' => -1, 'msg' => '此分类名称已存在!');
        }
        $mobileNameInfo = $this->findDataByField('name_mobile', $data['name_mobile']);
        if ($mobileNameInfo) {
            return array('status' => -1, 'msg' => '此手机分类名称已存在!');
        }
        $res = $this->addData($catData);
        if ($res) {
            return array('status' => 1, 'msg' => '添加成功!');
        } else {
            return array('status' => -1, 'msg' => '添加失败!');
        }
    }

    /**
     * 修改商品分类
     * @param array $data 修改的数据
     * @param int $catId 分类id
     * @return array
     */
    public function editGoodsCat($data, $catId) {
        $where = array(
            'cat_id' => $catId
        );
        $nameInfo = $this->findDataByField('name', $data['name']);
        if ($nameInfo && $nameInfo['cat_id'] != $catId) {
            return array('status' => -1, 'msg' => '此分类名称已存在!');
        }
        $mobileNameInfo = $this->findDataByField('name_mobile', $data['name_mobile']);
        if ($mobileNameInfo && $mobileNameInfo['cat_id'] != $catId) {
            return array('status' => -1, 'msg' => '此手机分类名称已存在!');
        }
        $catData = array(
            'name' => $data['name'],
            'name_mobile' => $data['name_mobile'],
            'parent_id' => $data['parent_id'],
            'level_num' => $this->getLevel($data['parent_id']),
        );
        $res = $this->saveData($where, $catData);
        if ($res) {
            return array('status' => 1, 'msg' => '更新成功!');
        } else {
            return array('status' => -1, 'msg' => '更新失败!');
        }
    }

    /**
     * 删除商品分类
     * @param int $catId 分类ID
     * @return array
     */
    public function delGoodsCat($catId) {
        $info = $this->findDataByField('parent_id', $catId);
        if ($info) {
            return array('status' => -1, 'msg' => '此分类下面有子分类不能删除!');
        }
        $res = $this->delData(array('cat_id' => $catId));
        if ($res) {
            return array('status' => 1, 'msg' => '删除成功!');
        } else {
            return array('status' => -1, 'msg' => '删除失败!');
        }
    }

    /**
     * 获取商品分类级别
     */
    public function getLevel($catId) {
        static $level = 1;
        $res = $this->findDataByField('cat_id', $catId);
        if ($res) {
            $level += 1;
            return $this->getLevel($res['parent_id']);
        } else {
            return $level;
        }
    }

    /**
     * 添加商品
     * @param array $data 添加的数据
     * @return array
     */
    public function addGoods($data) {
        $data['cat_id'] = 1;
        if (intVal($data['cat_id']) <= 0) {
            return array('status' => -1, 'msg' => '请选择分类!');
        }
        $nameInfo = $this->findDataByField('name', $data['name']);
        if ($nameInfo) {
            return array('status' => -1, 'msg' => '此商品名称已存在!');
        }

        $goodsData = array(
            'name' => $data['name'],
            'describe' => $data['describe'],
            'price' => $data['price'],
            'cat_id' => $data['cat_id'],
            'pv' => $data['pv'],
            'picture' => $data['picture'],
            'stock' => $data['stock'],
            'content' => $data['content'],
            'add_time' => time(),
            'is_top' => $data['is_top'],
            'statu' => $data['statu'],
            'type' => $data['type'],
            'type_id' => $data['type_id'],
            'keywords' => $data['keywords'],
            'goods_img' => implode(',', $data['goods_img']),
        );

        $res = $this->addData($goodsData);
        if ($res) {
            if (!$this->__after_insert($res)) {
                return array('status' => -1, 'msg' => '属性规格添加失败!');
            }
            return array('status' => 1, 'msg' => '添加成功!');
        } else {
            return array('status' => -1, 'msg' => '添加失败!');
        }
    }

    /**
     * 添加商品后 添加规格 和 添加属性
     * @param $data
     * @param $options
     * @return bool
     */
    protected function __after_insert($goods_id) {
        $items = I('post.item');
        $goods_price_store = D("GoodsPriceStore");
        $res = $goods_price_store->addGoodsPriceStore($items, $goods_id);
        if (!$res) {
            // 规格添加失败 ，就删除本商品
            $this->delete($goods_id);
            return false;
        }
        // 添加商品属性
        $attr_data = I("post.attr");
        $goods_attr = D('GoodsAttr');
        $res = $goods_attr->addGoodsAttr($attr_data, $goods_id);
        if (!$res) {
            M('GoodsPriceStore')->where(['goods_id' => $goods_id])->delete();
            $this->delete($goods_id);
            return false;
        }
        // 商品添加成功后，如果有规格就改变商品的库存
        $this->update_goods_store($goods_id);
        return true;
    }

    /**
     * 修改商品前，先修改商品 规格和属性
     * @param $data
     * @param $options
     */
    protected function __before_update($data, $goods_id) {
        // 先删除以前的规格 和 属性 ，再添加新的规格和属性
        $goods_price_store = new GoodsPriceStoreModel();
        $items = $data['item'];
        $res = $goods_price_store->savePriceStore($items, $goods_id);
        if (!$res) {
            return false;
        }
        // 添加商品属性
        $attr_data = $data['attr'];
        $goods_attr = new GoodsAttrModel();
        $res = $goods_attr->save_goods_attr($attr_data, $goods_id);
        if (!$res) {
            return false;
        }
        return true;
    }

    /** 添加商品后，如果有规格，商品的库存是所有规格库存的总和
     * @param $goods_id
     * @return bool
     */
    public function update_goods_store($goods_id) {
        $data = M('GoodsPriceStore')->where(['goods_id' => $goods_id])->getField('id,store_count');
        if (empty($data))
            return true;
        $count = array_sum($data);
        $sql = "update zfuwl_goods set stock=$count WHERE goods_id = $goods_id";
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        return $Model->execute($sql);
//        return M("goods")->where(['goods_id'=>$goods_id])->setField(['stock',$count]);
    }

    /**
     * 修改商品
     * @param array $data 修改的数据
     * @param int $goodsId 商品id
     * @return array
     */
    public function editGoods($data, $goodsId) {
        $data['cat_id'] = 1;

        $where = array(
            'goods_id' => $goodsId
        );

        $nameInfo = $this->findDataByField('name', $data['name']);
        if ($nameInfo && $nameInfo['goods_id'] != $goodsId) {
            return array('status' => -1, 'msg' => '此商品名称已存在!');
        }

        if (intVal($data['cat_id']) <= 0) {
            return array('status' => -1, 'msg' => '请选择分类!');
        }

        // 修改商品前，先修改规格和属性 -- 少杨修改
        $res = $this->__before_update($data, $goodsId);
        if (!$res) {
            return array('status' => -1, 'msg' => '此商品属性修改失败!');
        }

        $goodsData = array(
            'name' => $data['name'],
            'describe' => $data['describe'],
            'price' => $data['price'],
            'cat_id' => $data['cat_id'],
            'pv' => $data['pv'],
            'picture' => $data['picture'],
            'stock' => $data['stock'],
            'content' => $data['content'],
            'add_time' => time(),
            'is_top' => $data['is_top'],
            'statu' => $data['statu'],
            'type' => $data['type'],
            'type_id' => $data['type_id'],
            'keywords' => $data['keywords'],
            'goods_img' => implode(',', $data['goods_img']),
        );

        $res = $this->where($where)->save($goodsData);
        if ($res) {
            // 删除所有的缩略图
            $this->delete_thumb_img($goodsId);
            // 修改成功后，如果有规格就改变商品的库存
            $this->update_goods_store($goodsId);
            return array('status' => 1, 'msg' => '修改成功!');
        } else {
            return array('status' => -1, 'msg' => '修改失败!');
        }
    }

    /**
     * 删除商品前，先删除商品规格和属性
     * @param $goods_id
     * @return bool
     */
    protected function __before_delete($goods_id) {
        if (false === M("GoodsPriceStore")->where(['goods_id' => $goods_id])->delete()) {
            return false;
        }
        if (false === M("GoodsAttr")->where(['goods_id' => $goods_id])->delete()) {
            return false;
        }
        return true;
    }

    /**
     * 删除商品
     * @param int $goodsId 商品id
     * @return bool
     */
    public function delGoods($goodsId) {
        $res = $this->__before_delete($goodsId);
        if (!$res) {
            return array('status' => -1, 'msg' => '商品属性和规则删除失败!');
        }
        $where = array(
            'goods_id' => $goodsId
        );
        $res = $this->delData($where);
        if ($res) {
            return array('status' => 1, 'msg' => '删除成功!');
        } else {
            return array('status' => -1, 'msg' => '删除失败!');
        }
    }

    /**
     * 根据id查询分类
     */
    public function findGoodsCatById($catId) {
        $where = array(
            'cat_id' => $catId
        );
        return $this->where($where)->find();
    }

    //修改商品后删除 缩略图片
    private function delete_thumb_img($goodsId) {
        ///Public/upload/goods/thumb/53/goods_sub_thumb_53_0_400_400.png
        $path = "Public/upload/goods/thumb/$goodsId/";
        delFile($path);
    }

    /**
     * 格式化 规格数据
     * @param $spec_data
     * @return array
     */
    public static function getFormatSpec($spec_data)
    {
        $spec_data_tmp = [];
        foreach ($spec_data as $item) {
            $a1 = explode('_',$item['spec_key']);
            $a2 = explode(' ',$item['spec_name']);
            foreach ($a2 as $k => $v) {
                $a3 = explode(':',$v);
                $item_val = ['id'=>$a1[$k],'val'=>$a3[1]];
                if(!in_array($item_val, $spec_data_tmp[$a3[0]]))
                    $spec_data_tmp[$a3[0]][] = $item_val;
//                    array_push($spec_data_tmp[$a3[0]],$item_val);
            }
        }
        return $spec_data_tmp;
    }

}
