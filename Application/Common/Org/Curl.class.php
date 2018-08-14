<?php

class Curl
{

    protected static $_httpInfo = array(); // 传输信息
    protected static $_params = array(); // 请求参数
    protected static $_response = array();


    /**
     * 架构函数
     * @access public
     * @param array $params 请求参数
     */
    public function __construct($params = array())
    {
        self::$_params = $params;
    }

    /**
     * @param $url 请求网址
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @param int $https curl请求超时时间
     * @return bool|mixed
     */
    public static function httpRequest($url, $ispost = 0, $https = 0, $timeOut = 30)
    {
        $params = self::$_params;
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!$https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if (is_array($params)) {
            $params = http_build_query($params);
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));

        self::$_httpInfo = $httpInfo;
        self::$_response = $response;

        curl_close($ch);
        return $response;
    }

    /**
     * 获取请求信息
     */
    public function deBug()
    {
        header('Content-Type:text/html;Charset=UTF-8');
        echo '<pre>';
        echo "=====请求参数======<br />";
        print_r(self::$_params) . '<br />';
        echo "=====请求信息===== <br />";
        print_r(self::$_httpInfo) . '<br />';
        echo "=====response=====<br />";
        print_r(self::$_response);
        echo '</pre>';
    }
}