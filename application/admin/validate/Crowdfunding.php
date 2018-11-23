<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:39
 * Comment: 众筹验证器
 */

namespace app\admin\validate;

class Crowdfunding extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'            => 'number',
        'title'         => 'max:255',
        'status'        => 'number|in:0,1',
        'create_start'  => 'date',
        'create_end'    => 'date',
        'update_start'  => 'date',
        'update_end'    => 'date',
        'expired_start' => 'date',
        'expired_end'   => 'date',
        'target_amount' => 'number',
        'expired_time'  => 'date',
        'page_size'     => 'number',
        'jump_page'     => 'number',
    ];

    /* 验证消息 */
    protected $field = [
        'id'            => '众筹主键',
        'title'         => '众筹标题',
        'status'        => '众筹状态',
        'create_start'  => '众筹创建起始时间',
        'create_end'    => '众筹创建截止时间',
        'update_start'  => '众筹更新起始时间',
        'update_end'    => '众筹更新截止时间',
        'expired_start' => '众筹过期起始时间',
        'expired_end'   => '众筹过期截止时间',
        'target_amount' => '目标融资',
        'expired_time'  => '过期时间',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页',
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id' => 'number', 'title' => 'max:255', 'status' => 'number|in:0,1', 'create_start' => 'date', 'create_end' => 'date', 'update_start' => 'date', 'update_end' => 'date', 'expired_start' => 'date', 'expired_end' => 'date', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'          => ['id' => 'number', 'title' => 'require', 'target_amount' => 'require|number', 'status' => 'require|number'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number'],
        'auditing'      => ['id' => 'require|number', 'status' => 'require|number'],
    ];
}