<?php

namespace Zfuwl\Model;


class GoodsPriceStoreModel extends CommonModel {

    /**
     * 给商品添加规格值
     * @param $items
     * @param $goods_id
     * @return bool
     */
    public function addGoodsPriceStore($items,$goods_id)
    {
        if(empty($items)){
            return true;
        }
        $items_data = [];
        foreach ($items as $k => $v) {
            if(empty($v['price']) || empty($v['store']))continue;
            $tmp = [];
            $tmp['price'] = $v['price'];
            $tmp['pv'] = $v['pv'];
            $tmp['store_count'] = $v['store'];
            $tmp['spec_name'] = $v['key_name'];
            $tmp['spec_key'] = $k;
            $tmp['goods_id'] = $goods_id;
            $items_data[] = $tmp;
        }
        if(!empty($items_data)){
            if(!$this->addAll($items_data)){
                return false;
            }
        }
        return true;
    }

    /**
     * 修改商品规格
     * @param array $item
     * @param $goods_id
     */
    public function savePriceStore($item=[], $goods_id)
    {
        $old_data = $this->where(['goods_id'=>$goods_id])->getField('spec_key,id,price,store_count,spec_name,pv');
        // 获取 要修改的 ids 和 要删除的 ids
        $del_ids = [];
        $edit_data = []; // 要修改的数据
        foreach ($old_data as $k => $v) {
            if(array_key_exists($k,$item)){
                // 如果存在 就 判断 价格和库存是否相等 如果相同 就不用修改
                if(($v['spec_name'] == $item[$k]['key_name']) && ($v['price'] == $item[$k]['price']) && ($v['store_count'] == $item[$k]['store']) && ($v['pv'] == $item[$k]['pv'])){

                }else{
                    // 不相同就需要修改
                    $edit_data_tmp= [];
                    $edit_data_tmp['id'] = $v['id'];
                    $edit_data_tmp['spec_name'] = $item[$k]['key_name'];
                    $edit_data_tmp['store_count'] = $item[$k]['store'];
                    $edit_data_tmp['price'] = $item[$k]['price'];
                    $edit_data_tmp['pv'] = $item[$k]['pv'];
                    $edit_data[] = $edit_data_tmp;
                }
                unset($item[$k]);// 如果存在就 不需要 添加
            }else{
                // 如果不存在 就要删除 这条价格和库存
                $del_ids[] = $v['id'];
            }
        }
        if($del_ids || $edit_data || $item){
            $this->startTrans();
            $res1=true;$res2=true;$res3=true;
            if(!empty($del_ids)){// 删除价格 库存记录
                $res1 = $this->where(['id'=>['in',$del_ids]])->delete();
            }
            if(!empty($edit_data)){// 修改 价格 库存记录
                $res2 =$this->save_price_store($edit_data);
            }
            if(!empty($item)){// 添加价格 库存记录
                $res3 = $this->addGoodsPriceStore($item,$goods_id);
            }

            if($res3 && $res2 && $res1){
                $this->commit();
                return true;
            }else{
                $this->rollback();
                return false;
            }
        }
        return true;
    }

    /**
     * 修改 价格 库存记录
     * @param $edit_data
     * @return bool
     */
    private function save_price_store($edit_data)
    {
        foreach ($edit_data as $item) {
            $tmp = $item;
            unset($tmp['id']);
            $res = $this->where(['id'=>$item['id']])->setField($tmp);
            if(!$res)return false;
        }
        return true;
    }
}