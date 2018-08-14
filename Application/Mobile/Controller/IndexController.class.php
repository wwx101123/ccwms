<?php

namespace Mobile\Controller;

class IndexController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $this->display('index');
    }

}
