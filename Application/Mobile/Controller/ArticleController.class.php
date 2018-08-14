<?php

namespace Mobile\Controller;

class ArticleController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
//        $this->assign('catInfo', M('article_cat')->where("statu=1")->cache('catInfo')->getField('cat_id,title'));
    }

    /**
     * 文章列表
     */
    public function index()
    {
        $articleCatList = M('article_cat')->where(array('statu' => 1))->field('cat_id,title')->select();

        $articleList = array();

        foreach ($articleCatList as $v) {
            $articleList[$v['cat_id']] = M('article')->where(array('cat_id' => $v['cat_id'], 'statu' => 1, 'cn' => 1))->select();
        }

        $this->assign('articleList', $articleList);
        $this->assign('articleCatList', $articleCatList);

        $this->display('index');
    }

    /**
     * 文章详情
     */
    public function detail()
    {
        $id = I('get.id');

        $info = M('article')->where(array('id' => $id))->find();
        if(!$info || $info['statu'] != 1) {
            $this->error('此文章已删除');
        }

        $this->assign('info', $info);

        $this->display('detail');
    }

    public function kfIndex()
    {
        $this->display('kfIndex');
    }

}
