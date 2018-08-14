<?php

namespace Zfuwl\Controller;

class AuthGroupController extends CommonController {

    protected $adminAuthGroupModel;

    /**
     * 公共操作
     */
    public function _initialize() {
        parent::_initialize();
        $this->adminAuthGroupModel = D('AdminAuthGroup');
    }

    /**
     * 角色列表
     */
    public function authGroupList() {
        $data = $this->adminAuthGroupModel->getGroupList();
        $this->assign('list', $data['list']);
        $this->assign('page', $data['page']);
        $this->display('authGroupList');
    }

    /**
     * 添加角色
     */
    public function addGroup() {
        if (IS_POST) {
            $params = array('title' => I('title'), 'note' => I('note'), 'status' => 1, 'rules' => '');
            if (!$params['title']) {
                $this->error('请输入角色名称!');
            }
            $addGroupResult = $this->adminAuthGroupModel->addGroup($params);
            if (!$addGroupResult) {
                $this->error('添加失败!');
            } else {
                $this->success('添加成功!');
            }
        } else {
            $this->display('addGroup');
        }
    }

    /**
     * 修改角色名称
     */
    public function editAuthName() {
        $res = M('auth_group')->where(array('id' => I('id')))->save(array('title' => I('name')));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 修改角色状态
     */
    public function saveStatus() {
        if (IS_POST) {
            $val = I('val');
            $id = I('id');
            if ($id != 1) {
                $res = M('auth_group')->where(array('id' => $id))->save(array('status' => $val));
                if ($res) {
                    $this->success('更新成功!');
                } else {
                    $this->error('更新失败!');
                }
            }else{
                $this->error('请勿修改超级管理员');
            }
        }
    }

    /**
     * 修改角色信息
     */
    public function editGroup() {
        $id = I('id', 0, 'intval');
        if (!$id) {
            $this->error('角色不存在!');
        }
        if (IS_POST) {
            $params = array('id' => $id, 'title' => I('title'));
            if (!$params['title']) {
                $this->error('请输入角色名称!');
            }
            $saveGroupResult = $this->adminAuthGroupModel->editGroup($params);
            if (!$saveGroupResult) {
                $this->error('修改失败!');
            } else {
                $this->success('修改成功!');
            }
        } else {
            $groupInfo = $this->adminAuthGroupModel->findGroup($id); // 查出角色信息

            $this->assign('groupInfo', $groupInfo);
            $this->display('addGroup');
        }
    }

    /**
     * 删除角色
     */
    public function delGroup() {
        $id = I('id', 0, 'intval');
        $groupInfo = $this->adminAuthGroupModel->findGroup($id);
        if (!$id || !$groupInfo) {
            $this->error('角色不存在!');
        }
        // 修改角色状态
        $changeResult = $this->adminAuthGroupModel->delAuthGroup($id);
        if ($changeResult === false) {
            $this->error('删除失败!');
        } else {
            $this->success('删除成功!');
        }
    }

    /**
     * 分配角色
     */
    public function giveRole() {
        $admin_id = I('id', 0, 'intval');
        if (IS_POST) {
            if (!$admin_id) {
                $this->error('用户不存在!');
            }
            $group_id = $_POST['group_id'];
            $adminAuthGroupAccessModel = D('AdminAuthGroupAccess');
            if (!empty($group_id)) {
                //删除原有角色
                $adminAuthGroupAccessModel->where(array('uid' => $admin_id))->delete();
                foreach ($group_id as $v) {
                    $data = array(
                        'uid' => $admin_id,
                        'group_id' => $v,
                    );
                    $adminAuthGroupAccessModel->addUserGroupAccess($data);
                }
            }
            $this->success('分配成功!');
        } else {
            $this->assign('info', M('admin_user')->where(array('admin_id' => $admin_id))->find());
            $data = $this->adminAuthGroupModel->getGroupList($admin_id);
            $this->assign('list', $data['list']);
            $this->assign('admin_id', $admin_id);
            $this->display('giveRole');
        }
    }

    /**
     * 分配权限
     */
    public function ruleGroup() {
        $admin_auth_group_model = D('AdminAuthGroup');
        if (IS_POST) {
            $data = I('post.');
            $rule_ids = implode(",", $data['menu']);
            $note = $data['note'];
            $role_id = $data['role_id'];
            if (!count($rule_ids)) {
                $this->eroor('请选择需要分配的权限!');
            }
            if ($this->adminAuthGroupModel->addAuthRule($rule_ids, $role_id, $note) !== false) {
                $this->success('分配成功!');
            } else {
                $this->eroor('分配失败，请检查!');
            }
        } else {
            $role_id = I('get.role_id', '', 'intval');
            $menu_model = D('AdminMenu');
            $menus = get_column($menu_model->selectAllMenu(2), 2); // 获取所有权限
            $role_info = $this->adminAuthGroupModel->findGroup($role_id); // 获取角色的信息
            if ($role_info['rules']) {
                $rulesArr = explode(',', $role_info['rules']);
                $this->assign('rulesArr', $rulesArr);
            }
            $this->assign('menus', $menus);
            $this->assign('role_id', $role_id);
            $this->assign('note', $role_info['note']);
            $this->display('ruleGroup');
        }
    }

}
