<?php

namespace Mobile\Controller;
/**
 * 代理
 * Class ServiceController
 * @package Mobile\Controller
 * @author gkdos@qq.com
 */
class ServiceController extends CommonController
{

    /**
     * 会员申请代理
     */
    public function upServiceAdd()
    {

        $user = $this->user;
        if($user['service'] >= 1) {
            $this->error('不能申请');
        }

        if(IS_POST) {

            $post = I('post.');
            $serviceLogic = new \Zfuwl\Logic\ServiceLogic();

            $res = $serviceLogic->upServiceAdd($post, $user['user_id']);
            if($res['status'] == 1) {
                $this->success($res['msg'], $res['url']);
            } else {
                $this->error($res['msg']);
            }
        } else {
            $serviceInfo = M('service')->where(array('id' => $user['service']))->field('name_cn')->find();
            $serviceList = M('service')->where(array('statu' => 1, 'id' => 1))->field('id, name_cn, amount')->select();
            $jsArr = array();
            foreach($serviceList as $v) {
                $jsArr[] = array(
                    'value' => $v['id'],
                    'text' => $v['name_cn'],
                    'amount' => $v['amount']
                );
            }


            $this->assign('jsStr', json_encode($jsArr));

            $this->assign('serviceInfo', $serviceInfo);

            $this->display('upServiceAdd');
        }
    }

}