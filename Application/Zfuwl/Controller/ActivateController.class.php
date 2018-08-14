<?php

/**
 *  众福网络直销系统管理软件
 * ============================================================================
 * 版权所有 2015-2027 深圳市众福网络软件有限公司，并保留所有权利。
 * 网站地址: http://www.zfuwl.com   http://www.jiafuw.com
 * 联系方式：qq:1845218096 电话：15899929162
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author:  众福团队
 * Date:2016-12-10 21:30  154
 */

namespace Zfuwl\Controller;

class ActivateController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
    }

    public function acviteInfo() {
        if (IS_POST) {
            $data = I('post.');

            $user = M('users')->where(['user_id' => (int)$data['id']])->find();
            if(empty($user)) {
                $this->error('未获取到会员信息');
            }
            if($user['activate'] == 1) {
                $this->error('请勿重复激活');
            }
            $data = [
                'activate' => 1
                ,'jh_time' => time()
                ,'is_tz' => 1
                ,'jh_type' => 3
                ,'jhr_id' => 0
            ];
            $res = M('users')->where(['user_id' => $user['user_id']])->save($data);
            if($res) {
                userAction($user['user_id'], '管理员激活', 3);
                $this->success('激活成功');
            } else {
                $this->error('激活失败');
            }
            die;

            $data['istype'] = 1;
            if ($data['istype'] == 1) {
                # 实单激活会员
                if ($data['isavtivate'] <= 0) {
                    $this->error('请选择扣款方案');
                }
                $res = $this->realActivate($data);
            } elseif ($data['istype'] == 2) {
                # 空单激活会员
                $res = $this->emptyActivate($data);
            } elseif ($data['istype'] == 3) {
                # 回填单激活会员
                $res = $this->backActivate($data);
            }
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $userInfo = M('users')->where(array('user_id' => I('user_id')))->field('user_id,level,account,reg_time')->find();
            $this->assign('userlevel', M('level')->where(array('level_id' => $userInfo['level']))->find());
            $this->assign('userInfo', $userInfo);
            $this->display('acviteInfo');
        }
    }

    /**
     * 空单激活会员
     */
    public function emptyActivate($data) {
        if (IS_POST) {
            $model = new \Zfuwl\Logic\ActivateLogic();
            $res = $model->emptyActivate($data['user_id']);
            return $res;
        }
    }

    /**
     * 回填单激活会员
     */
    public function backActivate($data) {
        if (IS_POST) {
            $model = new \Zfuwl\Logic\ActivateLogic();
            $res = $model->backActivate($data['user_id']);
            return $res;
        }
    }

    /**
     * 实单激活会员
     */
    public function realActivate($data) {
        if (IS_POST) {
            $model = new \Zfuwl\Logic\ActivateLogic();
            $res = $model->userActivate($data['user_id'], $data['isavtivate']);
            return $res;
        }
    }

    /**
     * 管理员实单激活 扣公司费用  计算播出比
     */
    public function adminRealActivate() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ActivateLogic();
            $res = $model->userActivate($data['id'], 0, '', session('admin_name'));
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

}
