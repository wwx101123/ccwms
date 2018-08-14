<?php

namespace Common\Model;

use Think\Model;
use Think\Page2;

class CommonModel extends Model {

    /**
     * ajax查询数据 带分页
     * @param array $where 查询条件
     * @param int $pageNum 查询多少条
     * @param array $order 排序
     * @return array 查询出来的数据加分页
     */
    public function selectAllListAjax($where, $order = array(), $pageNum = PAGE_LIMIT) {
        $count = $this->dataCount($where);
        $page = ajaxGetPage($count, $pageNum);

        $show = $page->show();
        $list = $this->where($where)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();

        return array('page' => $show, 'list' => $list);
    }
    /**
     * 查询数据 带分页
     * @param array $where 查询条件
     * @param int $pageNum 查询多少条
     * @param array $order 排序
     * @return array 查询出来的数据加分页
     */
    public function selectAllList($where, $order = array(), $pageNum = PAGE_LIMIT) {
        $count = $this->dataCount($where);
        $page = getPage($count, $pageNum);

        $show = $page->show();
        $list = $this->where($where)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();

        return array('page' => $show, 'list' => $list);
    }

    /**
     * 查询数据 带分页
     * @param array $where 查询条件
     * @param int $pageNum 查询多少条
     * @param array $order 排序
     * @return array 查询出来的数据加分页
     */
    public function selectAllListSum($condition, $pageNum = PAGE_LIMIT) {
        $where = $condition['where'];
        $order = $condition['order'];
        $field = $condition['field'];
        $count = $this->dataCount($where);
        $page = getPage($count, $pageNum);

        $show = $page->show();
//        if($page_flag == 2){
//           $page =  new Page2($count,$pageNum);
//            $show = $page->show();
//        }
        $list = $this->where($where)->field($field)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
        return array('page' => $show, 'list' => $list,'count'=>$count);
    }



    /**
     * 查询数据
     * @param array,string $where 查询条件
     * @param array $field 查询字段
     * @param bool $isGetField 是否直接返回
     * @param array $order 排序
     * @param array $num 数据条数
     * @return array 查询出来的数据
     */
    public function selectAll($where, $field, $isGetField = false, $order = array(), $num = '') {
        $field = implode(',', $field);
        if ($isGetField) {
            return $this->where($where)->order($order)->limit($num)->getField($field);
        }
        return $this->where($where)->order($order)->field($field)->limit($num)->select();
    }

    /**
     * 统计条数
     * @param array $where 条件
     * @return bool 数据数量
     */
    public function dataCount($where) {
        return $this->where($where)->count();
    }

    /**
     * 添加数据
     * @param array $data 要添加的数据
     * @return bool 添加状态
     */
    public function addData($data) {
        return $this->add($data);
    }

    /**
     * 更新数据
     * @param array $where 条件
     * @param array $data 数据
     * @return bool 修改状态
     */
    public function saveData($where, $data) {
        return $this->where($where)->save($data);
    }

    /**
     * 删除数据
     * @param array $where 条件
     * @return bool 删除状态
     */
    public function delData($where) {
        return $this->where($where)->delete();
    }

    /**
     * 更改数据为删除状态
     * @param $where 条件
     * @param $type 字段
     */
    public function saveStateForDel($where, $type = 'is_type') {
        return $this->where($where)->save(array($type => DEL_STATUS));
    }

    /**
     * 根据某个字段查询信息
     */
    public function findDataByField($field, $val)
    {
        $where = array(
            $field => $val
        );

        return $this->where($where)->find();
    }

}
