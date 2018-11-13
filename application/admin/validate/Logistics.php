<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 11:00
 * Comment: 物流验证器
 */

namespace app\admin\validate;

class Logistics extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'        => 'number',
        'name'      => 'max:255',
        'code'      => 'number',
        'page_size' => 'number',
        'jump_page' => 'number'
    ];

    /* 验证字段 */
    protected $field = [
        'id'        => '物流主键',
        'name'      => '物流名称',
        'code'      => '物流代码',
        'page_size' => '分页大小',
        'jump_page' => '跳转页'
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id' => 'number', 'name' => 'max:255', 'code' => 'number', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'          => ['id' => 'number', 'name' => 'require|max:255', 'code' => 'require|number'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number'],
    ];
}