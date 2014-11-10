<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 08/11/14
 * Time: 21:14
 */


class PoAdminClass extends ModuleAdminController {

    public function __construct() {
        $this->module = 'admintabmodule';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->bootstrap = true;

        parent::__construct();
    }

    public function initContent()
    {

    }
}

