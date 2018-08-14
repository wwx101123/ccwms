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
 * Date:2016-12-10 17:33  162
 */

namespace Zfuwl\Controller;

use Think\Controller;

class ApiController extends Controller {
    /*
     * 获取地区
     */

    public function getRegion() {
        $parent_id = I('get.parent_id');
        $selected = I('get.selected', 0);
//        $data = M('region')->where("parent_id = {$parent_id}")->select();
        $data = M('region')->where(["parent_id" => $parent_id])->select();
        $html = '';
        if ($data) {
            foreach ($data as $h) {
                if ($h['id'] == $selected) {
                    $html .= "<option value='{$h['id']}' selected>{$h['name_cn']}</option>";
                }
                $html .= "<option value='{$h['id']}'>{$h['name_cn']}</option>";
            }
        }
        echo $html;
    }

    public function getTwon() {
        $parent_id = I('get.parent_id');
        $data = M('region')->where(["parent_id" => $parent_id])->select();
        $html = '';
        if ($data) {
            foreach ($data as $h) {
                $html .= "<option value='{$h['id']}'>{$h['name_cn']}</option>";
            }
        }
        if (empty($html)) {
            echo '0';
        } else {
            echo $html;
        }
    }

    /**
     * 验证码
     */
    public function verify() {
        // 验证码类型
        $type = I('get.type') ? I('get.type') : 'login_index';
        $config = array(
            'fontSize' => 15, // 验证码字体大小(px)
            'length' => 4, // 验证码位数
            'imageH' => 40, // 验证码图片高度
            'imageW' => 113, // 验证码图片宽度
            'fontttf' => '4.ttf', // 验证码字体，不设置随机获取
            'useCurve' => false, // 是否画混淆曲线
            'useNoise' => false
                ) // 是否添加杂点 true
        ;
        $Verify = new \Think\Verify($config);
        $Verify->entry($type);
    }

    /*
     * 获取安置树状网络数据
     */

    public function getUserForJd() {
        $where = ' 1=1';
        if ($_GET['account']) {
            $user = M('users')->where(array('account' => $_GET['account']))->field('user_id')->find();
            if (!$user) {
                $arr[0]['id'] = '1';
                $arr[0]['text'] = '未找到会员信息';
                exit(json_encode($arr));
            }
            $where .= ' and ub.uid = ' . $user['user_id'];
        } else {
            I('id') ? $where .= ' and ub.jdr_id = ' . I('id') : $where = 'ub.jdr_id = 0';
        }
        $userList = M()->table('__USERS_BRANCH__ as ub')->join('__USERS__ as u on u.user_id = ub.uid')->where($where)->order('ub.branch_id asc, ub.position asc')->limit(3)->select();

        $place = array('1' => 'A', '2' => 'B', '3' => 'C');
        $levelInfo =  M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn');
        foreach ($userList as $k => $v) {
            $arr[$k]['id'] = $v['branch_id'];
            $arr[$k]['text'] = $place[$v["position"]] . ' - 账号：' . $v['account'];
            $tjrNum = M('users_branch')->where(array('jdr_id' => $v['branch_id']))->count();
            if ($tjrNum > 0) {
                $arr[$k]['state'] = 'closed';
            }
        }

        exit(json_encode($arr));
    }

    public function oppositeAccount() {
        $account = I('name');
        // if (!checkAccount($account)) {
        //     $user['cg'] = 0;
        //     $user['msg'] = '账号格式错误!';
        //     $this->ajaxReturn($user);
        // }
        $user = D("UserView")->where("account='{$account}'")->field('nickname,mobile,username')->find();
        if ($user) {
            $user['cg'] = 1;
        } else {
            $user['cg'] = 0;
        }
        $this->ajaxReturn($user);
    }

    /**
     * git自动同步失败发送
     */
    public function gitErrorEmail() {
        sendMail('gkdos@qq.com', 'gkdos', 'b004git失败', 'b004git失败');
    }

    /**
     * git自动同步失败发送
     */
    public function drUser() {

//        $recent = drUser();
    }

    /**
     * @function imageUp
     */
    public function uploadGoodsImg() {

        $imgUrl = $_POST['dir'] ? $_POST['dir'] : 'home';
        $config = array(
            'rootPath' => 'Public/',
            'savePath' => 'upload/' . $imgUrl . '/'.$_POST['field'].'/',
            'maxSize' => 3145728,
            'subName' => date('Y-m-d'),
            "exts" => explode(",", 'gif,png,jpg,jpeg,bmp'),
        );
        $upload = new \Think\Upload($config);
        $info = $upload->upload($_FILES);
        if ($info) {
            $state = 1;
        } else {
            $state = 0;
            $msg = $upload->getError();
        }
        if ($state) {
            $returnData = array(
                'code' => 0,
                'msg' => '',
                'data' => array(
                    'src' => '/' . $config['rootPath'] . $info[$_POST['field']]['savepath'] . $info[$_POST['field']]['savename']
                )
            );
        } else {
            $returnData = array(
                'code' => 1,
                'msg' => $msg,
            );
        }
        $this->ajaxReturn($returnData, 'json');
    }

    /**
     * 删除商品图片
     */
    public function delGoodsImg() {
        $imgUrl = substr($_POST['imgUrl'], 1);
        $res = unlink($imgUrl);
        $goodsId = intVal($_POST['goods_id']);
        if ($goodsId) {
            $goodsInfo = M('goods')->where("goods_id = {$goodsId}")->find();
            $goodsImg = explode(',', $goodsInfo['goods_img']);
            foreach ($goodsImg as $k => $v) {
                if ($v == $_POST['imgUrl']) {
                    unset($goodsImg[$k]);
                }
            }
            M("goods")->where("goods_id = {$goodsId}")->save(array('goods_img' => implode(',', $goodsImg)));
        }
        $this->success('删除成功!');

    }

