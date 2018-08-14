<?php
namespace Mobile\Controller;

use Zfuwl\Logic\ActivateLogic;
class ActivateController extends CommonController
{

	# 激活逻辑处理
	private $activateLogic;

	public function _initialize()
	{
		parent::_initialize();

		$this->activateLogic = new ActivateLogic();
	}
    /**
     * 推荐人激活会员
     */
    public function tjrJhUser()
    {
    	if (IS_POST) {
    		$data = I('post.');
            $data['pay_code'] = 1;

            $res = $this->activateLogic->activateUser($data['id'], $data['type'], $this->user_id);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('User/userIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
            die;
    		$res = $this->activateLogic->userActivate($data['id'], $data['pay_code'], $this->user_id, 4);
    		if ($res['status'] == 1) {
    			$this->success('操作成功', U('User/userIndex'));
    			exit;
    		} else {
    			$this->error('操作失败,' . $res['msg']);
    		}
    	}
    }

    /**
     * 会员自己激活
     */
    public function userActivate()
    {

        $user = $this->user;
        if($user['activate'] == 1) {
            die("<script>history.go(-1);</script>");
        }

        $res = $this->activateLogic->activateUser($user['user_id'], 1, $user['user_id']);
        if ($res['status'] == 1) {
            $this->success('激活成功', U('User/userIndex'));
            exit;
        } else {
            $this->error($res['msg']);
        }
    }
    /**
     * 报单中心激活会员
     */
    public function bdrJhUser()
    {
    	if (IS_POST) {
    		$data = I('post.');
    		if($this->user['agent'] > 0) {
                $res = $this->activateLogic->userActivate($data['id'], $data['pay_code'], $this->user_id, 6);
            } else {
                $res = $this->activateLogic->userActivate($data['id'], $data['pay_code'], $this->user_id, 8);
            }
    		if ($res['status'] == 1) {
    			$this->success('操作成功', U('User/userIndex'));
    			exit;
    		} else {
    			$this->error('操作失败,' . $res['msg']);
    		}
    	}
    }

    /**
     * 激活会员详情
     */
    public function activateInfo()
    {
        $id = I('id', '', 'intval');

        $userInfo = M('users')->where(['user_id' => $id])->find();
        if(!$userInfo) {
            $this->error('会员不存在');
        }
        if($userInfo['activate'] == 1) {
            $this->error('此会员已激活');
        }

        $levelInfo = M('level')->where(['level_id' => $userInfo['level']])->find();

        $this->assign('info', $userInfo);
        $this->assign('levelInfo', $levelInfo);

        $this->display('activateInfo');
    }

    /**
     * 报单中心激活会员详情
     */
    public function activateInfoForAgent()
    {
        $id = I('id', '', 'intval');

        $userInfo = M('users')->where(['user_id' => $id])->find();
        if(!$userInfo) {
            $this->error('会员不存在');
        }
        if($userInfo['activate'] == 1) {
            $this->error('此会员已激活');
        }

        $levelInfo = M('level')->where(['level_id' => $userInfo['level']])->find();

        $this->assign('info', $userInfo);
        $this->assign('levelInfo', $levelInfo);

        $this->display('activateInfoForAgent');
    }
}