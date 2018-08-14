<?php

namespace Zfuwl\Model;

class AdminlogModel extends CommonModel {

    /**
     * 添加管理员操作日志
     * @param [number] $id   [管理员id]
     * @param [string] $info [操作说明]
     */
    public function addAdminLog($id, $info, $type = 1) {
        $data = array(
            'log_time' => time(),
            'admin_id' => $id,
            'log_info' => $info,
            'log_ip' => getIp(),
            'log_url' => CONTROLLER_NAME . '/' . ACTION_NAME,
            'log_type' => $type,
            'equipment' => equipmentSystem()
        );
        return $this->add($data);
    }

}
