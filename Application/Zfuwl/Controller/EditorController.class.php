<?php

namespace Zfuwl\Controller;

use Think\Controller;

class EditorController extends Controller {

    /**
     * 编辑器上传
     * @function imageUp
     */
    public function imageUp() {
        $imgUrl = $_GET['imgUrl'] ? $_GET['imgUrl'] : 'home';
        $imgNum = 0;
        foreach ($_FILES as $k => $v) {
            $imgName = $k;
            $imgNum++;
        }
        $config = array(
            "rootPath" => 'Public/',
            "savePath" => 'upload/' . $imgUrl . '/editor/',
            "maxSize" => 3145728, // 单位B
            "subName" => $imgName,
            "exts" => explode(",", 'gif,png,jpg,jpeg,bmp'),
        );

        $upload = new \Think\Upload($config);
        $info = $upload->upload($_FILES);
        if ($info) {
            $state = 0;
            $msg = '上传成功';
        } else {
            $state = -1;
            $msg = $upload->getError();
        }
        if ($imgNum == 1) {
            $returnData['code'] = $state;
            $returnData['msg'] = $msg;
            $returnData['data'] = array('src' => '/' . $config['rootPath'] . $info[$imgName]['savepath'] . $info[$imgName]['savename']);
            $this->ajaxReturn($returnData, 'json');
        }
        $info['state'] = $state;
        $this->ajaxReturn($info, 'json');
    }

}
