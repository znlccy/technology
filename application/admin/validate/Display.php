<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:41
 * Comment: 科技产品展示验证器
 */

namespace app\admin\validate;

class Display extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'            => 'number',
        'name'          => 'max:255',
        'recommend'     => 'number|in:0,1',
        'status'        => 'number|in:0,1',
        'create_start'  => 'date',
        'create_end'    => 'date',
        'update_start'  => 'date',
        'update_end'    => 'date',
        'page_size'     => 'number',
        'jump_page'     => 'number',
    ];

    /* 验证消息 */
    protected $field = [
        'id'            => '展示主键',
        'name'          => '展示名称',
        'recommend'     => '展示是否推荐',
        'status'        => '展示状态',
        'create_start'  => '展示创建起始时间',
        'create_end'    => '展示创建截止时间',
        'update_start'  => '展示更新起始时间',
        'update_end'    => '展示更新截止时间',
        'rich_text'     => '展示富文本',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页',
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id' => 'number', 'name' => 'max:255', 'recommend' => 'number|in:0,1', 'status' => 'number|in:0,1', 'create_start' => 'date', 'create_end' => 'date', 'update_start' => 'date', 'update_end' => 'date', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'          => ['id' => 'number', 'name' => 'max:255', 'recommend' => 'require|number|in:0,1', 'status' => 'require|number|in:0,1'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number'],
    ];
}

