<?php


namespace Zfuwl\Model;

class GoodsAttrModel extends CommonRelationModel {

    protected $tableName ="goods_attr";
    protected $_link = array(
        'goodsAttribute'=>array(
            'mapping_type'      => self::BELONGS_TO,
            'class_name'        => 'goodsAttribute',
            // 定义更多的关联属性
            'foreign_key'=>'attr_id',
//            'mapping_key'=>'attr_id',
//            'mapping_fields '=>['name'],
            'as_fields'=>'name:attr_name'
        ),
//        'spec_item'=>array(
//            'mapping_type'      => self::HAS_MANY,
//            'class_name'        => 'SpecItem',
//            // 定义更多的关联属性
//            'foreign_key'=>'spec_id',
////                 'mapping_fields '=>'item',
////                'as_fields'=>'name:type_name'
//        ),
    );
    /**
     * 给商品添加规格值
     * @param $items
     * @param $goods_id
     * @return bool
     */
    public function addGoodsAttr($attr_data,$goods_id)
    {
        if(empty($attr_data))return true;
        $attr_arr = [];
        foreach ($attr_data as $k => $v) {
            if(!$v)continue;// 如果没有值，直接跳过
            $tmp = [];
            $tmp['goods_id'] = $goods_id;
            $tmp['attr_id'] = $k;
            $tmp['attr_val'] = $v;
            $attr_arr[] = $tmp;
        }
//        dd($attr_arr);
        if(!empty($attr_arr)){
            if(!M('GoodsAttr')->addAll($attr_arr)){
                return false;
            }
        }
        return true;
    }


    /**
     *保存属性
     * @param $attr_data
     * @param $goods_id
     * @return bool
     */
    public function save_goods_attr($attr_data, $goods_id)
    {
        // 获取商品原属性
        $old_attr = $this->where(['goods_id'=>$goods_id])->select();
        $del_ids = []; // 要修该的属性
        $edit_data = []; // 要修改的值
        foreach ($old_attr as $k => $v) {
            if(array_key_exists($v['attr_id'],$attr_data)){
                if($v['attr_val'] != $attr_data[$v['attr_id']]){
                    // 要修改的属性
                    $edit_data_temp = $v;
                    $edit_data_temp['attr_val'] = $attr_data[$v['attr_id']];
                    $edit_data[] = $edit_data_temp;
                }
                unset($attr_data[$v['attr_id']]);
            }else{
                $del_ids[] = $v['id'];
            }
        }
        $res1=true;$res2=true;$res3=true;
        if($del_ids || $edit_data || $attr_data){
            $this->startTrans();
            if(!empty($del_ids)){
                $res1 = $this->where(['id'=>['in',$del_ids]])->delete();
            }
            if(!empty($edit_data)){
                $res2 = $this->edit_goods_attr($edit_data);
            }
            if(!empty($attr_data)){
                $res3 = $this->addGoodsAttr($attr_data,$goods_id);
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
     * 修改要修改的属性值
     * @param $edit_data
     * @return bool
     */
    private function edit_goods_attr($edit_data)
    {
        foreach ($edit_data as $k => $v) {
            $tmp = $v;
            unset($tmp['id']);
            $res = $this->where(['id'=>$v['id']])->setField($tmp);
            if(!$res)return false;
        }
        return true;
    }
}