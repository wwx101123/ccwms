<?php

class Sms {

    var $url;
    private $config;
    private $mobile;
    private $text;
    private $err = ''; // 错误内容

    /**
     * Sms constructor.
     * @param array $config 短信配置
     * @param string $mobile 手机号
     * @param string $text 短信内容
     */

    public function __construct($config = array(), $mobile = '', $text = '') {
        import('Common.Org.Curl');

        $this->config = $config;

        $this->mobile = $mobile;

        $this->text = $text;

        $this->url = 'http://utf8.api.smschinese.cn/';
    }

    /**
     * 发送短信
     */
    public function sendSms() {


        $data = array(
            'Uid' => $this->config['sms_user'],
            'Key' => $this->config['sms_key'],
            'smsMob' => $this->mobile,
            'smsText' => $this->text
        );
        $curl = new Curl($data);
        $res = $curl->httpRequest($this->url);
        $checkRes = $this->checkSend($res); // 检查短信
        if ($checkRes) {
            return 0;
        }
        return $res;
    }

    /**
     * 获取短信数量
     */
    public function getSmsNum() {
        $data = array(
            'Action' => 'SMS_Num',
            'Uid' => $this->config['sms_user'],
            'Key' => $this->config['sms_key'],
        );
        $curl = new Curl($data);
        $res = $curl->httpRequest('http://www.smschinese.cn/web_api/SMS/');

        return $res;
    }

    /**
     * 添加发送记录
     * @param string $mobile 手机号
     * @param string $code 验证码
     */
    public function addSendSmsLog($mobile, $code) {
        $data = array(
            'name' => $mobile,
            'zf_time' => time(),
            'content' => $this->text,
            'code' => $code,
            'is_type' => 2,
            'session_id' => session_id(),
            'is_class' => 1
        );

        return M('sms_log')->add($data);
    }

    /**
     * 检查短信发送
     * @param string $res 发送返回信息
     */
    public function checkSend($res) {
        if ($res < 1) {
            $this->err .= '发送失败';
        }
        switch ($res) {
            case -4:
                $this->err .= '手机号错误!';
                break;
            case -14:
                $this->err .= '短信内容含有非法字符!';
                break;
            case -6:
                $this->err .= 'IP限制!';
                break;
        }
        return $this->err;
    }

    /**
     * 返回错误信息
     */
    public function showErr() {
        return $this->err;
    }

}
