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
 * Date:2016-12-10 17:31 358
 */

namespace Zfuwl\Controller;

class SharesController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneylist', M('money')->where("statu=1")->cache('money')->getField('money_id,name_cn'));
        $this->assign('sharesType', sharesType());
        $this->assign('sharesLogType', sharesLogType());
        $this->assign('sharesIssueIsType', sharesIssueIsType());
        $this->assign('sellBuyStatus', sellBuyStatus());
        $this->assign('sharesRiseType', sharesRiseType()); // 涨价规则
        $this->assign('sharesPriceType', sharesPriceType()); // 价格变动说明
        $this->assign('sharesList', M(NUOYILIANNAME.'.shares')->cache(true)->getField('id, name_cn'));
    }

    /**
     * 修改股票新的交易价格
     */
    public function editNowPrice() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->editNowPrice($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Shares/sharesList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        }
    }

    /**
     * 股票列表
     */
    public function sharesList() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesListAjax');
            die;
        }
        $this->display('sharesList');
    }

    /**
     * 新增股票
     */
    public function addShares() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->addShares($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Shares/sharesList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('addShares');
        }
    }

    /**
     * 修改股票
     */
    public function editShares() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->addShares($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Shares/sharesList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M(NUOYILIANNAME.'.shares')->where(array('id' => I('id')))->find());
            $this->display('addShares');
        }
    }

    /**
     * 修改股票状态
     */
    public function saveShares() {
        $val = I('val') == 1 ? 2 : 1;
        $data[I('fieldVal')] = $val;
        $where['id'] = I('id');
        $res = M(NUOYILIANNAME.'.shares')->where($where)->save($data);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 删除股票
     */
    public function delShares() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('shares')->where($where)->save(array('is_type' => DEL_STATUS));
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
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
     * 会员股票余额
     */
    public function sharesUserList() {
//        $this->assign('levelList', M('users_level')->getField('level_id, name_cn'));
//        $this->assign('leaderList', M('users_leader')->getField('id, name_cn'));
//        $this->assign('agentList', M('users_agent')->getField('agent_id, name_cn'));
        if (IS_AJAX) {
            $condition = $where = array();
            I('price') && $condition['price'] = array('like', '%' . trim(I('price') . '%'));
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('account') ? $condition['user_id'] = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if (I("level_id") || $startTime || $endTime || I('account') || I('leader_id') || I('agent_id')) {
                if ($startTime && $endTime) {
                    $where['reg_time'] = array('between', array($startTime, $endTime));
                } elseif ($startTime > 0) {
                    $where['reg_time'] = array('gt', $startTime);
                } elseif ($endTime > 0) {
                    $where['reg_time'] = array('lt', $endTime);
                }

                I('level_id') && $where['level'] = I('level_id');
//                I('account') && $where['account'] = I('account');
                I('leader_id') && $where['leader'] = I('leader_id');
                I('agent_id') && $where['agent'] = I('agent_id');
                $userIdArr1 = M('users')->where($where)->getField('account, user_id');
                $userIdArr1 && $condition['user_id'] = array('in', implode(',', $userIdArr1));
            }
            $condition['pt'] = PTVAL;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_user')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_user')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account,level,reg_time,agent,leader'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesUserListAjax');
            die;
        }
        $this->display('sharesUserList');
    }

    /**
     * 对会员股票做修改
     */
    public function handSharesUser() {
        if (IS_POST) {
            $res = handSharesUser($_POST);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Shares/sharesUserList'));
            }
        } else {
            $this->display('handSharesUser');
        }
    }

    /**
     * 对会员股票做修改
     */
    public function editUserShares() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->editUserShares($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Shares/sharesUserList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $info = M('shares_user')->where(array('id' => I('id')))->find();
            $this->assign('user', M('users')->where(array('user_id' => $info['user_id']))->field('account')->find());
            $this->assign('info', $info);
            $this->display('editUserShares');
        }
    }

    /**
     * 手动拆分会员手中的股票
     */
    public function sharesSplitInfo() {
        if (IS_POST) {
            $res = SharesSplitInfo($_POST);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U('Shares/sharesUserList')); //拆分成功后  跳到 拆分记录页面
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $date = I('');
            if ($date) {
                $info = M()->table("__SHARES_USER__ as u")->join('__SHARES__ as s on u.shares_id = s.id')->where("u.user_id = '{$date['user_id']}' and s.is_type = 1")->select();
            } else {
                $info = M('shares')->where(array('is_type' => 1))->select();
                foreach ($info as &$v) {
                    $v['money'] = floatval(M('shares_user')->where(array('shares_id' => $v['id']))->sum('total'));
                    $v['num'] = floatval(M('shares_user')->where(array('shares_id' => $v['id']))->count());
                }
            }
//            if ($info['split_auto'] == 1) {
//                $this->success('当前为自动拆分模式，不支持手动拆分', U('Shares/sharesUserList'));
//            }
            $this->assign('info', $info);
            $this->assign('user_id', $date['user_id']);
            $this->display('sharesSplitInfo');
        }
    }

    /**
     * 会员股票持有数倍增
     */
    public function multiplication() {
        $model = M('user_shares');
        $condition = array();
        $user_id = I('user_id');
        if ($user_id) {
            $condition['user_id'] = $user_id;
        } else {

        }
        $list = $model->where($condition)->select();

        if (!$list) {
            $this->error('未找到会员信息');
        }
        $sharesList = M('shares')->where(array('is_type' => 1))->select();
        $i = 0;
        foreach ($list as $val) {
            $user = M('users')->where(array('user_id' => $val['user_id']))->field('account')->find();
            $text = '倍增会员' . $user['account'];
            foreach ($sharesList as $v) {
                if ($val['shares' . $v['id']] > 0) {
                    $data['shares' . $v['id']] = $val['shares' . $v['id']] * $v['cf_per'] + $val['shares' . $v['id']];
                    $text .= '--' . $v['shares_name'] . '倍增前' . $val['shares' . $v['id']] . '倍增后' . $data['shares' . $v['id']];
                }
                //倍增后 全部自动挂卖出去 并删除以前的所有购买记录
                $map['shares_id'] = $v['id'];
                $map['user_id'] = $val['user_id'];
                $map['zf_time'] = time();
                $map['num'] = $data['shares' . $v['id']];
                $map['out_num'] = $data['shares' . $v['id']];
                $map['price'] = $v['price'];
                $map['total'] = $v['price'] * $data['shares' . $v['id']];
                $map['poundage'] = $v['poundage'];
                $map['status'] = 1;
                if ($map['num'] > 0) {
                    if (M('user_shares_trade')->add($map)) {
                        $data['shares' . $v['id']] = 0;
                        M('user_shares')->where(array('user_id' => $val['user_id']))->save($data);
                        M('user_shares_add')->where(array('user_id' => $val['user_id'], 'shares_id' => $v['id']))->delete();
                    }
                }
                // 倍增后 处理结束
            }
            // $res = $model->where(array('user_id' => $val['user_id']))->save($data);
            unset($data);
            $i++;
        }
        if ($i > 0) {
            adminLog($text, __ACTION__);
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 管理员卖出会员股票
     */
    public function adminSellUserShares() {
        if (IS_POST) {
            $res = adminSellUserShares($_POST);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg']);
            }
        }
    }

    /**
     * 股票出售记录
     */
    public function sellSharesOutList() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['user_id'] = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
//            I('account') ? $condition['user_id'] = I('account') : false;
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('status') ? $condition['status'] = I('status') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $condition['pt'] = PTVAL;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_sell')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_sell')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sellSharesOutListAjax');
            die;
        }
        $this->display('sellSharesOutList');
    }

    /**
     * 股票买入记录
     */
    public function buySharesAddList() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['user_id'] = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
