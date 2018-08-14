<?php

namespace Mobile\Controller;

use Zfuwl\Logic\InvesLogic;

class InvesController extends CommonController
{


    public $investLogic;

    public function _initialize()
    {
        parent::_initialize();

        $this->investLogic = new InvesLogic();
    }

    /**
     * 会员加单
     */
    public function addOrder()
    {
        $user = $this->user;

        $levelInfo = M('level')->where(['level_id' => $user['level']])->field('amount')->find();
        if(IS_POST) {

            $post = I('post.');

            $post['money'] = $levelInfo['amount'];

            $res = $this->investLogic->addOrder($post, $user);
            if($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {

            $this->assign('levelInfo', $levelInfo);

            $this->assign('maxAddNum', $this->investLogic->determineAddNum($user));

            $this->display('addOrder');
        }
    }

    /**
     * 会员复投
     */
    public function recast()
    {
        $user = $this->user;

        $level = M('level')->where(['level_id' => $this->user['level']])->find();

        if(!judgeFt($user["user_id"])) {
            $this->error('暂时不能复投');
        }
        if(IS_POST) {

            $post = I('post.');

            $post['level_id'] = $level['level_id'];

            $res = $this->investLogic->recast($post, $user);
            if($res['status'] == 1) {
                $this->success($res['msg'], U('User/userIndex'));
            } else {
                $this->error($res['msg']);
            }
        } else {
//            $jsArr = array();
//            $levelList = M('level')->where(['statu' => 1, 'level_id' => ['egt', $user['level']]])->field('level_id, name_cn, amount')->select();
//            foreach($levelList as $v) {
//                $jsArr[] = [
//                    'value' => $v['level_id']
//                    ,'text' => $v['amount']
//                ];
//            }

//            $this->assign('jsStr', json_encode($jsArr));
            $this->assign('level', $level);
            $this->display('recast');
        }
    }
    /**
     * 会员投资
     */
    public function investment()
    {
        $user = $this->user;

        $level = M('level')->where(['level_id' => $this->user['level']])->find();

        if(IS_POST) {

            $post = I('post.');

            $post['level_id'] = $level['level_id'];

            $res = $this->investLogic->investment($post, $user);
            if($res['status'] == 1) {
                $this->success($res['msg'], U('User/userIndex'));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->assign('level', $level);
            $this->display('investment');
        }
    }

    /**
     * 会员投资记录
     */
    public function investLog()
    {

        $condition = [
            'uid' => $this->user_id
        ];
        I('is_type') ? $condition['statu'] = I('is_type') : false;
        $model = M('bonus_log');
        $startTime = strtotime(I('add_time'));
        $endTime = strtotime(I('end_time'));
        if ($startTime && $endTime) {
            $condition['add_time'] = array('between', array($startTime, $endTime + 86400));
        } elseif ($startTime > 0) {
            $condition['add_time'] = array('gt', $startTime);
        } elseif ($endTime > 0) {
            $condition['add_time'] = array('lt', $endTime);
        }
        if(IS_AJAX) {


            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 20;
            $sort_order = (I('order') ? I('order') : 'id') . ' ' . (I('sort') ? I('sort') : 'desc');

            $list = $this->investLogic->where($condition)->order($sort_order)->limit($p*$pSize.','.$pSize)->select();

            $this->assign('list', $list);

            $this->display('investLogAjax');
        } else {

            $this->display('investLog');
        }
    }
}