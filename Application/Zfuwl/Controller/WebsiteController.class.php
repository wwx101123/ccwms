<?php

namespace Zfuwl\Controller;

class WebsiteController extends CommonController {

    protected $postData;

    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON', false);
        $this->assign('config', zfCache(ACTION_NAME));
        if (IS_POST) {
//            $this->postData = unArrNull($_POST); // 处理空值
            $this->postData = $_POST; // 处理空值
        }
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
    }

    /**
     * 网站信息
     */
    public function webInfo() {
        if (IS_POST) {
            zfCache('webInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->display();
        }
    }

    /**
     * 短信邮件设置
     */
    public function smtpSmsInfo() {
        if (IS_POST) {
            zfCache('smtpSmsInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->display('smtpSmsInfo');
        }
    }

    /**
     * 发送测试邮件
     */
    public function testSendEmail() {
        $res = sendMail(zfCache('smtpSmsInfo.test_email'), '', '测试邮件', '测试邮件发送');
        if ($res === true) {
            $this->success('发送成功!');
        } else {
            $this->error('发送失败!');
        }
    }

    /**
     * 注册信息
     */
    public function regInfo() {
        if (IS_POST) {
            zfCache('regInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->display();
        }
    }

    /**
     * 登录
     */
    public function loginInfo() {
        if (IS_POST) {
            zfCache('loginInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->display();
        }
    }

    /**
     * 系统安全设置
     */
    public function securityInfo() {
        if (IS_POST) {
            zfCache('securityInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->assign('moneyList', M('money')->getField('money_id, name_cn'));
            $this->display();
        }
    }

    public function pdInfo() {
        if (IS_POST) {
            zfCache('pdInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->display();
        }
    }

    public function sellerInfo() {
        if (IS_POST) {
            zfCache('sellerInfo', $this->postData);
            $this->success("操作成功!");
        } else {
            $this->display();
        }
    }

    public function countryIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('code') && $condition['code'] = array('like', '%' . trim(I('code') . '%'));
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('country')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('country')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('countryIndexAjax');
            die;
        }
        $this->display('countryIndex');
    }

    public function countryAdd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\WebsiteLogic();
            $res = $model->countryInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Website/countryIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('countryInfo');
        }
    }

    public function countryEdit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\WebsiteLogic();
            $res = $model->countryInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Website/countryIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('country')->where(array('id' => I('id')))->find());
            $this->display('countryInfo');
        }
    }

    public function saveSountryStatu() {
        if (IS_POST) {
            $res = M('country')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function countryDel() {
        $num = M('users_data')->where(array('country' => I('id')))->count();
        if ($num > 0) {
            $this->error('当前国家还有' . $num . '名会员，请勿删除');
        }
        $where = array('id' => array('in', I('id')));
        $res = $row = M('country')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function regionIndex() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') ? $condition['parent_id'] = $res = M('region')->where(array('name_cn' => array('like', '%' . trim(I('name_cn') . '%'))))->getField('id') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            if (I('parent_id') > 0) {
                $condition['parent_id'] = I('parent_id');
            } else {
                if (I('name_cn') == '') {
                    $condition['level'] = 1;
                }
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('region')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('region')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('regionIndexAjax');
            die;
        }
        $this->assign('info', M('region')->where(array('id' => I('parent_id')))->find());
        $this->display('regionIndex');
    }

    public function regionAdd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\WebsiteLogic();
            $res = $model->regionInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Website/regionIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('lastInfo', M('region')->where(array('id' => I('parent_id')))->find());
            $this->display('regionInfo');
        }
    }

    public function regionEdit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\WebsiteLogic();
            $res = $model->regionInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Website/regionIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('region')->where(array('id' => I('id')))->find());
            $this->display('regionInfo');
        }
    }

    public function saveRegionStatu() {
        if (IS_POST) {
            $res = M('region')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function regionDel() {
        $where = array('id' => array('in', I('id')));
        $num = M('region')->where(array('parent_id' => I('id')))->count();
        if ($num > 0) {
            $this->error('当前地区存在' . $num . '个下属地区，请勿删除');
        }
        $res = $row = M('region')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
