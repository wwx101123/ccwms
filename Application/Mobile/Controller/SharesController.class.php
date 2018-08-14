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
 * Date:2016-12-10 20:27  203
 */

namespace Mobile\Controller;

use Think\AjaxPage;

class SharesController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('sharesStatus', sellBuyStatus());
        $this->assign('sharesLogType', sharesLogType());
        $this->assign('sharesList', M(NUOYILIANNAME.'.shares')->cache('shares')->getField('id,name_cn'));


        $this->sharesLogic = new \Zfuwl\Logic\SharesLogic();
    }

    /**
     *  页面合成
     */
    public function tradeHall() {
        $this->assign('userShares', M(NUOYILIANNAME.'.shares_user')->where(array('user_id' => $this->user_id, 'shares_id' => 1, 'pt' => PTVAL))->find());
        $this->display('tradeHall');
    }

    /**
     * 发送 a 转 b
     */
    public function changeAddA() {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->sharesLogic->changeAddA($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display();
        }
    }

    /**
     * 发送 记录
     */
    public function changeAddALog() {
        $condition = array();
        $condition['uid'] = $this->user_id;
        $condition['is_type'] = 1;
        $count = M('shares_change_log')->where($condition)->count();
        $Page = ajaxGetPage($count, 10);
        $result = M('shares_change_log')->where($condition)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page->show());
        $this->assign('tradelist', $result);
        if (IS_AJAX && $_GET['is_list'] == 1) {
            $this->display('changeAddALogAjaxList');
            die();
        } else if (IS_AJAX) {
            $this->display('changeAddALogAjax');
            die();
        }
        $this->display('changeAddALog');
    }

    /**
     * 接收 记录
     */
    public function receiveALog() {
        $condition = array();
        $condition['to_uid'] = $this->user_id;
        $condition['is_type'] = 1;
        $count = M('shares_change_log')->where($condition)->count();
        $Page = ajaxGetPage($count, 10);
        $result = M('shares_change_log')->where($condition)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page->show());
        $this->assign('tradelist', $result);
        if (IS_AJAX && $_GET['is_list'] == 1) {
            $this->display('receiveALogAjaxList');
            die();
        } else if (IS_AJAX) {
            $this->display('receiveALogAjax');
            die();
        }
        $this->display('receiveALog');
    }

    /**
     * 转出  从当前平台 转到另外的平台
     */
    public function changeAddB() {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->sharesLogic->changeAddB($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display();
        }
    }

    /**
     * 发送 记录
     */
    public function changeBddALog() {
        $condition = array();
        $condition['uid'] = $this->user_id;
        $condition['is_type'] = 2;
        $count = M('shares_change_log')->where($condition)->count();
        $Page = ajaxGetPage($count, 10);
        $result = M('shares_change_log')->where($condition)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page->show());
        $this->assign('tradelist', $result);
        if (IS_AJAX && $_GET['is_list'] == 1) {
            $this->display('changeAddBLogAjaxList');
            die();
        } else if (IS_AJAX) {
            $this->display('changeAddBLogAjax');
            die();
        }
        $this->display('changeAddBLog');
    }

    /**
     * 接收 记录
     */
    public function receiveBLog() {
        $condition = array();
        $condition['to_uid'] = $this->user_id;
        $condition['is_type'] = 2;
        $count = M('shares_change_log')->where($condition)->count();
        $Page = ajaxGetPage($count, 10);
        $result = M('shares_change_log')->where($condition)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page->show());
        $this->assign('tradelist', $result);
        if (IS_AJAX && $_GET['is_list'] == 1) {
            $this->display('receiveBLogAjaxList');
            die();
        } else if (IS_AJAX) {
            $this->display('receiveBLogAjax');
            die();
        }
        $this->display('receiveBLog');
    }

    /**
     * 股票大厅
     */
    public function sharesIndex() {
        $shares = $this->shares;
//        $this->assign('buylist', M('shares_buy')->where(array('user_id' => $this->user_id))->order('id desc')->select());
//        $this->assign('selllist', M('shares_sell')->where(array('user_id' => $this->user_id))->order('id desc')->select());
//        $this->assign('tradelist', M('shares_trade')->where("sell_uid = {$this->user_id} OR buy_uid = {$this->user_id}")->order('trade_id desc')->select());
//        $this->assign('aboutlist', M('shares_about')->where("is_type = 1")->limit(10)->order('add_time desc')->select());
        $model = M(NUOYILIANNAME.'.shares_price');
        $condition = array();
        $list = $model->where($condition)->limit(7)->select();
        $this->assign('list', $list);
        $sharesConfig = M(NUOYILIANNAME.'.shares_config')->where(array('shares_id' => 1))->find();
//
//        # 验证 买家账户余额 是否充足
//        $userMoney = usersMoney($this->user_id, $sharesConfig['money_id'], 1);
//        #账户余额 可购买量
//        $userBuyNum = intval($userMoney / ($shares['now_price'] + ($shares['now_price'] * $sharesConfig['buy_fee'] / 100)));
//        $this->assign('sharesTotal', $userBuyNum);
        # 诺一链价格
        $this->assign('shares', M(NUOYILIANNAME.'.shares')->where('is_type = 1')->find());
        $this->assign('sharesConfig', $sharesConfig);
        $this->display('sharesIndex');
    }

    /**
     * 买家列表
     */
    public function buyIndex() {
        $condition = array();
        I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
        I('is_type') ? $condition['is_type'] = I('is_type') : false;
        $add_time = strtotime(urldecode(trim(I('start_time'))));
        $out_time = strtotime(urldecode(trim(I('end_time'))));
        if ($add_time && $out_time) {
            $condition['zf_time'] = array('between', array($add_time, $out_time));
        } elseif ($add_time) {
            $condition['zf_time'] = array('egt', $add_time);
        } elseif ($out_time) {
            $condition['zf_time'] = array('elt', $out_time);
        }
        $condition['buy_uid'] = $this->user_id;
        $condition['pt'] = PTVAL;
        $sort_order = I('order_by', 'trade_id') . ' ' . I('sort', 'desc');
        $count = M(NUOYILIANNAME.'.shares_trade')->where($condition)->count();
        $Page = ajaxGetPage($count, 10);
        $result = M(NUOYILIANNAME.'.shares_trade')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page->show());
        $this->assign('tradelist', $result);
        if (IS_AJAX && $_GET['is_list'] == 1) {
            $this->display('buyIndexAjaxList');
            die();
        } else if (IS_AJAX) {
            $this->display('buyIndexAjax');
            die();
        }
        $this->display('buyIndex');
    }

    /**
     * 卖家列表
     */
    public function sellIndex() {
        $condition = array();
        I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
        I('is_type') ? $condition['is_type'] = I('is_type') : false;
        $add_time = strtotime(urldecode(trim(I('start_time'))));
        $out_time = strtotime(urldecode(trim(I('end_time'))));
        if ($add_time && $out_time) {
            $condition['zf_time'] = array('between', array($add_time, $out_time));
        } elseif ($add_time) {
            $condition['zf_time'] = array('egt', $add_time);
        } elseif ($out_time) {
            $condition['zf_time'] = array('elt', $out_time);
        }
        $condition['sell_uid'] = $this->user_id;
        $condition['pt'] = PTVAL;
        $sort_order = I('order_by', 'trade_id') . ' ' . I('sort', 'desc');
        $count = M(NUOYILIANNAME.'.shares_trade')->where($condition)->count();
        $Page = ajaxGetPage($count, 10);
        $result = M(NUOYILIANNAME.'.shares_trade')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page->show());
        $this->assign('tradelist', $result);
        if (IS_AJAX && $_GET['is_list'] == 1) {
            $this->display('sellIndexAjaxList');
            die();
        } else if (IS_AJAX) {
            $this->display('sellIndexAjax');
            die();
        }
        $this->display('sellIndex');
    }

    /**
     * 交易走势图
     */
    public function sharesFigure() {
        $model = M('shares_day');
        $condition = array();
        $list = $model->where($condition)->limit(7)->order('id desc')->select();
        $this->assign('list', $list);
        $this->display('sharesFigure');
    }

    /**
     *  买入大厅
     */
    public function sharesBuy() {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->sharesLogic->sharesBuy($this->user_id, $data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg']);
            }
        } else {
            $this->display('sharesBuy');
        }
    }

    /**
     * 买入撤回
     */
    public function sharesBuyReturn() {
        $data = I('');
        if ($data) {
            $res = $this->sharesLogic->sharesBuyReturn($this->user_id, $data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 卖出大厅
     */
    public function sharesSell() {
        if (IS_POST) {
            $data = I('post.');

            $res = $this->sharesLogic->sharesSell($this->user_id, $data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg']);
            }
        } else {
            $this->display('sharesSell');
        }
    }

    /**
     * 卖出撤回
     */
    public function sharesSellReturn() {
        $data = I('');
        if ($data) {
            $res = $this->sharesLogic->sharesSellReturn($this->user_id, $data);
            if ($res['status'] == 1) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 买入股票  列表
     */
    public function sharesBuyList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('status') ? $condition['status'] = I('status') : false;
            $add_time = strtotime(urldecode(trim(I('start_time'))));
            $out_time = strtotime(urldecode(trim(I('end_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $condition['user_id'] = $this->user_id;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_buy')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_buy')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $sharesIdArr = getArrColumn($result, 'shares_id');
            if ($sharesIdArr) {
                $this->assign('sharesList', M('shares')->where("id in (" . implode(',', $sharesIdArr) . ")")->getField('id,shares_name'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesBuyListAjax');
            die;
        }
        $this->display('sharesBuyList');
    }

    /**
     * 卖出股票 列表
     */
    public function sharesSellList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('status') ? $condition['status'] = I('status') : false;
            $add_time = strtotime(urldecode(trim(I('start_time'))));
            $out_time = strtotime(urldecode(trim(I('end_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $condition['user_id'] = $this->user_id;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_sell')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_sell')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $sharesIdArr = getArrColumn($result, 'shares_id');
            if ($sharesIdArr) {
                $this->assign('sharesList', M('shares')->where("id in (" . implode(',', $sharesIdArr) . ")")->getField('id,shares_name'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesSellListAjax');
            die;
        }
        $this->display('sharesSellList');
    }

    /**
     * 股票变动明细列表
     */
    public function sharesLogList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $add_time = strtotime(urldecode(trim(I('start_time'))));
            $out_time = strtotime(urldecode(trim(I('end_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $condition['user_id'] = $this->user_id;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $sharesIdArr = getArrColumn($result, 'shares_id');
            if ($sharesIdArr) {
                $this->assign('sharesList', M('shares')->where("id in (" . implode(',', $sharesIdArr) . ")")->getField('id,shares_name'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesLogListAjax');
            die;
        }
        $this->display('sharesLogList');
    }

    //    股票资讯列表
    public function sharesNewsList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $add_time = strtotime(urldecode(trim(I('start_time'))));
            $out_time = strtotime(urldecode(trim(I('end_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_about')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_about')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('articleList', $result);
            $this->display('sharesNewsListAjax');
            die();
        }
        $this->assign('count', 5);
        $this->assign('articleList', M('shares_about')->order(['add_time' => 'desc'])->limit(0, 5)->select());

        $this->display();
    }

    /**
     * 股票资讯 详情
     */
    public function sharesDetail() {
//        die();
        $articleId = I('id', '', 'intVal');
        $order = array('add_time' => 'desc');

        $articleModel = D('shares_about');
//        $articleModel->getNextArticleById();
        $where = array(
            'is_type' => 1
        );
        $prevInfo = $articleModel->where(['id' => ['gt', $articleId]])->order('id asc')->limit(1)->find();
        $nextInfo = $articleModel->where(['id' => ['lt', $articleId]])->order('id desc')->limit(1)->find();
        $info = $articleModel->find($articleId);
        $this->assign('info', $info);

        $this->assign([
            'prevInfo' => $prevInfo,
            'nextInfo' => $nextInfo,
        ]);
        $this->display();
    }

    /**
     * 诺一链明细
     */
    public function userSharesLog() {

        $this->assign('partnerPrice', M('users_partner_log')->where(array('uid' => $this->user_id))->sum('price')); //合伙人 购买金额
        $this->assign('partnerGiveNum', M('users_partner_log')->where(array('uid' => $this->user_id))->sum('shares_num')); //合伙人 赠送数量
        $this->assign('shopPrice', M('users_invest_log')->where(array('type' => 1, 'uid' => $this->user_id))->sum('price')); //诺购区 购买金额
        $this->assign('shopNum', M('users_invest_log')->where(array('type' => 1, 'uid' => $this->user_id))->sum('give_num')); //诺购区 赠送数量
        $this->assign('regPrice', M('users_invest_log')->where(array('type' => 2, 'uid' => $this->user_id))->sum('price')); //排单区 购买金额
        $this->assign('regNum', M('users_invest_log')->where(array('type' => 2, 'uid' => $this->user_id))->sum('give_num')); //排单区 赠送数量
        $this->assign('buyPrice', M('users_money_log')->where(array('is_type' => 119, 'user_id' => $this->user_id))->sum('money')); //诺益区 购买金额
        $this->assign('buyNum', M('shares_log')->where(array('is_type' => 2, 'user_id' => $this->user_id))->sum('money')); //诺益区 赠送数量

        $condition = array();
        I('money_id') ? $condition['money_id'] = I('money_id') : false;
        $condition['user_id'] = $this->user_id;
        $count = M('shares_log')->where($condition)->count();

        $Page = ajaxGetPage($count, 10);
        $result = M('shares_log')->where($condition)->order('zf_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('count', $count);
        $this->assign('list', $result);
        if (IS_AJAX) {
            $this->display('userSharesLogAjax');
            die;
        }
        $this->display('userSharesLog');
    }

    public function logs() {
        $condition = array();
        I('money_id') ? $condition['money_id'] = I('money_id') : false;
        I('is_type') ? $condition['is_type'] = I('is_type') : false;
        $condition['user_id'] = $this->user_id;
        $condition['pt'] = PTVAL;
        $count = M(NUOYILIANNAME.'.shares_log')->where($condition)->count();

        $Page = ajaxGetPage($count, 10);
        $result = M(NUOYILIANNAME.'.shares_log')->where($condition)->order('zf_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('count', $count);
        $this->assign('list', $result);
        if (IS_AJAX) {
            $this->display('logsAjax');
            die;
        }
        $this->display('logs');
    }

}
