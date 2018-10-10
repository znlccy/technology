<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:54
 * Comment: 基础控制器
 */

namespace app\admin\controller;

use think\Controller;

class BasisController extends Controller {



    /* 返回状态信息 */
    public function return_message($code = '200', $message = '', $data = []) {

        if (is_null($data) || empty($data)) {
            return json([
                'code'      => $code,
                'message'   => $message
            ]);
        } else {
            return json([
                'code'      => $code,
                'message'   => $message,
                'data'      => $data
            ]);
        }
    }
}