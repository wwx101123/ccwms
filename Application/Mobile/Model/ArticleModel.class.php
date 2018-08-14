<?php

namespace Member\Model;

use Think\Model;

class ArticleModel extends Model {

    /**
     * 获取文章列表
     */
    public function getArticleList($where, $pageNum = 10, $order = array()) {

        $count = $this->where($where)->count();

        $page = new \Think\AjaxPage($count, $pageNum);

        $list = $this->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();

        return array('list' => $list, 'page' => $page->show());
    }

    /**
     * 增加点击量
     */
    public function addClickNum($articleId) {
        $where = array(
            'article_id' => $articleId
        );

        return $this->where($where)->setInc('click', 1);
    }

    /**
     * 获取下一篇文章
     */
    public function getNextArticleById($articleId) {

        $where = array(
            'is_type' => 1,
            'article_id' => array('gt', $articleId)
        );

        return $this->where($where)->order('article_id asc')->limit(1)->find();
    }

    /**
     * 获取上一篇文章
     */
    public function getPrevArticleById($articleId) {
        $where = array(
            'is_type' => 1,
            'article_id' => array('lt', $articleId)
        );

        return $this->where($where)->order('article_id desc')->limit(1)->find();
    }

}
