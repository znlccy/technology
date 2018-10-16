<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 11:51
 * Comment: 科技产品展示控制器
 */

namespace app\index\controller;

use think\Request;

class Display extends BasicController {

    protected $display_model;

    protected $display_validatel;

    public function __construct(Request $request = null) {
        parent::__construct($request);
    }


}