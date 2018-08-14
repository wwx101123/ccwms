<?php

namespace Mobile\Controller;

class HelpController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
//        $this->assign('helpCatInfo', M('help_cat')->where("statu=1")->cache('helpCatInfo')->getField('cat_id,title'));
    }

    public function helpIndex()
    {

        $helpCatList = M('help_cat')->where(array('statu' => 1))->select();

        $helpList = array();
        foreach ($helpCatList as $v) {
            $helpList[$v['cat_id']] = M('help')->where(array('cat_id' => $v['cat_id'], 'statu' => 1))->select();
        }

        $this->assign('helpList', $helpList);
        $this->assign('helpCatList', $helpCatList);

        $this->display('helpIndex');
    }

    public function detail()
    {
        $id = I('get.id');

        $info = M('help')->where(array('id' => $id))->find();
        if(!$info || $info['statu'] != 1) {
            $this->error('此文章已删除');
        }

        $this->assign('info', $info);

        $this->display('detail');
    }

}
