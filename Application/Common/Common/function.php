<?php

define('AUTH_CODE', md5("webZFUWLJIAFUW"));
define('AUTH_ADMIN', md5("ADMINZFUWLJIAFUW"));

use Org\Net\IpLocation;

/**
 *
 * @param type $count 分页总条数
 * @param type $pagesize 黙认 10页 当前值过来时 以传过来的值为准
 * @return \Think\Page
 */
function getPage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows" style="font-size:16px;line-height:34px;">共<b style="color:#009688;font-size:16px;"> %TOTAL_ROW% </b>条记录&nbsp;当前显示第<b style="color:#009688;font-size:16px;"> %NOW_PAGE% </b>页/共<b style="color:#009688;font-size:16px;"> %TOTAL_PAGE% </b>页</li>');
    $p->setConfig('prev', '<<');
    $p->setConfig('next', '>>');
    $p->setConfig('first', '1...');
    $p->setConfig('last', '...%TOTAL_PAGE%');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false; //最后一页不显示为总页数
    return $p;
}

/**
 * ajax分页
 * @param $dir
 * @param string $file_type
 */
function ajaxGetPage($count, $pagesize = 10) {
    $p = new Think\AjaxPage($count, $pagesize);
    $p->setConfig('header', '<li class="rows" style="font-size:16px;line-height:34px;">共<b style="color:#009688;"> %TOTAL_ROW% </b>条数据&nbsp;当前显示第<b style="color:#009688;font-size:16px;"> %NOW_PAGE% </b>页/共<b style="color:#009688;"> %TOTAL_PAGE% </b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('first', '首页');
    $p->setConfig('last', '末页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false; //最后一页不显示为总页数
    return $p;
}

// 递归删除文件夹
function delFile($dir, $file_type = '') {
    if (is_dir($dir)) {
        $files = scandir($dir);
        //打开目录 //列出目录中的所有文件并去掉 . 和 ..
        foreach ($files as $filename) {
            if ($filename != '.' && $filename != '..') {
                if (!is_dir($dir . '/' . $filename)) {
                    if (empty($file_type)) {
                        unlink($dir . '/' . $filename);
                    } else {
                        if (is_array($file_type)) {
                            //正则匹配指定文件
                            if (preg_match($file_type[0], $filename)) {
                                unlink($dir . '/' . $filename);
                            }
                        } else {
                            //指定包含某些字符串的文件
                            if (false != stristr($filename, $file_type)) {
                                unlink($dir . '/' . $filename);
                            }
                        }
                    }
                } else {
                    delFile($dir . '/' . $filename);
                    rmdir($dir . '/' . $filename);
                }
            }
        }
    } else {
        if (file_exists($dir))
            unlink($dir);
    }
}

/**
 * 前台加密
 */
function webEncrypt($str) {
    return md5(AUTH_CODE . $str);
}

/**
 * 后台加密
 */
function adminEncrypt($str) {
    return md5(AUTH_ADMIN . $str);
}

/**
 * 商家加密
 */
function sellerEncrypt($str) {
    return md5(AUTH_SELLER . $str);
}

/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array|string|int
 */
function zfCache($config_key, $data = array()) {
    $configModel = new \Zfuwl\Model\ConfigModel();
    $param = explode('.', $config_key);
    $where = array('inc_type' => $param[0]);
    if (empty($data)) {
        $config = F($param[0], '', TEMP_PATH); //直接获取缓存文件
        if (empty($config)) {
            //缓存文件不存在就读取数据库
            $res = $configModel->selectAll($where);
            if ($res) {
                foreach ($res as $k => $val) {
                    $config[$val['name']] = $val['value'];
                }
                F($param[0], $config, TEMP_PATH);
            }
        }
        if (count($param) > 1) {
            return $config[$param[1]];
        } else {
            return $config;
        }
    } else {
        //更新缓存
        $result = $configModel->selectAll($where);
        if ($result) {
            foreach ($result as $val) {
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k => $v) {
                $newArr = array('name' => $k, 'value' => trim($v), 'inc_type' => $param[0]);
                if (!isset($temp[$k])) {
                    $configModel->addData($newArr);
                } else {
                    if ($v != $temp[$k]) {
                        $configModel->saveData("name='$k'", $newArr);
                    }
                }
            }
            //更新后的数据库记录
            $newRes = $configModel->selectAll($where);
            foreach ($newRes as $rs) {
                $newData[$rs['name']] = $rs['value'];
            }
        } else {
            foreach ($data as $k => $v) {
                $newArr[] = array('name' => $k, 'value' => trim($v), 'inc_type' => $param[0]);
            }
            $configModel->addAll($newArr);
            $newData = $data;
        }
        return F($param[0], $newData, TEMP_PATH);
    }
}

/**
 * 获取客户端ip
 */
function getIP() {
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknow";
    return $ip;
}

/**
 * 淘宝 根据ip获取详细地址
 * */
function getAddressByIp($ip) {
    import('Org.Net.IpLocation'); // 导入IpLocation类
    $Ip = new IpLocation(); // 实例化类
    $location = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
    dump($location);
}

/**
 * 淘宝 根据ip获取详细地址
 * */
function getcposition($ip) {
    if ($ip == '127.0.0.1' || $ip == '') {
        return "本地";
    }
    $res1 = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=$ip");
    $res1 = json_decode($res1, true);
    if ($res1["code"] == 0) {
        return $res1['data']["country"] . $res1['data']["region"] . $res1['data']["city"] . "_" . $res1['data']["isp"];
    } else {
        return "未知";
    }
}

/**
 * 用户用户设备信息
 */
function equipmentSystem() {
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (stristr($agent, 'iPad')) {
        $fb_fs = "iPad";
    } else if (preg_match('/Android (([0-9_.]{1,3})+)/i', $agent, $version)) {
        $fb_fs = "手机(Android " . $version[1] . ")";
    } else if (stristr($agent, 'Linux')) {
        $fb_fs = "电脑(Linux)";
    } else if (preg_match('/iPhone OS (([0-9_.]{1,3})+)/i', $agent, $version)) {
        $fb_fs = "手机(iPhone " . $version[1] . ")";
    } else if (preg_match('/Mac OS X (([0-9_.]{1,5})+)/i', $agent, $version)) {
        $fb_fs = "电脑(OS X " . $version[1] . ")";
    } else if (preg_match('/unix/i', $agent)) {
        $fb_fs = "Unix";
    } else if (preg_match('/windows/i', $agent)) {
        $fb_fs = "电脑(Windows)";
    } else {
        $fb_fs = "Unknown";
    }
    return $fb_fs;
}

/**
 * 是否移动端访问访问
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 * @return bool
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 验证帐号格式
 * @param $email 帐号
 */
function checkAccount($account) {
    if (zfCache('regInfo.isfirst') == 1 && intval($account) > 0) {
        return false;
    }
    if (preg_match('/^[a-zA-Z0-9_\x7f-\xff]{' . zfCache('regInfo.account_mai') . ',' . zfCache('regInfo.account_max') . '}$/', $account)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 检查手机号码格式
 * @param $mobile 手机号码
 */
function checkMobile($mobile) {
    if (preg_match('/1[34578]\d{9}$/', $mobile))
        return true;
    return false;
}

/**
 * 检查身份证格式
 * @param $card 身份证号
 */
function checkCard($card) {
    if (preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/', $card))
        return true;
    return false;
}

/**
 * 检查银行卡号
 * @param $email 邮箱地址
 */
function checkBankCard($card_number) {
    $arr_no = str_split($card_number);
    $last_n = $arr_no[count($arr_no) - 1];
    krsort($arr_no);
    $i = 1;
    $total = 0;
    foreach ($arr_no as $n) {
        if ($i % 2 == 0) {
            $ix = $n * 2;
            if ($ix >= 10) {
                $nx = 1 + ($ix % 10);
                $total += $nx;
            } else {
                $total += $ix;
            }
        } else {
            $total += $n;
        }
        $i++;
    }
    $total -= $last_n;
    $x = 10 - ($total % 10);
    if ($x == $last_n) {
        return 'true';
    } else {
        return 'false';
    }
}

/**
 * 检查邮箱地址格式
 * @param $email 邮箱地址
 */
function checkEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}

/**
 * 验证密码格式
 * @param $pass 密码
 * @return bool
 */
function checkPass($pass) {
    if (preg_match('/[0-9a-zA-Z\!\@\#\^\_]{' . zfCache('securityInfo.pass_mai') . ',' . zfCache('securityInfo.pass_max') . '}$/', $pass))
        return true;
    return false;
}

/**
 * 验证密码格式 首字母必须大写
 * @param $pass 密码
 * @return bool
 */
function checkPass2($pass) {
    if (preg_match('/^\b[A-Z][0-9a-zA-Z\!\@\#\^\_]{' . zfCache('securityInfo.pass_mai') . ',' . zfCache('securityInfo.pass_max') . '}$/', $pass))
        return true;
    return false;
}

/**
  +----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
 * http://www.thinkphp.cn/code/94.html
  +----------------------------------------------------------
 * @param string    $string   待转换的字符串
 * @param int       $bengin   起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int       $len      需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int       $type     转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string    $glue     分割符
  +----------------------------------------------------------
 * @return string   处理后的字符串
  +----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@") {
    if (empty($string))
        return false;
    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    if ($type == 0) {
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", $array);
    }else if ($type == 1) {
        $array = array_reverse($array);
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", array_reverse($array));
    }else if ($type == 2) {
        $array = explode($glue, $string);
        $array[0] = hideStr($array[0], $bengin, $len, 1);
        $string = implode($glue, $array);
    } else if ($type == 3) {
        $array = explode($glue, $string);
        $array[1] = hideStr($array[1], $bengin, $len, 0);
        $string = implode($glue, $array);
    } else if ($type == 4) {
        $left = $bengin;
        $right = $len;
        $tem = array();
        for ($i = 0; $i < ($length - $right); $i++) {
            if (isset($array[$i]))
                $tem[] = $i >= $left ? "*" : $array[$i];
        }
        $array = array_chunk(array_reverse($array), $right);
        $array = array_reverse($array[0]);
        for ($i = 0; $i < $right; $i++) {
            $tem[] = $array[$i];
        }
        $string = implode("", $tem);
    }
    return $string;
}

/**
 * 生成随机数
 * @param type $length
 * @return string
 */
function getRanduNum($length = 8) {
    // 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

/**
 * 获取网站域名
 * @return string 网站域名
 */
function getWebUrl() {
    /* 协议 */
    $protocol = 'http://';
    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = ':' . $_SERVER['SERVER_PORT'];
            if ((':80' == $port & 'http://' == $protocol) || (':443' == $port & 'https://' == $protocol)) {
                $port = '';
            }
        } else {
            $port = '';
        }

        if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'] . $port;
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            $host = $_SERVER['SERVER_ADDR'] . $port;
        }
    }

    return $protocol . $host;
}

/**
 * 获取数组中的某一列
 * @param array $arr 数组
 * @param string $keyName 列名
 * @return array  返回那一列的数组
 */
function getArrColumn($arr, $keyName) {
    $returnArr = array();
    if (!empty($arr)) {
        foreach ($arr as $k => $v) {
            $returnArr[] = $v[$keyName];
        }
    }
    return $returnArr;
}

/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名
 */
function convertArrKey($arr, $keyName) {
    $arr2 = array();
    foreach ($arr as $key => $val) {
        $arr2[$val[$keyName]] = $val;
    }
    return $arr2;
}

/**
 * 重组数组
 * @param $arr 数组
 * @param $keyName 数组中的某个键的值做为键
 * @param $valName 数组中的某个键的值做为值
 * @return array 重组后的数组
 */
function recombinantArr($arr, $keyName, $valName) {
    $returnArr = array();
    if (is_array($arr)) {
        foreach ($arr as $v) {
            $returnArr[$v[$keyName]] = $v[$valName];
        }
    }
    return $returnArr;
}

/**
 * 去除数组中的空值
 * @param $arr
 * @return 去除空值后的数组
 */
function unArrNull($arr) {
    foreach ($arr as $k => $v) {
        if ($v == '') {
            unset($arr[$k]);
        }
    }
    return $arr;
}

/**
 *   实现中文字串截取无乱码的方法
 */
function getSubstr($string, $start, $length) {
    if (mb_strlen($string, 'utf-8') > $length) {
        $str = mb_substr($string, $start, $length, 'utf-8');
        return $str . '...';
    } else {
        return $string;
    }
}

/**
 * 递归文章分类
 * @param $array
 * @param int $fid
 * @param int $level
 */
function getArticleCatColumn($array, $type = 1, $fid = 0, $level = 0) {
    $column = [];
    if ($type == 2) {
        foreach ($array as $key => $vo) {
            if ($vo['parent_id'] == $fid) {
                $vo['level'] = $level;
                $column[$key] = $vo;
                $column [$key][$vo['cat_id']] = getArticleCatColumn($array, $type, $vo['cat_id'], $level + 1);
            }
        }
    } else {
        foreach ($array as $key => $vo) {
            if ($vo['parent_id'] == $fid) {
                $vo['level'] = $level;
                $column[] = $vo;
                $column = array_merge($column, getArticleCatColumn($array, $type, $vo['cat_id'], $level + 1));
            }
        }
    }
    return $column;
}

/**
 * 获取文件修改时间
 * @param string $file 文件名
 * @param string $DataDir 目录名
 * @return false|string
 */
function getfiletime($file, $DataDir) {
    $a = filemtime($DataDir . $file);
    $time = date("Y-m-d H:i:s", $a);
    return $time;
}

/**
 * 获取文件大小
 * @param string $file 文件名
 * @param string $DataDir 目录名
 * @return string
 */
function getfilesize($file, $DataDir) {
    $perms = stat($DataDir . $file);
    $size = $perms['size'];
    // 单位自动转换函数
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if ($size < $kb) {
        return $size . " B";
    } else if ($size < $mb) {
        return round($size / $kb, 2) . " KB";
    } else if ($size < $gb) {
        return round($size / $mb, 2) . " MB";
    } else if ($size < $tb) {
        return round($size / $gb, 2) . " GB";
    } else {
        return round($size / $tb, 2) . " TB";
    }
}

/**
 * 系统邮件发送函数
 * @param string $to 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @param bool $debug 是否开启调试
 * @return boolean
 */
function sendMail($to, $name, $subject = '', $body = '', $attachment = null, $debug = false) {
    $config = zfCache('smtpSmsInfo');
    import('Vendor.PHPMailer.Smtp');
    import('Vendor.PHPMailer.PHPMailer'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail = new PHPMailer(); //PHPMailer对象
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug = $debug;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true;                  // 启用 SMTP 验证功能
//    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host = $config['smtp_server'];  // SMTP 服务器
    $mail->Port = $config['smtp_port'];  // SMTP服务器的端口号
    $mail->Username = $config['smtp_user'];  // SMTP服务器用户名
    $mail->Password = $config['smtp_pwd'];  // SMTP服务器密码
    $mail->SetFrom($config['send_useremail'], $config['send_username']);
    $replyEmail = $config['reply_useremail'] ? $config['reply_useremail'] : $config['send_useremail'];
    $replyName = $config['reply_username'] ? $config['reply_username'] : $config['send_username'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    if (is_array($to)) {
        foreach ($to as $v) {
            $mail->AddAddress($v, $name);
        }
    } else {
        $mail->AddAddress($to, $name);
    }
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 发送短信
 * @param string $mobile 手机号
 * @param string $text 短信内容
 * @param string $code 验证码
 */
function sendSms($mobile, $text, $code = '') {
    $num = 0;
    $err = '';
    import('Common.Org.Sms');
    header('Content-Type:text/html;Charset=UTF-8');
    $mobile = explode(',', $mobile);
    foreach ($mobile as $v) {
        if (checkMobile($v)) {
            $sms = new Sms(zfCache('smtpSmsInfo'), $v, $text);
            $logRes = $sms->addSendSmsLog($v, $code); // 添加发送记录
            $res = $sms->sendSms();
            if ($res) {
                M('sms_log')->where("id = {$logRes}")->save(array('is_type' => 1));
                $num++;
            } else {
                M('sms_log')->where("id = {$logRes}")->save(array('errcode' => $sms->showErr()));
                $err .= $v . $sms->showErr();
            }
        }
    }
    if ($num > 0) {
        return array('status' => 1, 'msg' => '发送成功!');
    } else {
        return array('status' => -1, 'msg' => $err);
    }
}

/**
 * 获取短信数量
 */
function getSmsNum() {
    import('Common.Org.Sms');
    header('Content-Type:text/html;Charset=UTF-8');
    $sms = new Sms(zfCache('smtpSmsInfo'));
    $res = $sms->getSmsNum();

    return $res;
}

/**
 * 短信验证码验证
 * @param $mobile   手机
 * @param $code  验证码
 * @param $session_id   唯一标示
 * @return bool
 */
function smsCodeVerify($mobile, $code, $session_id, $sms_time_out = '') {
    $session_id = $session_id ? $session_id : session_id();
    //判断是否存在验证码
    $data = M('sms_log')->where(array('mobile' => $mobile, 'session_id' => $session_id, 'code' => $code, 'is_verify' => 2))->order('id DESC')->find();
    if (empty($data)) {
        return array('status' => -1, 'msg' => '手机验证码不匹配');
    }
    //获取时间配置
    $sms_time_out = $sms_time_out ? $sms_time_out : zfCache('smtpSmsInfo.sms_time_out');
    $sms_time_out = $sms_time_out ? $sms_time_out : 120;
    //验证是否过时
    if ((time() - $data['zf_time']) > $sms_time_out) {
        return array('status' => -1, 'msg' => '手机验证码超时'); //超时处理
    }

    M('sms_log')->where(array('mobile' => $mobile, 'session_id' => $session_id, 'code' => $code, 'is_verify' => 2))->save(array('is_verify' => 1));
    return array('status' => 1, 'msg' => '验证成功');
}

/**
 * 邮箱验证码验证
 * @param $email   邮箱
 * @param $code  验证码
 * @param $session_id   唯一标示
 * @return array
 */
function emailCodeVerify($email, $code, $session_id, $sms_time_out = '') {
    if ($code == '') {
        return array('status' => -1, 'msg' => '请输入邮箱验证码');
    }
    $session_id = $session_id ? $session_id : session_id();
    //判断是否存在验证码
    $data = M('sms_log')->where(array('name' => $email, 'session_id' => $session_id, 'code' => $code, 'is_verify' => 2, 'is_class' => 2))->order('id DESC')->find();
    if (empty($data)) {
        return array('status' => -1, 'msg' => '邮箱验证码不匹配');
    }
    //获取时间配置
    $sms_time_out = $sms_time_out ? $sms_time_out : zfCache('smtpSmsInfo.email_time_out');
    $sms_time_out = $sms_time_out ? $sms_time_out : 120;
    //验证是否过时
    if ((time() - $data['zf_time']) > $sms_time_out) {
        return array('status' => -1, 'msg' => '邮箱验证码超时'); //超时处理
    }

    M('sms_log')->where(array('name' => $email, 'session_id' => $session_id, 'code' => $code, 'is_verify' => 2, 'is_class' => 2))->save(array('is_verify' => 1));
    return array('status' => 1, 'msg' => '验证成功');
}

/**
 * [prevtd 查出上级帐号]
 * @param  [type] $id [会员帐号]
 */
function prevtd($id) {
    static $arr = array();
    $user = M('users')->where(array('user_id' => $id))->field('user_id,tjr_id')->find(); //查出登录会员的推荐人的帐号
    if ($user) {
        $arr[] = $user['tjr_id'];
        prevtd($user['tjr_id']);
    }
}

/**
 * [nexttd 查出下级的帐号]
 * @param  [type] $id [会员帐号]
 */
function nexttd($id) {
    global $arr;
    $userList = M('users')->where(array('tjr_id' => $id))->field('user_id')->select(); //查出登录会员推荐的人的帐号
    foreach ($userList as $v) {
        $arr[] = $v['user_id'];
        nexttd($v['user_id']);
    }
    return $arr;
}

function tuopu_tjr_tree($tjr_id, $dtp, $dtp1) {
    if ($dtp >= $dtp1) {
        echo '<table align="center" cellpadding="0" cellspacing="0"><tr>';
        $i = 1;
        $tjrList = M('users')->where(array('tjr_id' => $tjr_id))->select();
        $tjrCount = count($tjrList);
        foreach ($tjrList as $k => $v) {
            $level = M('level')->where(array('level_id' => $v['level']))->find();
            echo '<td valign="top">';
            if ($tjrCount != 1) {
                echo '<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';
                switch ($k) {
                    case 0:
                        echo '<td width="50%" height="1"></td><td width="50%" height="1" bgcolor="#003399"></td>';
                        break;
                    case $tjrCount - 1:
                        echo '<td width="50%" height="1" bgcolor="#003399"></td><td width="50%" height="1"></td>';
                        break;
                    default:
                        echo '<td width="50%" height="1" bgcolor="#003399"></td><td width="50%" height="1" bgcolor="#003399"></td>';
                        break;
                }
                echo ' </tr></table><img src="/Public/images/line.gif" alt="" width="27" border="0" style="WIDTH: 1px; HEIGHT: 20px" /></td></tr></table>';
            }
            echo '	<table width="120" border="0" cellpadding="0" cellspacing="1" bgcolor="#517DBF" align="center" ><tr><td align="center" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">';
            echo '<tr><td height="20" align="center" bgcolor="' . $level['color'] . '"><a style="width:100px;" href="?account=' . $v['account'] . '"><font color="#fff"><strong>' . $v['account'] . '</strong></font></a></td></tr>';
            echo '<tr><td height="20" align="center" bgcolor="' . $level['color'] . '"><font color="#fff">' . $v['nickname'] . '</font></td></tr>';
            echo '<tr><td height="20" align="center" bgcolor="' . $level['color'] . '">' . $level['name_cn'] . '</td></tr>';
            echo '<tr><td height="20" align="center" bgcolor="' . $level['color'] . '">' . date('Y-m-d H:i:s', $v['reg_time']) . '</td></tr>';
            global $arr;
            $arr = array();
            echo '<tr>
					<td align="center" bgcolor="#B0E0E6">
						<table width="100%" cellspacing="1" cellpadding="0" border="0" bgcolor="#E7F2FB">
							<tbody bgcolor="#c9e8ec" align="center">
								<tr><td>总</td><td>' . count(nexttd($v['user_id'])) . '</td></tr>
								<tr><td>直推</td><td>' . js_team($v['user_id'], 'tjr_id') . '</td></tr>
							</tbody>
						</table>
					</td>
				</tr>
			  ';
            if ($v['activate'] != 1) {
                echo '<tr><td height="19" align="center" style="color:red;">未激活</td></tr>';
            } else {
                echo '<tr><td height="19" align="center">' . date('Y-m-d H:i:s', $v['jh_time']) . '</td></tr>';
            }
            echo '</tr></table></td></tr></table>';

            $tjrxjList = M('users')->where(array('tjr_id' => $v['user_id']))->field('user_id')->select();
            echo '	<div style="display:block" id="table_' . ($k + 1) . '">';
            if (count($tjrxjList) > 0) {
                echo '
                    <table align="center" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center"><img style="WIDTH: 1px; HEIGHT: 20px" alt="" src="/Public/images/line.gif" border="0" /></td>
                        </tr>
                    </table>
                ';
                tuopu_tjr_tree($v['user_id'], $dtp, $dtp1 + 1);
            }
            echo '</div></td>';
        }
        echo '</tr></table>';
    }
}

/**
 * 计算  一级 二级  三级团队人数
 * @param 会员id  $id
 * @param 类型    $field
 */
function js_team($id, $field) {
    return M('users')->where(array($field => $id))->count('user_id');
}

function testExce() {
    import('Vendor.PHPExcel.Lite');

    $data = array(
        array('username' => 'zhangsan', 'password' => "123456"),
        array('username' => 'lisi', 'password' => "abcdefg"),
        array('username' => 'wangwu', 'password' => "111111"),
    );

    $filename = "test_excel.xlsx";
    $headArr = array("用户名", "密码");
    $PHPExcel_Lite = new PHPExcel_Lite();
    $PHPExcel_Lite->exportExcel($filename, $data, $headArr);
}

/**
 * 接点人业绩切换
 * @param $user_id 会员id
 * @param $total   换位会员总业绩
 * @param $new   换位会员新增业绩
 * @param $type 类型 1 是加 2是减 默认1
 */
function jdrMoneySwitch($user_id, $total, $new, $type = 1) {
    $model = M('users_branch');
    $user = $model->where(array('user_id' => $user_id))->find();
    if (!$user) {
        return false;
    }

    $sf = array('1' => '+', '2' => '-');
    $totalYj = $sf[$type] . ($total);
    $newYj = $sf[$type] . ($new);

    switch ($user['position']) {
        case 1:
            $sql = "update __PREFIX__users_branch set left_new = left_new+{$newYj}, left_total = left_total+{$totalYj} where user_id = " . $user['jdr_id'];
            break;
        case 2:
            $sql = "update __PREFIX__users_branch set right_new = right_new+{$newYj}, right_total = right_total+{$totalYj} where user_id = " . $user['jdr_id'];
            break;
    }
    $sql && M()->execute($sql);
    $jdr = $model->where("user_id = {$user['jdr_id']}")->find();
    if ($jdr['jdr_id']) {
        jdrMoneySwitch($jdr['user_id'], $total, $new, $type);
    }
}

function verifySecurity($user, $answer) {
    if (intVal($user['security_id']) <= 0 || $user['answer'] == '') {
        return array('status' => -1, 'msg' => '请先设置密保!');
    }
    if ($answer == '') {
        return array('status' => -1, 'msg' => '请输入密保答案!');
    }
    if ($user['answer'] != $answer) {
        return array('status' => -1, 'msg' => '密保验证失败!');
    }

    return array('status' => 1, 'msg' => '验证成功!');
}

/**
 * 扣除管理费
 * @param  int $jdrId 接点人id
 * @param  int $pos 接点位置
 * @return bool
 */
function kouchuguanlifei($jdrId, $pos) {
    $user = M('users_branch')->where(array('jdr_id' => $jdrId, 'position' => $pos))->find();
    if ($user) {
        switch ($pos) {
            case 2:
                $money = zfCache('securityInfo.right_glf');
                $note = '右区管理费';
                break;

            default:
                $money = zfCache('securityInfo.left_glf');
                $note = '左区管理费';
                break;
        }
        userMoneyAddLog($jdrId, 1, '-' . $money, 0, 114, $note);
    }
    return true;
}

function getBranchPathNum($userId, $pos) {
    $userBranch = M('users_branch')->where(array('user_id' => $userId))->field('path_num')->find();
    $jdrList = M('users_branch')->where(array('jdr_id' => $userId))->field('position,path,user_id')->select();
    $jdrList = convertArrKey($jdrList, 'position');
    $pathNum = 0;
    if ($jdrList[$pos]) {
        $pathNum += 1;
        $a = M('users_branch')->where("locate('" . $jdrList[$pos]['path'] . ',' . $jdrList[$pos]['user_id'] . "', path) > 0")->order('branch_id desc')->field('path_num')->find();
        $a > 0 && $pathNum += $a['path_num'] - $userBranch['path_num'];
    }
    return $pathNum;
}

/**
 * 时间
 */
function time_tran($timeInt, $format = 'Y-m-d H:i:s') {
    $d = time() - $timeInt;
    if ($d < 0) {
        return $timeInt;
    } else {
        if ($d < 60) {
            return $d . '秒前';
        } else {
            if ($d < 3600) {
                return floor($d / 60) . '分钟前';
            } else {
                if ($d < 86400) {
                    return floor($d / 3600) . '小时前';
                } else {
                    if ($d < 259200) {//3天内
                        return floor($d / 86400) . '天前';
                    } else {
                        return date($format, $timeInt);
                    }
                }
            }
        }
    }
}

//  "1" ->待支付,2=>待发货,3=>待收货,4=>已完成  显示支付状态
// 没有数字，返回所有的状态
function showPayStatus($num = 0) {
    $arr = ["1" => 待支付, 2 => 待发货, 3 => 待收货, 4 => 已完成];
    if (!$num)
        return $arr;
    return $arr[$num];
}

/**
 *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
 *
 * @author Wu Junwei*
 * @param int $length 需要生成的字符串的长度
 * @return string 包含 大小写英文字母 和 数字 的随机字符串
 */
function random_str($length) {
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $arr_len - 1);
        $str.=$arr[$rand];
    }

    return $str;
}

/**
 * 获取上级接点人
 * @param  [int]  $userId [description]
 * @param  integer $num    [description]
 * @param  integer $i      [description]
 * @return [type]          [description]
 */
function getJdrPrevId($userId, $num = 0, $i = 0) {

    if (!$userId) {
        return false;
    }
    $a = $i;
    if ($num > $i + 1 && $num != 1) {
        if (session('user')) {
            $user = session('user');
            $userPlayMoneyInfo = M('users_play_money')->where(array('is_type' => 1, 'status' => 2, 'uid' => $user['user_id'], 'level_id' => $num))->find();
            if ($userPlayMoneyInfo) {
                $userBranch = M('users_branch')->where(array('uid' => $userPlayMoneyInfo['to_uid']))->field('jdr_id')->find();
                # 接点人信息
                $jdrUser = M('users')->where(array('user_id' => $userPlayMoneyInfo['to_uid']))->field('level,power')->find();
                # 接点人直推人数
                if ($jdrUser['power'] != 1 && !in_array($num + 1, array(1, 2, 3))) {
                    # 20180317 添加的，管理员在后台 设置 不接受考核条件的收款
                    $jdrTjrNum = M('users')->where(array('tjr_id' => $userId))->count();
                    $level = M('level')->where(array('level_id' => $num + 1))->find();
                    if (!$jdrUser || $jdrUser['level'] < ($level['level_id']) || $jdrTjrNum < $level['tjr_num']) {
                        $a = $num - 1;
                    }
                } else {
                    $a = $num;
                }
//                if($userBranch['jdr_id'] <= 0) {
//                    $userBranch['jdr_id'] = $userPlayMoneyInfo['to_uid'];
//                }
            } else {
                $a += 1;
                $userBranch = M('users_branch')->where(array('uid' => $userId))->field('jdr_id')->find();
            }
        } else {
            $a += 1;
            $userBranch = M('users_branch')->where(array('uid' => $userId))->field('jdr_id')->find();
        }
    } else {
        $a += 1;
        $userBranch = M('users_branch')->where(array('uid' => $userId))->field('jdr_id')->find();
    }
    if ($num > $i) {
        if ($userBranch['jdr_id'] > 0) {
            return getJdrPrevId($userBranch['jdr_id'], $num, $a);
        } else {
            return $userId;
        }
    } else {
        # 接点人信息
        $jdrUser = M('users')->where(array('user_id' => $userId))->field('level,power')->find();
        # 接点人直推人数
        if ($jdrUser['power'] != 1 && !in_array($num + 1, array(1, 2, 3))) {
            # 20180317 添加的，管理员在后台 设置 不接受考核条件的收款
            $jdrTjrNum = M('users')->where(array('tjr_id' => $userId))->count();
            $level = M('level')->where(array('level_id' => $num + 1))->find();
            if (!$jdrUser || $jdrUser['level'] < ($level['level_id']) || $jdrTjrNum < $level['tjr_num']) {
                if ($userBranch['jdr_id'] > 0) {
                    return getJdrPrevId($userBranch['jdr_id'], $num, $i);
                } else {
                    return $userId;
                }
            }
        }
        return $userId;
    }
}

/* function getJdrPrevId_bak($userId, $num = 0, $i = 0)
  {

  if(!$userId) {
  return false;
  }
  $userBranch = M('users_branch')->where(array('uid' => $userId))->field('jdr_id')->find();
  if($num > $i) {
  if($userBranch['jdr_id'] > 0) {
  return getJdrPrevId($userBranch['jdr_id'], $num, $i+1);
  } else {
  return $userId;
  }
  } else {
  # 接点人信息
  $jdrUser = M('users')->where(array('user_id' => $userId))->field('level,power')->find();
  # 接点人直推人数
  if ($jdrUser['power'] != 1) {
  # 20180317 添加的，管理员在后台 设置 不接受考核条件的收款
  $jdrTjrNum = M('users')->where(array('tjr_id' => $userId))->count();
  $level = M('level')->where(array('level_id' => $num+1))->find();
  if (!$jdrUser || $jdrUser['level'] < ($level['level_id']) || $jdrTjrNum < $level['tjr_num']) {
  if($userBranch['jdr_id'] > 0) {
  return getJdrPrevId($userBranch['jdr_id'], $num, $i);
  } else {
  return $userId;
  }
  }
  }
  return $userId;
  }


  } */

/**
 * 创建前台地址文件
 * @param  int $pid    [上级id]
 * @param  int $level  [层级]
 * @param  array  $region [description]
 * @return 地址信息
 */
function createDataCity($pid = 0, $level = 1, $region = []) {
    if (!$region) {
        $list = M("region")->select();
        foreach ($list as $key => $val) {
            $region[$val['parent_id']][] = $val;
        }
    }
    $arr = [];
    foreach ($region[$pid] as $k => $v) {
        $arr2 = [
            'value' => $v['id'],
            'text' => $v['name_cn']
        ];
        $count = count($region[$v['id']]);
        if ($count > 0) {
            $arr2['children'] = createDataCity($v['id'], $level + 1, $region);
        }
        $arr[] = $arr2;
    }
    return $arr;
}

function getTjrList($tjr_id, $dtp, $dtp1) {
    global $tjrList;
    if ($dtp >= $dtp1) {
        $tjrList1 = M('users')->where(array('tjr_id' => $tjr_id, 'is_type' => 1))->field('user_id')->select();

        $tjrList = $tjrList ? array_merge($tjrList, $tjrList1) : $tjrList1;

        foreach ($tjrList1 as $k => $v) {
            $tjrxjList = M('users')->where(array('tjr_id' => $v['user_id'], 'is_type' => 1))->field('user_id')->select();
            if (count($tjrxjList) > 0) {
                getTjrList($v['user_id'], $dtp, $dtp1 + 1);
            }
        }
    }
    return $tjrList;
}

/**
 * @param $uid 用户的id
 * @param $mid 钱包id
 * 达到条件自动升级源码级别 2018-6-14 17:40
 */
function levelS($uid, $bid = 1) {
    // 查出用户的信息
    $user = userInfo($uid);
    // 查出用户的的储存积分
    $userBlock = M('users')->where(['user_id' => $uid])->field('invest_money')->find();
    $kk = M('level')->where(array('cast(amount as decimal)' => array('elt', $userBlock['invest_money'])))->order('level_id desc')->find();
    if ($user['level'] != $kk['level_id']) {
        $data['uid'] = $uid;
        $data['y_id'] = $user['level'];
        $data['x_id'] = $kk['level_id'];
        $data['zf_time'] = time();
        $data['statu'] = 1;
        if ($user['level'] < $kk['level_id']) {
            $data['note'] = blockOne($bid)[name_cn] . '达到' . $kk['amount'] . '自动升级';
        } else {
            $data['note'] = blockOne($bid)[name_cn] . '小于' . $kk['amount'] . '自动降级';
        }
        M('level_log')->add($data);
      	$kk['level_id'] <= 0 ?$kk = 1: $kk['level_id'] = $kk['level_id'];
        M('users')->where(array('user_id' => $uid))->save(array('level' => $kk['level_id']));
    }
}

/**
 * 代理级别达到条件自动升级
 * @param $uid 用户的id
 */
function leaderS($uid) {
    // 查出用户的信息
    $user = userInfo($uid);

    $block = M('users')->where(['user_id' => $uid])->field('invest_money')->find();

    //查出该用户直推源码合伙人数量
    $tjr = M('users')->where(['tjr_id' => $uid])->count();

    // 查出代理级别
    $leader = M('leader')->where(['id' => $user['leader'] + 1])->find();
    if ($leader['id'] == 1) {
        if ($tjr >= $leader['tjr_num'] && count(tjrNums($uid))>= $leader['tjr_num_1'] && teamAllMoney($uid) >= $leader['share_money']) {
            $data = [];
            $data['uid'] = $uid;
            $data['y_id'] = $user['level'];
            $data['x_id'] = $leader['id'];
            $data['zf_time'] = time();
            $data['statu'] = 1;
            if ($user['level'] < $leader['id']) {
                $data['note'] = '总资产达到和分享业绩达到条件自动升级';
            } else {
                $data['note'] = '总资产达到和分享业绩未达条件自动降级';
            }
            M('leader_log')->add($data);
            M('users')->where(array('user_id' => $uid))->save(array('leader' => $leader['id']));
        }
    } else {
        // 查出代理推荐人
        $tjrLeader = M('users')->where(['tjr_id' => $uid, 'leader' => ['egt', $user['leader']]])->count();
      	
        if ($tjr >= $leader['tjr_num'] && $tjrLeader >= $leader['tjr_num_1'] && $block['invest_money'] >= $leader['cun_money'] && teamAllMoney($uid) >= $leader['share_money']) {
            $data = [];
            $data['uid'] = $uid;
            $data['y_id'] = $user['level'];
            $data['x_id'] = $leader['id'];
            $data['zf_time'] = time();
            $data['statu'] = 1;
            if ($user['level'] < $leader['id']) {
                $data['note'] = '总资产达到和分享业绩达到条件自动升级';
            } else {
                $data['note'] = '总资产达到和分享业绩未达条件自动降级';
            }
            M('leader_log')->add($data);
            M('users')->where(array('user_id' => $uid))->save(array('leader' => $leader['id']));
        }
    }
}

/**
 * 降级
 */
function leaderMinus($uid) {
    // 查出用户的信息
    $user = userInfo($uid);

    // 查出用户的的储存积分
    $share_money = M('leader')->where(array('share_money' => array('elt', teamAllMoney($uid))))->order('id desc')->find();
    $cun_money = M('leader')->where(array('cun_money' => array('elt', $user['invest_money'])))->order('id desc')->find();
    $leader = min($share_money['id'], $cun_money['id']);
    if ($user['leader'] > $leader) {
        $data['uid'] = $uid;
        $data['y_id'] = $user['leader'];
        $data['x_id'] = $leader;
        $data['zf_time'] = time();
        $data['statu'] = 1;
        $data['note'] = '条件未达到自动降级';
        M('leader_log')->add($data);
        M('users')->where(array('user_id' => $uid))->save(array('leader' => $leader));
    }
}

/**
 * 查出二级会员的人数
 * @param $uid
 * @return mixed
 */
function tjrNums($uid) {
    $tjrUser = M('users')->where(['tjr_id' => $uid])->field('user_id')->select();
    foreach ($tjrUser as $v) {
        $tjrTjr = M('users')->where(['tjr_id' => $v['user_id']])->field('user_id')->select();
        foreach ($tjrTjr as $va) {
            $arr[] = $va['user_id'];
        }
    }

    return $arr;
}

/**
 * 查出该用户的总分享业绩
 * @param $uid 用户的id
 */
function teamAllMoney($uid) {
    $teamId = teamAll($uid);
    $teamId = array_unique($teamId);
    if ($teamId) {
        $data = [];
        $data['user_id'] = ['in', $teamId];
        $blockAll = M('users')->where($data)->sum('invest_money');
        return $blockAll;
    } else {
        return 0;
    }
}

/**
 * 查出该用户团队总人数
 * @param $uid
 */
function teamAll($uid) {
    global $arr;
    $tjr_id = M('users')->where(['tjr_id' => $uid])->field('user_id')->select();
    if ($tjr_id) {
        foreach ($tjr_id as $v) {
            $arr[] = $v['user_id'];
            teamAll($v['user_id']);
        }
    }
    return $arr;
}

function guideUser() {
    $user = M('users')->field('user_id')->select();
    // 循环
    foreach ($user as $v) {
        levelS($v['user_id']);
    }
}
