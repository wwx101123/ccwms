<?php

namespace Zfuwl\Controller;

use Think\Controller;

class IndexController extends CommonController {

    public function index() {
        $adminUserModel = D('AdminUser');
        $userInfo = $adminUserModel->findUser(array('admin_id' => $this->admin_id));

        $admin_auth_group_access_model = D('AdminAuthGroupAccess');
        $menus = $admin_auth_group_access_model->getUserRules($this->admin_id);

        $this->assign('menus', $menus);
        $this->assign('userInfo', $userInfo);
        $this->display();
    }

    /**
     * 主页
     */
    public function welcome() {
        $today = strtotime(date('Y-m-d', time()));
        $date = date('Y-m-d');  //当前日期
        $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w = date('w', strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $now_start = date('Y-m-d', strtotime("$date -" . ($w ? $w - $first : 6) . ' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天


        # 获取最近7天的会员数据
        $userList = M('users')->where(['reg_time' => array('egt', strtotime(date('Ymd'))-7*86400)])->order("reg_time asc")->select();
        $list = [];
        for($i = 6; $i>=0; $i--) {
            $list[$i]['time'] = date('m-d', strtotime("-" . $i . " day", strtotime(date('Ymd'))));
            $list[$i]['count'] = 0;
            foreach($userList as $v) {
                if(strtotime("-" . $i . " day", strtotime(date('Ymd'))) == strtotime(date('Ymd', $v['reg_time']))) {
                    $list[$i]['count']++;
                }
            }
        }
        $userJsTime = json_encode(getArrColumn($list, 'time'));
        $this->assign('userJsTime', $userJsTime);
        $this->assign('userJsCount', json_encode(getArrColumn($list, 'count')));

        $this->assign('userTotal', M('users')->where("1=1")->count()); // 平台总会员
        $this->assign('newUsers', M('users')->where("reg_time>$today")->count()); // 今日新增会员
        $this->assign('newLast', M('users_action')->where(array('is_type' => 1, 'zf_time' => array('gt', $today)))->count()); // 今日登录会员数量
        $this->assign('trialTotal', M('users')->where("activate=2")->count()); // 未审会员
        $this->assign('lockTotal', M('users')->where("frozen=2")->count()); // 冻结会员
        $this->assign('emptyTotal', M('users')->where("user=2")->count()); // 冻结会员

        $this->assign('levelList', M('level')->where("statu=1")->cache('levelList')->select());
        $this->assign('bochuJrDay', M('bochu_day')->where(array('zf_time' => strtotime(date('Y-m-d'))))->find()); // 今日统计
        $this->assign('bochuZrDay', M('bochu_day')->where(array('zf_time' => strtotime(date('Y-m-d', strtotime("-1 day")))))->find()); // 昨日统计
        $this->assign('adminLoglList', M('admin_log')->where(array('admin_id' => session('admin_id'), 'log_type' => 1))->limit('6')->order('log_time desc')->select()); // 最近登录日志
        $this->assign('sys_info', $this->get_sys_info());
        $this->display();
    }

    /**
     * 清空系统缓存
     */
    public function cleanCache() {
        if (delFile(RUNTIME_PATH)) {
            $this->success("清除成功");
        } else {
            $this->success("操作完成!!");
        }
    }

    /**
     * 获取系统信息
     */
    public function get_sys_info() {
        $sys_info['os'] = PHP_OS;
        $sys_info['zlib'] = function_exists('gzclose') ? 'YES' : 'NO'; //zlib
        $sys_info['safe_mode'] = (boolean) ini_get('safe_mode') ? 'YES' : 'NO'; //safe_mode = Off
        $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl'] = function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv'] = phpversion();
        $sys_info['ip'] = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        $sys_info['max_ex_time'] = @ini_get("max_execution_time") . 's'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain'] = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit'] = ini_get('memory_limit');
        $mysqlinfo = M()->query("SELECT VERSION() as version");
        $sys_info['mysql_version'] = $mysqlinfo[0]['version'];
        if (function_exists("gd_info")) {
            $gd = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }
        return $sys_info;
    }

    /**
     * ajax 修改指定表数据字段
     * table,idName,idValue,field,value
     */
    public function changeTableVal() {
        $table = I('table'); // 表名
        $where[I('idName')] = I('idValue');
        $data[I('field')] = I('value');
        $res = D($table)->saveData($where, $data);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * ajax 修改指定表数据字段
     * table,idName,idValue,field,value
     */
    public function changeTableVal2() {
        $table = I('table'); // 表名
        $where[I('idName')] = I('idValue');
        $data[I('field')] = I('value');
        $res = M($table)->where($where)->save($data);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
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
            "savePath" => 'upload/' . $imgUrl . '/',
            "maxSize" => 3145728, // 单位B
            "subName" => $imgName,
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
        if ($imgNum == 1) {
            $returnData['state'] = $state;
            $returnData['url'] = '/' . $config['rootPath'] . $info[$imgName]['savepath'] . $info[$imgName]['savename'];
            $returnData['imgName'] = $imgName;
            $returnData['msg'] = $msg;
            $this->ajaxReturn($returnData, 'json');
        }
        $info['state'] = $state;
        $this->ajaxReturn($info, 'json');
    }

    public function test() {
        testPayment();
    }


    /**
     * 重新排序权限
     */
    public function test2()
    {
        $this->test3(0,0);
    }

    public function test3($pid, $pid2)
    {

        $list = M('auth_rule')->where(array('pid' => $pid))->select();
        foreach($list as $v) {
            $data = array(
                'icon' => $v['icon'],
                'menu_name' => $v['menu_name'],
                'title' => $v['title'],
                'pid' => $pid2,
                'is_menu' => $v['is_menu'],
                'status' => 1,
                'sort' => $v['sort']
            );
            $res = M('auth_rule_copy')->add($data);
            $count = M('auth_rule')->where(array('pid' => $v['id']))->count();
            if($count > 0) {
                $this->test3($v['id'], $res);
            }
        }

    }
    /**
     * 获取短信数量
     */
    public function getSmsNum() {
        $smsNum = getSmsNum();
        if ($smsNum) {
            $this->ajaxReturn(array('status' => 1, 'msg' => $smsNum));
        } else {
            $this->ajaxReturn(array('status' => -1, 'msg' => '短信数量获取失败!'));
        }
    }

    /*
     * 获取会员数据
     */

    public function getUser() {
        if ($_GET['account']) {
            $where['account'] = $_GET['account'];
        } else {
            $where['tjr_id'] = I('id') ? I('id') : 0;
        }
        $userList = M('users')->where($where)->field('user_id,account,reg_time')->select();
        foreach ($userList as $k => $v) {
            $arr[$k]['id'] = $v['user_id'];
            $arr[$k]['text'] = '账号：' . $v['account'] . '，注册于：' . date('Y-m-d H:i:s', $v['reg_time']);
            $tjrNum = M('users')->where(array('tjr_id' => $v['user_id']))->count();
            if ($tjrNum > 0) {
                $arr[$k]['state'] = 'closed';
            }
        }
        if (!$arr) {
            $arr[0]['id'] = '1';
            $arr[0]['text'] = '未找到会员信息';
        }
        exit(json_encode($arr));
    }

}
