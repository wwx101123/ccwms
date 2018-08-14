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

namespace Mobile\Controller;

class TransformController extends CommonController {

    public $moneyLogic;

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
    }

    /**
     * 会员钱包转换
     */
    public function transformAdd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\TransformLogic();
            $res = $model->transFormAdd($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U('Transform/transformLog'));
                exit;
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('transformAdd');
        }
    }

    /**
     * 钱包转换记录
     */
    public function transformIndex() {
        $condition = array();
        $condition['uid'] = $this->user_id;
        $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
        $count = M('money_transform_log')->where($condition)->count();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('money_transform_log')->where($condition)->order($sort_order)->limit($p * $pSize, $pSize)->select();

            $this->assign('list', $result);
            $this->display('transformIndexAjax');
            die;
        }
        $this->assign('count', $count);
        $this->display('transformIndex');
    }

    /**
     * 根据转出钱包获取转入钱包
     */
    public function getZrMoneyByZcMoney() {
        if (IS_POST) {
            $zcId = I('post.id', '', 'intval');
            $transMoneyList = M('money_transform')->where(array('statu' => 1, 'money_id' => $zcId))->select();
            foreach ($transMoneyList as &$v) {
                $v['info'] = moneyList($v['type_id']) . '余额 ' . usersMoney($this->user_id, $v['type_id']);
                $v['zr_money_name'] = moneyList($v['type_id']);
                $v['zc_money_name'] = moneyList($v['money_id']);
            }

            if ($transMoneyList) {
                $this->success($transMoneyList);
            } else {
                $this->error('获取转入钱包失败');
            }
        }
    }

}
