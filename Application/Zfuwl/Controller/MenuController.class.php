<?php

namespace Zfuwl\Controller;

class MenuController extends CommonController {

    protected $menuModel;

    public function __construct() {
        parent::__construct();
        $menuModel = D('AdminMenu');
        $this->menuModel = $menuModel;
    }

    /**
     * 菜单列表
     */
    public function index() {
        $menu = $this->menuModel->selectAllMenu();
        $menu = get_column($menu);
        $this->assign('menu', $menu);
        $this->display();
    }

    /**
     * 修改菜单名称
     */
    public function editMenuName() {
        $res = M('auth_rule')->where(array('id' => I('id')))->save(array('title' => I('name')));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 修改菜单状态
     */
    public function saveStatus() {
        if (IS_POST) {
            $val = I('val');
            $id = I('id');
            $res = $this->menuModel->where(array('id' => $id))->save(array('status' => $val));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    // 商家菜单 

    public function sellerIndex() {
        $menu = $this->menuModel->selectAllSellerMenu();
        $menu = get_column($menu);
        $this->assign('menu', $menu);
        $this->display();
    }

    /**
     * 修改菜单名称
     */
    public function editMenuSellerName() {
        $res = M('seller_auth_rule')->where(array('id' => I('id')))->save(array('title' => I('name')));
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 修改菜单状态
     */
    public function saveSellerStatus() {
        if (IS_POST) {
            $val = I('val');
            $id = I('id');
            $res = M('seller_auth_rule')->where(array('id' => $id))->save(array('status' => $val));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

}
