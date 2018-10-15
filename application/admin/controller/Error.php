<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 12:00
 * Comment: 错误控制器
 */

namespace app\admin\controller;

class Error extends BasisController {

    /* 返回空操作 */
    public function _empty() {
        return json([
            'code'      => '401',
            'message'   => '您操作不当，不存在当前控制器或方法'
        ]);
    }
}