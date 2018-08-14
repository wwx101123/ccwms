<?php

namespace Zfuwl\Controller;

header("Content-type: text/html; charset=utf-8");

class ping {

    private $title;    //博客名称
    private $hosturl;  //博客首页地址
    private $arturl;   //新发文章地址
    private $rssurl;   //博客rss地址
    private $baiduXML; //百度XML结构
    private $baiduRPC; //百度XML地址

    public function __construct($title, $arturl, $hosturl, $rssurl) {
        if (empty($title) || empty($arturl))
            return false;
        $this->title = $title;
        $this->hosturl = $hosturl;
        $this->rssurl = $rssurl;
        $this->arturl = $arturl;
        $this->baiduRPC = 'http://ping.baidu.com/ping/RPC2';

        $this->baiduXML = '<?xml version=\"1.0\" encoding=\"gb2312\"?>';
        $this->baiduXML .='<methodCall>';
        $this->baiduXML .='	<methodName>weblogUpdates.extendedPing</methodName>';
        $this->baiduXML .='		<params>';
        $this->baiduXML .='		<param><value><string>' . $this->hosturl . '</string></value></param>';
        $this->baiduXML .='		<param><value><string>' . $this->title . '</string></value></param>';
        $this->baiduXML .='		<param><value><string>' . $this->arturl . '</string></value></param>';
        $this->baiduXML .='		<param><value><string>' . $this->rssurl . '</string></value></param>';
        $this->baiduXML .='	</params>';
        $this->baiduXML .='</methodCall>';
    }

    public function pingbaidu() {
        $ch = curl_init();
        $headers = array(
            'User-Agent: request',
            'Host: ping.baidu.com',
            'Content-Type: text/xml',
        );
        curl_setopt($ch, CURLOPT_URL, $this->baiduRPC);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->baiduXML);
        $res = curl_exec($ch);
        curl_close($ch);
        //return $res;
        return (strpos($res, "<int>0</int>")) ? true : false;
    }

}
