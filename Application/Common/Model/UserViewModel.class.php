<?php

namespace Common\Model;

use Think\Model\ViewModel;

class UserViewModel extends ViewModel {

    public $viewFields = array(
        'users' => array('user_id', 'account', 'tjr_id', 'bdr_id', 'nickname', 'password', 'secpwd', 'leader', 'level', 'agent', 'reg_time', 'jh_time', 'activate', 'frozen', 'user', 'tk', 'trends', 'static', 'openid', 'token', 'xinyu', 'data_id', 'bank_id', 'main_id', 'service', 'jiangjin_jihuo_status', 'invest_money', 'reg_ip', '_as' => 'u', '_type' => 'LEFT'),
        'users_data' => array('id', 'username', 'mobile', 'email', 'sex', 'wx_name', 'zfb_name', 'qq_name', 'wx_code', 'zfb_code', 'head', 'number', 'imgz', 'imgf', 'country', 'province', 'city', 'district', 'dl_province', 'dl_city', 'dl_district', 'twon', 'address', 'is_mobile', 'is_email', 'is_number', 'pass_name', 'pass_number', 'ylh_name', 'yft_name', 'yhy_name', '_as' => 'ud', '_on' => 'u.data_id=ud.id', '_type' => 'LEFT'),
        'users_bank' => array('id', 'uid', 'opening_id', 'bank_address', 'bank_account', 'bank_name', 'bank_default', '_as' => 'ub', '_on' => 'ub.id=u.bank_id'),
    );

}
