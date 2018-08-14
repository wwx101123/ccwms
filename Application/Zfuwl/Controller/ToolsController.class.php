<?php

namespace Zfuwl\Controller;

class ToolsController extends CommonController {

    private $dbManage;
    private $backDir;

    public function _initialize() {

        parent::_initialize();
        import('Common.Org.DbManage');

        $this->dbManage = new DbManage(C('DB_HOST'), C('DB_USER'), C('DB_PWD'), C('DB_NAME'));
        $this->backDir = 'databak/';
    }

    /**
     * 数据库备份列表
     */
    public function backList() {

        if (IS_AJAX) {

            $lists = $this->MyScandir('databak/', 1);

            $list = array();
            $p = I('p') - 1;
            foreach ($lists as $k => $v) {
                if ($k >= $p * I('p_num') && $k < ($p * I('p_num') + I('p_num'))) {
                    $list[] = array(
                        'key' => $k + 1,
                        'file_name' => $v,
                        'bak_time' => getfiletime($v, $this->backDir),
                        'file_size' => getfilesize($v, $this->backDir)
                    );
                }
            }
            if (!$list) {
                $arr = array(
                    'code' => 1,
                    'msg' => '暂无备份'
                );
            } else {
                $arr = array(
                    'code' => 0,
                    'msg' => '',
                    'count' => count($lists),
                    'data' => $list
                );
            }
            exit(json_encode($arr));
        } else {
            $this->display('backList');
        }
    }

    /**
     * 查出备份文件
     * @param string $FilePath
     * @param int $Order
     * @return array
     */
    private function MyScandir($FilePath = './', $Order = 0) {
        $FilePath = opendir($FilePath);
        while (false !== ($filename = readdir($FilePath))) {
            // 去除 . ..
            if ($filename != "." && $filename != "..") {
                $FileAndFolderAyy[] = $filename;
            }
        }
        $Order == 0 ? sort($FileAndFolderAyy) : rsort($FileAndFolderAyy);
        return $FileAndFolderAyy;
    }

    /**
     * 新增备份
     */
    public function back() {
        $res = $this->dbManage->backup('', $this->backDir, zfCache('securityInfo.back_size'));
        $file = '';
        if ($res['status'] > 0) {
            foreach ($res['file'] as $v) {
                if ($v != '') {
                    $file[] = 'databak/' . $v;
                }
            }
            // sendMail(zfCache('webInfo.web_email'), '', '备份数据库', '备份数据库', $file);
            $this->success('数据备份成功!');
        } else {
            $this->error($res['msg']);
        }
    }

    /**
     * 还原数据
     */
    public function restore() {

        $file = I('file');
        if (!file_exists($this->backDir . $file)) {
            $this->error('备份文件不存在,请刷新后重试!');
        }
        $res = $this->dbManage->restore($this->backDir . $file);
        if ($res['status'] > 0) {
            $this->success('数据还原成功!');
        } else {
            $this->error($res['msg']);
        }
    }

    /**
     * 删除备份文件
     */
    public function delBack() {
        $files = I('file');
        if (!is_array($files)) {
            $files = explode(',', $files);
        }
        foreach ($files as $file) {
            $a = unlink($this->backDir . $file);
        }
        if ($a) {
            $this->success("删除成功!");
        } else {
            $this->error("删除失败!");
        }
    }

    /**
     * 上传sql文件
     */
    public function restoreUpload() {
        $config = array(
            "rootPath" => $this->backDir,
            "maxSize" => 100000000, // 单位B
            "exts" => array('sql'),
            "subName" => array(),
        );

        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { // 上传成功 获取上传文件信息
            $file_path_full = '.' . $info['sqlfile']['urlpath'];
            if (file_exists($file_path_full)) {
                $this->success("上传成功");
            } else {
                $this->error('文件不存在');
            }
        }
    }

    /**
     * 下载sql文件
     */
    public function downBack() {
        $file = I('file');
        ob_end_clean();
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($this->backDir . $file));
        header('Content-Disposition: attachment; filename=' . basename($this->backDir . $file));
        readfile($this->backDir . $file);
    }

    /**
     * 数据表列表
     */
    public function tableList() {

        if (IS_AJAX) {

            $tables = M()->query('SHOW TABLE STATUS');
            $total = 0;
            $p = I('p') - 1;
            $list = array();
            $i = 0;
            foreach ($tables as $k => $v) {
                if ($k >= $p * I('p_num') && $k < ($p * I('p_num') + I('p_num'))) {
                    $list[$i] = $v;
                    $list[$i]['size'] = formatBytes($v['data_length'] + $v['index_length']);
                    $i++;
                }
            }
            if (!$list) {
                $arr = array(
                    'code' => 1,
                    'msg' => '暂无数据'
                );
            } else {
                $arr = array(
                    'code' => 0,
                    'msg' => '',
                    'count' => count($tables),
                    'data' => $list
                );
            }
            exit(json_encode($arr));



            $this->assign('lists', $list);

            $this->display('tableListAjax');
        } else {
            $this->display('tableList');
        }
    }

    /**
     * 优化数据表
     */
    public function optimize() {
        $table = I('tableName');
        if (is_array($table)) {
            $table = implode(',', $table);
        }

        if (!M()->query("OPTIMIZE TABLE {$table} ")) {
            $strTable = '';
        }
        $this->success('优化成功!');
    }

    /**
     * 修复数据表
     */
    public function repair() {
        $table = I('tableName');

        if (is_array($table)) {
            $table = implode(',', $table);
        }

        if (!M()->query("REPAIR TABLE {$table} ")) {
            $strTable = '';
        }
        $this->success('修复成功!');
    }

    /**
     * 一键清空数据表
     */
    public function oneKeyClearTable() {

        $tables = M()->query('SHOW TABLE STATUS');

        $prohibitArr = [
            'admin_user'
            , 'auth_group'
            , 'auth_group_access'
            , 'auth_rule'
            , 'config'
            , 'article_cat'
            , 'article'
            , 'block'
            , 'bonus'
            , 'bonus_tax'
            , 'money'
            , 'money_change'
            , 'money_transform'
            , 'region'
            , 'agent'
            , 'level'
            , 'service'
            , 'agent'
            , 'bank'
        ];

        // 增加前缀
        foreach ($prohibitArr as &$v) {
            $v = C("DB_PREFIX") . $v;
        }
        $tables = getArrColumn($tables, 'name');
        foreach ($tables as $val) {
            if (!in_array($val, $prohibitArr)) {
                $sql = "truncate table " . $val;
                M()->execute($sql);
            }
        }

        // 新增会员数据
        // 新增会员数据
        $userData = [
            'username' => zfCache('webInfo.web_name')
        ];
        $dataId = usersDataAdd($userData);
        $userInfo = [
            'nickname' => zfCache('webInfo.web_name')
            , 'account' => zfCache('regInfo.default_tjrAccount')
            , 'password' => webEncrypt(zfCache('regInfo.default_pass'))
            , 'secpwd' => webEncrypt(zfCache('regInfo.default_repass'))
            , 'activate' => 1
            , 'reg_time' => time()
            , 'jh_time' => time()
            , 'level' => 2
            , 'data_id' => $dataId
            , 'agent' => 1
        ];

        $userId = M('users')->add($userInfo);
        userMoneyAdd($userId);
        userBlockAdd($userId);
        $this->success('数据清空成功!');
    }

}
