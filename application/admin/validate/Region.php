<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 16:02
 * Comment: 区域验证器
 */

namespace app\admin\validate;

class Region extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'level'     => 'number',
        'id'        => 'number'
    ];

    /* 验证消息 */
    protected $field = [
        'level'     => '地区等级',
        'id'        => '地区主键'
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'   => ['level' => 'number', 'id' => 'number']
    ];

}