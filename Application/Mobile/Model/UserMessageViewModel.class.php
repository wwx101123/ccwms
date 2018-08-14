<?php

namespace Mobile\Model;

use Think\Model\ViewModel;

class UserMessageViewModel extends ViewModel
{

    public $viewFields = array(
        'web_users_message' => array('rec_id', 'user_id', 'message_id', 'status', '_type' => 'LEFT'),
        'web_message' => array('message', 'send_time', 'send_user_id', '_on' => 'web_users_message.message_id=web_message.message_id')
    );

}
