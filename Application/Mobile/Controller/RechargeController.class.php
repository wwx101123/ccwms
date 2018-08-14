<?php

namespace Mobile\Controller;

use Zfuwl\Logic\RechargeLogic;

class RechargeController extends CommonController
{

    protected $rechargeLogic;

    public function _initialize()
    {
        # 不需要登录操作的
        $noLogin = array(
            'payReturn',
            'payNotify'
        );
        if (!in_array(ACTION_NAME, $noLogin)) {
            parent::_initialize();
        }

        $this->rechargeLogic = new RechargeLogic();
    }

    /**
     * 在线充值
     */
    public function rechargeAdd()
    {
        if(IS_POST) {

            $data = I('post.');

            $res = $this->rechargeLogic->rechargeAdd($data, $this->user_id);
            if($res['status'] == 1) {
                $this->success($res['msg'], $res['url']);
            } else {
                $this->error($res['msg']);
            }
        } else {

            $payConfigList = $this->rechargeLogic->where(array('statu' => 1))->select();
            $jsArr = array();
            foreach($payConfigList as $k=>$v) {
                $jsArr[$k] = array(
                    'value' => $v['mid'],
                    'text' => moneyList($v['mid']) . ':' . usersMoney($this->user_id, $v['mid'])
                );
            }
            $this->assign('jsStr', json_encode($jsArr));
            $this->assign('payConfigList', $payConfigList);


            $this->display('rechargeAdd');
        }
    }
    /**
     * 在线充值记录
     */
    public function rechargeAddLog()
    {
        $condition = array();
        $condition['uid'] = $this->user_id;
        if(I('type')) {
            $condition['statu'] = I('type', '', 'intval');
        } else {
            $condition['statu'] = 1;
        }
        if(IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $list = M('pay_recharge')->where($condition)->order('id desc')->limit($p*$pSize, $pSize)->select();
            $this->assign('list', $list);

            $this->display('rechargeAddLogAjax');
        } else {
            $this->assign('type', I('type', '1'));

            $count = M('pay_recharge')->where(array('uid' => $this->user_id, 'statu' => 1))->count();
            $this->assign('count', $count);
            $count = M('pay_recharge')->where(array('uid' => $this->user_id, 'statu' => 3))->count();
            $this->assign('count2', $count);
            $this->display('rechargeAddLog');
        }
    }


