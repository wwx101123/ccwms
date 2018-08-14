<?php
header('Content-Type:text/html;Charset=UTF-8');
$dbConfig = require_once '/Application/Common/Conf/db.php';
require_once '/Application/Common/Common/function.php';
$bdArray = $dbConfig;
$link = @mysql_connect($bdArray['DB_HOST'] . ':' . $bdArray['DB_PORT'], $bdArray['DB_USER'], $bdArray['DB_PWD']) or die('Mysql Connect Error : ' . mysql_error());
mysql_select_db($bdArray['DB_NAME'], $link) or die('Mysql Connect Error:' . mysql_error());
mysql_query('SET NAMES ' . $bdArray['DB_CHARSET'], $link);
$czpassword = 'admin888';
$passwrod = adminEncrypt($czpassword);
$username = 'admin';
$sql = 'select user_name,password,status,admin_id from '.$bdArray['DB_PREFIX'].'admin_user where user_name="'.$username.'"';
$retval = mysql_query($sql, $link);
if(!$retval) {
    die('Unable To Read Data: '.mysql_error($link));
}
$res = mysql_fetch_array($retval);
if(!$res) {
    $sql = 'insert into '.$bdArray['DB_PREFIX'].'admin_user (user_name,password,add_time,status) value("'.$username.'","'.$passwrod.'","'.time().'",1)';
    $result = mysql_query($sql, $link);
    if($result) {
        $userId = mysql_insert_id($link);
    }
} else {
    $sql = 'update '.$bdArray['DB_PREFIX'].'admin_user set status=1,password="'.$passwrod.'" where user_name="'.$username.'"';
    $result = mysql_query($sql, $link);
    $userId = $res['admin_id'];
}
$sql = 'select id from '.$bdArray['DB_PREFIX'].'auth_group where status=1 order by length(rules) desc limit 0,1';
$retval = mysql_query($sql, $link);
$res = mysql_fetch_array($retval);
$sql = 'insert into '.$bdArray['DB_PREFIX'].'auth_group_access (uid,group_id) value('.$userId.','.$res['id'].')';
$result = mysql_query($sql, $link);
//unlink(__FILE__);
die('账号:'.$username.', 密码:'.$czpassword.' 请记住 并登入平台及时改掉');