//            I('account') ? $condition['user_id'] = I('account') : false;
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('status') ? $condition['status'] = I('status') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $condition['pt'] = PTVAL;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_buy')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_buy')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('buySharesAddListAjax');
            die;
        }
        $this->display('buySharesAddList');
    }

    /**
     * 价格变动管理
     */
    public function sharesPriceChangeList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_price')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_price')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesPriceChangeListAjax');
            die;
        }
        $this->display('sharesPriceChangeList');
    }

    /**
     * 手动调节股票价格
     */
    public function sharesPriceChangeInfo() {
        if (IS_POST) {
            $res = sharesPriceChangeInfo($_POST['shares_id'], $_POST['after_price'], 3);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Shares/sharesPriceChangeList'));
            }
        } else {
            $this->assign('sharesPricesharesList', M('shares')->where(array('is_type' => 1))->select());
            $this->display('sharesPriceChangeInfo');
        }
    }

    /**
     * 发行股票
     */
    public function sharesIssueAdd() {
        if (IS_POST) {
            $res = sharesIssueAdd($_POST);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Shares/sharesIssueList'));
            }
        } else {
            $this->display('sharesIssueAdd');
        }
    }

    /**
     * 撤回发行股票
     */
    public function sharesIssueReturn() {
        $id = I('id');
        $issue = M('shares_issue')->where(array('id' => $id))->find();
        if ($id && $issue) {
            if ($issue['is_type'] != 1) {
                $this->error('操作失败，请勿重复提交');
            } else {
                $model = new \Think\Model();
                $model->startTrans();
                $num = $issue['issue_num'] - $issue['out_num'];
                $res = M(NUOYILIANNAME.'.shares_issue')->where(array('id' => $id))->save(array('is_type' => 3, 'return_time' => time(), 'return_num' => $num));
                $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $issue['shares_id']))->setDec('out_num', $num);
                if ($res && $info) {
                    $model->commit();
                    $this->success('操作成功');
                } else {
                    $model->rollback();
                    $this->error('操作失败');
                }
            }
        }
    }

    /**
     * 发行记录管理
     */
    public function sharesIssueList() {
        if (IS_AJAX) {
            $condition = array();
//            I('money_name') && $condition['money_name'] = array('like', '%' . trim(I('money_name') . '%'));
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_issue')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_issue')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesIssueListAjax');
            die;
        }
        $this->display('sharesIssueList');
    }

    /**
     * 拆分记录管理
     */
    public function sharesSplitList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $sort_order = I('order_by', 'split_id') . ' ' . I('sort', 'desc');
            $count = M('shares_split')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_split')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesSplitListAjax');
            die;
        }
        $this->display('sharesSplitList');
    }

    /**
     * 会员股票变动明细列表
     */
    public function sharesLogList() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['user_id'] = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $condition['pt'] = PTVAL;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'user_id');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesLogListAjax');
            die;
        }
        $this->display('sharesLogList');
    }

    /**
     * 涨价规则设置
     */
    public function sharesRiseList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M(NUOYILIANNAME.'.shares_rise')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M(NUOYILIANNAME.'.shares_rise')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesRiseListAjax');
            die;
        }
        $this->display('sharesRiseList');
    }

    /**
     * 累积交易数量变动日志
     */
    public function riseLog() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            $add_time = strtotime(urldecode(trim(I('add_time'))));
            $out_time = strtotime(urldecode(trim(I('out_time'))));
            if ($add_time && $out_time) {
                $condition['zf_time'] = array('between', array($add_time, $out_time));
            } elseif ($add_time) {
                $condition['zf_time'] = array('egt', $add_time);
            } elseif ($out_time) {
                $condition['zf_time'] = array('elt', $out_time);
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_rise_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_rise_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('riseLogAjax');
            die;
        }
        $this->display('riseLog');
    }

    /**
     * 添加涨价规则
     */
    public function addRise() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->sharesRiseInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Shares/sharesRiseList'));
            }
        } else {
            $this->display('sharesRiseInfo');
        }
    }

    /**
     * 修改涨价规则
     */
    public function editRise() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->sharesRiseInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Shares/sharesRiseList'));
            }
        } else {
            $this->assign('info', M(NUOYILIANNAME.'.shares_rise')->where(array('rise_id' => I('id')))->find());
            $this->display('sharesRiseInfo');
        }
    }

    /**
     * 状态管理
     */
    public function saveRise() {
        $val = I('val') == 1 ? 2 : 1;
        $data[I('fieldVal')] = $val;
        $where['rise_id'] = I('id');
        $res = M('shares_rise')->where($where)->save($data);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 删除规则
     */
    public function delRise() {
        $where = array('rise_id' => array('in', I('id')));
        $res = $row = M('shares_rise')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 股票交易设置
     */
    public function sharesConfigList() {
        if (IS_AJAX) {
            $condition = array();
            I('shares_id') ? $condition['shares_id'] = I('shares_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_config')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_config')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('sharesConfigListAjax');
            die;
        }
        $info = D('shares_config')->where('id=1')->find();
        $this->assign('info', $info);
        $this->display('addSharesConfig');
    }

    /**
     * 股票的交易价格设置
     */
    public function sharesConfigInfo() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->addMoneyConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/moneyList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $id = I('GET.id');
            if ($id) {
                $info = D('shares_config')->where('id=' . $id)->find();
                $this->assign('info', $info);
            }
            $this->display('sharesConfigInfo');
        }
    }

    /**
     *  新增股票的交易参数
     */
    public function addSharesConfig() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->addSharesConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/moneyList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $info = D(NUOYILIANNAME.'.shares_config')->where('id=1')->find();
        $this->assign('info', $info);
            $this->display('addSharesConfig');
        }
    }

    /**
     * 修改股票的交易参数
     */
    public function editSharesConfig() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->addSharesConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/moneyList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('shares_config')->where(array('id' => I('id')))->find());
            $this->display('addSharesConfig');
        }
    }

    /**
     * 修改股票的交易参数状态
     */
    public function saveSharesConfig() {
        $val = I('val') == 1 ? 2 : 1;
        $data[I('fieldVal')] = $val;
        $where['id'] = I('id');
        $res = M('shares_config')->where($where)->save($data);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 删除钱包
     */
    public function delSharesConfig() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('shares_config')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 清空数据
     */
    public function sharesEmpty() {
        $db = M('Shares');
        $dbconn = M();
        $tables = array(
            'shares_day',
            'shares_issue',
            'shares_price',
            'shares_rise',
            'shares_sell',
            'shares_split',
            'shares_trade',
            'shares_log',
            'shares_user',
            'shares_rise_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->ajaxReturn("操作成功");
    }

    public function aboutList() {
        if (IS_AJAX) {
            $condition = array();
            I('title') && $condition['title'] = array('like', '%' . trim(I('title') . '%'));
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('shares_about')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('shares_about')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('aboutListAjax');
            die;
        }
        $this->display('aboutList');
    }

    /**
     * 添加
     */
    public function addAbout() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->addAboutInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Shares/aboutList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('addAbout');
        }
    }

    /**
     * 修改
     */
    public function editAbout() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\SharesLogic();
            $res = $model->addAboutInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Shares/aboutList'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('shares_about')->where(array('id' => I('id')))->find());
            $this->display('addAbout');
        }
    }

    /**
     * 修改显示状态
     */
    public function saveAbout() {
        $val = I('val') == 1 ? 2 : 1;
        $data[I('fieldVal')] = $val;
        $where['id'] = I('id');
        $res = M('shares_about')->where($where)->save($data);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 删除奖项
     */
    public function delAbout() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('shares_about')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    /**
     * 赠送会员股票
     */
    public function giveShares() {
        if (IS_AJAX && IS_POST) {
            $userWhere = array();
            $startTime = strtotime(I('start_time'));
            $endTime = strtotime(I('end_time'));
            if ($startTime && $endTime) {
                $userWhere['reg_time'] = array('between', array($startTime, $endTime));
            } elseif ($startTime > 0) {
                $userWhere['reg_time'] = array('gt', $startTime);
            } elseif ($endTime > 0) {
                $userWhere['reg_time'] = array('lt', $endTime);
            }
            $sharesType = (I('shares_id') ? I('shares_id') : 1);
            I('level_id') && $userWhere['level'] = I('level_id');
            I('leader_id') && $userWhere['leader'] = I('leader_id');
            I('agent_id') && $userWhere['agent'] = I('agent_id');
            $userList = M('users')->where($userWhere)->field('user_id')->select();
            $firstUser = M('users')->order('user_id')->field('user_id')->find();
            foreach ($userList as $k => $v) {
                if ($v['user_id'] == $firstUser['user_id']) {
                    unset($userList[$k]);
                }
            }
            $zsNum = floatval(I('zsNum'));
            if ($zsNum <= 0) {
                $this->error('请输入赠送股票');
            }
            $zsZong = $zsNum * count($userList);
            $yfZong = M('shares_user')->sum('total');
            $fxZong = M('shares')->where(array('id' => $sharesType))->getField('taotal');
            if (($zsZong + $yfZong) > $fxZong) {
                $this->error('股票超过封顶!' . $zsZong . '--' . $fxZong . '---' . $sharesType);
            } elseif (($zsZong + $yfZong) < 0) {
                $this->error('股票出现负数!');
            }
            $zfNum = 0;
            foreach ($userList as $v) {
                if ($zsNum < 0) {
                    $userShares = usersShares($v['user_id'], 1);
                    if ($userShares > 0) {
                        $jianNum = min(abs($zsNum), $userShares);
                        $zfNum -= $jianNum;
                        sharesLog($v['user_id'], $sharesType, '-' . $jianNum, 0, 7, I('note'), session('admin_id'));
                    }
                } else {
                    $zfNum += $zsNum;
                    sharesLog($v['user_id'], $sharesType, $zsNum, 0, 7, I('note'), session('admin_id'));
                }
            }
            sharesLog($firstUser['user_id'], $sharesType, '-' . $zfNum, 0, 7, I('note'), session('admin_id'));
            $this->success('操作成功!');
        }
    }

    /**
     * 清空股票数据
     */
    public function clearSharesData() {
        $dbconn = M();
        $tables = array(
            'shares_buy',
            'shares_day',
            'shares_issue',
            'shares_log',
            'shares_price',
            'shares_rise_log',
            'shares_sell',
            'shares_split',
            'shares_trade',
            'shares_user',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $data = array(
            'total_num' => 0,
            'out_num' => 0,
            'split_num' => 0
        );
        $res = M('shares')->where(array('id' => 1))->save($data);
        $this->success("操作成功");
    }

    /**
     * 走势图
     */
    public function sharesTrendChart() {
        $this->display('sharesTrendChart');
    }

}
