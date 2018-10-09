<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:55
 * Comment: 基础验证器
 */

namespace app\admin\validate;

use think\Validate;

class BasisValidate extends Validate {

    /* 手机正则表达式 */
    protected $regex = [ 'mobile' => '/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/'];

}