    /**
     * 跳转充值接口
     */
    public function locationRecharge()
    {
        $id = I('id', '', 'intval');
        if($id <= 0) {
            $this->error('操作失败');
        }

        $info = M('pay_recharge')->where(array('id' => $id, 'statu' => 3))->find();
        if(!$info) {
            $this->error('该订单已支付');
        }

        require_once 'Vendor/ipspay/config.php';
        require_once 'Vendor/ipspay/lib/IpsPaySubmit.class.php';

        $returnUrl = U('Recharge/ipsPayReturn', '', true, true);
        $notifyUrl = U('Recharge/ipsPayNotify', '', true, true);
        $data = [
            'Version' => $ipspay_config['Version'] // 接口版本号
            ,'MerCode' => $ipspay_config['MerCode'] // 商户号
            ,'Account' => $ipspay_config['Account'] // 交易账户号
            ,'MerCert' => $ipspay_config['MerCert'] // 商户证书
            ,'PostUrl' => $ipspay_config['PostUrl'] // 请求地址
            ,'S2Snotify_url' => $notifyUrl // 服务器S2S通知页面路径
            ,'Return_url' => $returnUrl // 页面跳转同步通知页面路径
            ,'CurrencyType' => $ipspay_config['Ccy'] // 156#人民币
            ,'Lang' => $ipspay_config['Lang'] // 语言 GB中文
            ,'OrderEncodeType' => $ipspay_config['OrderEncodeType'] // 订单支付接口加密方式 5#订单支付采用Md5的摘要认证方式
            ,'RetType' => $ipspay_config['RetType'] // 返回方式 1#S2S返回
            ,'MerBillNo' => $info['order_sn'] // 商户订单号
            ,'MerName' => $inMerName // 商户名
            ,'MsgId' => $ipspay_config['MsgId'] // 消息编号
            ,'PayType' => $info['pay_code_type'] // 支付方式 01#借记卡 02#信用卡 03#IPS账户支付
            ,'Merchanturl' => $returnUrl // 支付结果成功返回的商户 URL
            ,'FailUrl' => '' // 支付结果失败返回的商户 URL 可以为空
            ,'Date' => date('Ymd', $info['add_time']) // 订单日期
            ,'ReqDate' => date("YmdHis")
            ,'Amount' => 0.01 // 订单金额
            // ,'Amount' => $inAmount // 订单金额
            ,'Attach' => json_encode(['order_sn' => $info['order_sn']]) // 商户数据包 通知时返回 由"数字、字母或数字+字母"
            ,'RetEncodeType' => 17 // 交易返回接口加密方式 16# 交 易 返 回 采 用Md5WithRsa 的签名认证方式17#交易返回采用 Md5 的摘要认证方式
            ,'BillEXP' => 10 // 订单有效期 单位小时必须整数
            ,'GoodsName' => "在线充值".moneyList($info['mid']) // 商品名称
            ,'BankCode' => '' // 银行号
            ,'IsCredit' => '' // 直连选择 1直连 null非直连
            ,'ProductType' => 1 // 产品类型 1个人网银 2企业网银
        ];
        //建立请求
        $ipspaySubmit = new \IpsPaySubmit($ipspay_config);
        $html_text = $ipspaySubmit->buildRequestForm($data);
        echo $html_text;
        die;
    }

    /**
     * ips同步返回
     */
    public function ipsPayReturn()
    {
        require_once 'Vendor/ipspay/config.php';
        require_once 'Vendor/ipspay/lib/IpsPayNotify.class.php';
        $ipspayNotify = new \IpsPayNotify($ipspay_config);
        $verify_result = $ipspayNotify->verifyReturn();
        /***
        商户在处理数据时一定要按照文档中’交易返回接口验证事项‘进行判断处理
        1：先判断签名是否正确
        2：判断交易状态
        3：判断订单交易时间，订单号，金额，订单状态，和订单防重处理
         **/
        if ($verify_result) { // 验证成功
            $paymentResult = $_REQUEST['paymentResult'];
            $xmlResult = new \SimpleXMLElement($paymentResult);
            $status = $xmlResult->GateWayRsp->body->Status;
            if ($status == "Y") {
                $merBillNo = $xmlResult->GateWayRsp->body->MerBillNo;
                $ipsBillNo = $xmlResult->GateWayRsp->body->IpsBillNo;
                $ipsTradeNo = $xmlResult->GateWayRsp->body->IpsTradeNo;
                $bankBillNo = $xmlResult->GateWayRsp->body->BankBillNo;
                $this->redirect('User/userIndex');
                $message = "交易成功";
            }elseif($status == "N")
            {
                $this->error('交易失败', U('User/userIndex'));
                $message = "交易失败";
            }else {
                $this->redirect('User/userIndex'); //跳转到配置项中配置的支付失败页面；
                $message = "交易处理中";
            }
        } else {
            $this->error('验证失败', U('User/userIndex')); //跳转到配置项中配置的支付失败页面；
            $message = "验证失败";
        }
    }