    /**
     * @function imageUp
     */
    public function imageUp() {
        $imgUrl = $_POST['dir'] ? $_POST['dir'] : 'home';
        $config = array(
            'rootPath' => 'Public/',
            'savePath' => 'upload/' . $imgUrl . '/',
            'maxSize' => 3145728,
            'subName' => $_POST['field'],
            "exts" => explode(",", 'gif,png,jpg,jpeg,bmp'),
        );
        $upload = new \Think\Upload($config);
        $info = $upload->upload($_FILES);
        if ($info) {
            $state = 1;
        } else {
            $state = 0;
            $msg = $upload->getError();
        }
        if ($state) {
            $returnData = array(
                'code' => 0,
                'msg' => '',
                'data' => array(
                    'src' => '/' . $config['rootPath'] . $info[$_POST['field']]['savepath'] . $info[$_POST['field']]['savename']
                )
            );
        } else {
            $returnData = array(
                'code' => 1,
                'msg' => $msg,
            );
        }
        $this->ajaxReturn($returnData, 'json');
    }

    public function tradeLocdAdd() {
        $mon = M('ep_log')->where(array('id' => I('id')))->find();
        if ($mon['is_type'] == 1) {
            $lockData = array(
                'user_id' => $mon['buy_uid'],
                'lock_time' => time(),
                'log_info' => 'EP买入超时未打款',
                'log_url' => CONTROLLER_NAME . '' . ACTION_NAME,
            );
            $res = M('users_lock_log')->add($lockData);
            M("users")->where("user_id = {$mon['buy_uid']}")->save(array('is_lock' => 2));
            M('ep_log')->where(array('id' => $mon['id']))->save(array('is_type' => 0, 'buy_time' => 0, 'buy_uid' => 0));
        } else {
            $lockData = array(
                'user_id' => $mon['sell_uid'],
                'lock_time' => time(),
                'log_info' => 'EP卖出超时未确认收款',
                'log_url' => CONTROLLER_NAME . '' . ACTION_NAME,
            );
            $model = new \Think\Model();
            $model->startTrans();
            $lockId = M('users_lock_log')->add($lockData);
            $userId = M("users")->where("user_id = {$mon['sell_uid']}")->save(array('is_lock' => 2));
            if ($mon['poundage'] > 0) {
                $poundage_num = $mon['poundage'] * $mon['num'] / 100;
                $buyNum = $mon['num'] - $poundage_num;
                $text .= '买入手续费' . $mon['poundage'] . '%';
            } else {
                $poundage_num = 0;
                $buyNum = $mon['num'];
            }
            $sell = M('users')->where(array('user_id' => $mon['sell_uid']))->field('account')->find(); //卖家
            $logId = userMoneyAddLog($mon['buy_uid'], $mon['money_id'], $buyNum, 0, 110, '卖家' . $sell['account'] . '超时未确认,自动确认收款' . $text);
            $epId = M('ep_log')->where(array('id' => $mon['id']))->save(array('is_type' => 9, 'out_time' => time()));
            if ($lockId && $userId && $logId && $epId) {
                $model->commit();
            } else {
                $model->rollback();
            }
        }
    }

  public function daySj() {
    echo time();
        bonus2Clear();
    	bonus3Clear();
    echo time().'结算成功';
    }

    public function createDataCity()
    {
        $res = createDataCity();
        file_put_contents('Public/js/data.city.js', 'var cityData3 = '.json_encode($res,JSON_UNESCAPED_UNICODE));
        $this->success('生成成功');
    }


    public function judgeCj()
    {
        $users = M('users')->where(['is_cj' == 2])->field('user_id')->select();
        foreach($users as $v) {
            judgeCj($v['user_id']);
        }

        $this->success('操作成功');
    }


    /**
     * 会员超时复投冻结
     */
    public function judgeFrozen()
    {
        detectTimeout();

        $this->success('操作成功');
    }

    /**
     * 奖金释放
     */
    public function releaseShares()
    {
        bonus1ClearSj();
//        bonus2ClearSj();
        $this->success('释放成功');
        die;
        $where = [
            'statu' => 2
            ,'sf_money' => ['gt', 0]
        ];
        $list = M('shares_user_lock')->where($where)->select();
        foreach($list as $v) {
            $sfDay = intval(zfCache('securityInfo.sf_day'));
            if(strtotime("+" . $sfDay . " day", $v['lock_time']) <= time()) {
                $sfMoney = (($v['frozen']-$v['money']) > $v['sf_money'] ? $v['sf_money'] : ($v['frozen']-$v['money']));
                if($sfMoney > 0) {
                    sharesLog($v['uid'], $v['sid'], 0, '-'.$sfMoney, 1, $note.'释放');
                    $data = [
                        'money' => $v['money']+$sfMoney
                    ];
                    if($v['frozen'] <= $data['money']) {
                        $data['statu'] = 1;
                        $data['out_time'] = time();
                    }
                    M('shares_user_lock')->where(['id' => $v['id']])->save($data);
                }
            }
        }

        $this->success('操作成功');
    }

    public function test()
    {
        guideUser();
        exit;
//        $userList = M('users')->field("user_id")->select();
//        foreach($userList as $v) {
//            userMoneyAdd($v['user_id']);
//        }
//        $res = addUserBranch(10);
//        $res = playMoney(10);
        $res = bonus1ClearSj();
//        $res = bonus3Clear(1, 2, 1000, '测试');
//        $res = bonus2Clear(1, 2, 1000, '测试');
        // $info = M('users')->find();
//        $res = bonus3Clear(1, 10, 1000, 'test');
        dump($res);
    }
  
  	public function block()
    {
        buySellRemitTimeBlock();
    }

}
