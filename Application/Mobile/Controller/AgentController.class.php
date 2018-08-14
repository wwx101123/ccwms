<?php

namespace Mobile\Controller;

use Zfuwl\Logic\AgentLogic;

class AgentController extends CommonController
{

    public $agentLogic;

    public function _initialize()
    {
        parent::_initialize();

        $this->agentLogic = new AgentLogic();
    }

    /**
     * 申请服务中心
     */
    public function upAgentAdd()
    {
        $user = $this->user;
        if(IS_POST){

            $post = I('post.');

            $res = $this->agentLogic->upAgentAdd($post, $user);
            if($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('upAgentAdd');
        }
    }

    /**
     * 申请记录
     */
    public function upLogList()
    {

        $list = M('agent_log')->where(['uid' => $this->user_id])->select();

        $this->assign('list', $list);

        $this->display('upLogList');
    }

    /**
     * 所有报单会员列表
     */
    public function agentUserList()
    {

        $condition = [
            'activate' => 2
        ];
        $count = M('users')->where($condition)->count();
        if(IS_AJAX) {

            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $list = M('users')->where($condition)->order('user_id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $list);

            $this->display('agentUserListAjax');
        } else {

            $this->assign('count', $count);

            $this->display('agentUserList');
        }
    }
}