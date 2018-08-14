<?php

namespace Common\Model;

class CommentModel extends NewsBaseRelationModel {

    protected $_validate = array(
        array('content','require','留言内容不能为空'), //默认情况下用正则进行验证
        array('goods_rank','require','商品评分必须填写！'),
        array('service_rank','require','服务评分必须填写！'),
        array('deliver_rank','require','物流评分必须填写！'),
//        array('address','require','详细地址必须填写！'),
//        array('province','require','省份必须填写！'),
//        array('city','require','城市必须填写！'),
//        array('district','require','区必须填写！'),
//        array('twon','require','镇必须填写！'),
//        array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
//        array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
//        array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    );
    protected  $_auto = array(
                array('img','merge_img',3,'callback'),
                array('add_time','time',1,'function'),
    );
    protected $_link = array(
        'goods'=>array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'Goods',
            // 定义更多的关联属性
            'foreign_key'=>'goods_id',
            'mapping_key'=>'goods_id',
//            'mapping_fields '=>['goods_name'],
            'as_fields'=>'goods_name'
        ),
    );

    /**
     * 获取商品评论
     * @param $goodsId
     */
    public function get_goods_comment($post,$statistic=false)
    {
        if(is_numeric($post))$goodsId=$post;
        else $goodsId = $post['goods_id'];
        $where = ['goods_id'=>$goodsId];
        $this->get_comment_where($where,$post);// 获取where 条件
        // layui 的分页数据
        $data = $this->selectLayuiPage(['where'=>$where,'order'=>['add_time'=>'desc']],['page_num'=>2]);

        // 把img字符串 转 为 src 的数组
        foreach ($data['list'] as $k => $v) {
            if($v['img']){
                $data['list'][$k]['img'] = explode(',',$v['img']);
            }
            // 获取管理员回复

            $data['list'][$k]['admin_reply'] = $this->field('is_admin,content,add_time')->where(['parent_id'=>$v['id']])->select();
        }
//        dump($data);
        if($statistic){ // 获取评论统计信息
            $tmp = $this->field("goods_rank,count(id) as num")->where($where)->group("goods_rank")->select();
            $tmp = $this->statistic_comment_by_goods_rank($tmp);
            $data['statistic'] = $tmp;
            $data['statistic_per'] = $this->get_statistic_per($tmp);
        }

        return $data;
    }

    // 删除评论
    public function delete_comment($id)
    {
        $data = $this->find($id);
        if(empty($data))return $this->return_arr_error("评论不存在");
//        dump($data);
        // 先删除 管理员回复
        if(false === $this->where(['parent_id'=>$id])->delete())return $this->return_arr_error("管理员回复删除失败");
        // 删除留言图片
        // TODO  删除留言图片没有做
        // 删除评论
        if($this->delete($id) !== false)return $this->return_arr_success("评论删除成功");
        else return $this->return_arr_error("评论删除失败");
    }

    // 自动完成 图片的拼接
    protected function merge_img($img){
        if(empty($img))return "";
        return implode(',',$img);
    }

    /**
     * 添加商品评论
     * @param $goods_id
     * @param $order_id
     * @param $post
     */
    public function add_comment($goods_id, $order_id, $post,$user)
    {
        if(!$post = $this->create($post)){
            return $this->return_arr_error($this->getError());
        }
        $post['goods_id'] = $goods_id;
        $post['order_id'] = $order_id;
        // 获取用户信息
        $post['user_id'] = $user['user_id'];
        $post['username'] = $user['username'];
        $post['account'] = $user['account'];
        if($this->add($post)){
            // 添加评论成功后，更新订单商品 为已评论 和 订单是否已经评论
            $this->update_goods_order_status($goods_id,$order_id);
            return $this->return_arr_success("评论成功");
        }else $this->return_arr_error("评论失败");
//        dd($post);
    }

    // 跟新商品订单状态为已评价
    private function update_goods_order_status($goods_id, $order_id)
    {
        $order_goods_model = M("OrderGoods");
        // 更新当前的订单商品为 已评论
        $order_goods_model->where(['order_id'=>$order_id,'goods_id'=>$goods_id])->setField('is_comment',1);
        // 如果订单中所有的商品都已评论 更新订单的评论字段为已评论
//        if(!$order_goods_model->where(['order_id'=>$order_id,'is_comment'=>0])->count()){
//            M("Order")->where(['order_id'=>$order_id])->setField('is_comment',1);
//        }
        // 更新商品的评论数量
        M("Goods")->where(['goods_id'=>$goods_id])->setInc('comment_num',1);
    }

    // 统计 商品 好 中 差 的评论数
    private function statistic_comment_by_goods_rank($tmp)
    {
        // 差 中 好 bad  middle good
        $data = [];
        $data['bad'] = 0;
        $data['middle'] = 0;
        $data['good'] = 0;
        foreach ($tmp as $item) {
            switch ($item['goods_rank']) {

                case 1:
                    $data['bad'] = $item['num'];
                    break;
                case 2:
                    $data['bad'] += $item['num'];
                    break;
                case 3:
                    $data['middle'] = $item['num'];
                    break;
                case 4:
                    $data['good'] = $item['num'];
                    break;
                case 5:
                    $data['good'] += $item['num'];
                    break;
            }
        }
        return $data;
    }

    // 获取统计的百分比
    private function get_statistic_per($tmp)
    {
        $sum = 0;
        foreach ($tmp as $item) {
            $sum += $item;
        }
        $data = [];
        foreach ($tmp as $k => $v) {
            $data[$k] = round($v/$sum,2);
        }
        return $data;
    }

    // 获取comment where 条件
    private function get_comment_where(&$where,$post)
    {
        $status = $post['status'];
        if($status){
            $tmp = [];
            switch ($status) {
                case 'good':
                    $tmp = ['gt',3];
                    break;
                case 'middle':
                    $tmp = ['eq',3];
                    break;
                case 'bad':
                    $tmp = ['lt',3];
                    break;
            }
            if(!empty($tmp))
                $where['goods_rank'] = $tmp;
        }
    }

}
