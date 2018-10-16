<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 15:08
 * Comment: 购买明细控制器
 */

namespace app\admin\controller;

use think\Request;

class Purchase extends BasisController {

    /* 声明 */
    protected $purchase_model;

    protected $purchase_validate;

    protected $purchase_page;

    public function __construct(Request $request = null) {
        parent::__construct($request);
    }


    public function listing() {

    }

    public function save() {

    }

    public function detail() {

    }

    public function delete() {

    }

    public function delivery() {

    }

    public function refund() {

    }
}

