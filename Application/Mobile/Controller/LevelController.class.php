<?php

namespace Mobile\Controller;

class LevelController extends CommonController
{

    /**
     * 会员升级
     */
    public function upgrade()
    {
        $user = $this->user;
        if(IS_POST) {

            $post = I('post.');

            $levelLogic = new \Zfuwl\Logic\LevelLogic();

            $res = $levelLogic->upgrade($post, $user);
            if($res['status'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
        } else {

            $levelList = M('level')->where(['statu' => 1])->getField('level_id, amount, name_cn');
            $jsArr = [];
            $levelInfo = $levelList[$user['level']];
            foreach($levelList as $v) {
                if($v['level_id'] > $user['level']) {
                    $jsArr[] = [
                        'value' => $v['level_id']
                        ,'text' => $v['name_cn']
                        ,'amount' => $v['amount']-$levelInfo['amount']
                    ];
                }
            }

            $this->assign('jsStr', json_encode($jsArr));

            $this->display('upgrade');
        }
    }
}