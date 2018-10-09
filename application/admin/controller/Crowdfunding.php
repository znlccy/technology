<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:32
 * Comment: 众筹控制器
 */

namespace app\admin\controller;

use think\Request;

class Crowdfunding extends BasisController {

    public function __construct(Request $request = null) {
        parent::__construct($request);
    }

    public function listing() {
        echo 'Hello world';
    }

}