    /**
     * ips异步返回
     */
    public function ipsPayNotify()
    {

        require_once 'Vendor/ipspay/config.php';
        require_once 'Vendor/ipspay/lib/IpsPayNotify.class.php';
        $ipspayNotify = new \IpsPayNotify($ipspay_config);
        $verify_result = $ipspayNotify->verifyReturn();
        /***
        商户在处理数据时一定要按照文档中’交易返回接口验证事项‘进行判断处理
        1：先判断签名验证是否正确
        2：判断交易状态
        3：判断订单交易时间，订单号，金额，订单状态，和订单防重处理
         **/
        if ($verify_result) { // 验证成功
            $paymentResult = $_REQUEST['paymentResult'];
            $xmlResult = new \SimpleXMLElement($paymentResult);
            $status = $xmlResult->GateWayRsp->body->Status;
            if ($status == "Y") {
                $merBillNo = $xmlResult->GateWayRsp->body->MerBillNo;
                $ipsBillNo = $xmlResult->GateWayRsp->body->IpsBillNo;
                $ipsTradeNo = $xmlResult->GateWayRsp->body->IpsTradeNo;
                $bankBillNo = $xmlResult->GateWayRsp->body->BankBillNo;
                $merBillNo = json_encode($merBillNo);
                $merBillNo = json_decode($merBillNo, true);

                $info = M('pay_recharge')->where(array("order_sn" => $merBillNo[0], 'statu' => 3))->find();

                if ($info) {
                    userMoneyLogAdd($info['uid'], $info['mid'], $info['money'], 101, '在线充值');
                    $data = array(
                        'pay_code' => 'ips',
                        'pay_name' => '环迅',
                        'pay_time' => time(),
                        'statu' => 1
                    );
                    M('pay_recharge')->where(array('id' => $info['id']))->save($data); // 支付成功修改支付状态
                }

                $message = "交易成功";
            }elseif($status == "N")
            {
                $message = "交易失败";
            }else {
                $message = "交易处理中";
            }
            //请商户根据自己的业务逻辑进行数据处理操作。
            echo "ipscheckok";
        } else {
            echo "ipscheckfail";
        }
    }
    /**
     * 汇款充值
     */
    public function remitRecharge()
    {
        if(IS_POST) {

            $post = I('post.');
            $res = $this->rechargeLogic->remitRecharge($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }

        } else {
            $bankList = M('bank')->where(array('statu' => 1, 'is_c' => 1))->group('name_cn')->select();
            $bankList = convertArrKey($bankList, 'id');

            $bankJsArr = array();
            $i = 0;
            foreach ($bankList as $k => $v) {
                $bankJsArr[$i] = [
                    'value' => $v['id']
                    ,'text' => $v['name_cn']
                    ,'username' => $v['username']
                    ,'bank_account' => $v['account']
                    ,'bank_address' => $v['address']
                ];
                $i++;
            }

            $moneyList = M('money')->where(['is_c' => 1])->select();

            $moneyJsArr = [];
            foreach($moneyList as $k=>$v) {
                $moneyJsArr[$k] = array(
                    'value' => $v['money_id'],
                    'text' => moneyList($v['money_id']) . ':' . usersMoney($this->user_id, $v['money_id'])
                );
            }

            $this->assign('moneyJsStr', json_encode($moneyJsArr));
            $this->assign('moneyJsArr', $moneyJsArr);
            $this->assign('bankJsArr', $bankJsArr);


            $this->assign('bankJsStr', json_encode($bankJsArr));
            $this->display('remitRecharge');
        }
    }

    /**
     * 汇款充值记录
     */
    public function remitRechargeLog()
    {
        if(IS_AJAX) {
            $condition = [
                'uid' => $this->user_id
            ];
            I('type') && $condition['type'] = I('type', '', 'intval');
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $list = M('users_money_add')->where($condition)->order('id desc')->limit($p*$pSize, $pSize)->select();

//            var_dump($list);exit;
            $this->assign('list', $list);

            $this->display('remitRechargeLogAjax');

        } else {
            $this->display('remitRechargeLog');
        }
    }
  
  	public function upload() {
        // $this->ajaxReturn(array('msg' => $post['img']));
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = 'Public/'; // 设置附件上传根目录
        $upload->savePath = 'upload/web'; // 设置附件上传根目录
        // 上传单个文件
        $info = $upload->uploadOne($_FILES['img']);
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功 获取上传文件信息
            $this->ajaxReturn(array('msg' => '/' . $upload->rootPath . $info['savepath'] . $info['savename']));
        }
    }
  
}