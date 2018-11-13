<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:40
 * Comment: 订单验证器
 */

namespace app\admin\validate;

class Order extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'            => 'number',
        'order_id'      => 'max:255',
        'amount_start'  => 'float',
        'amount_end'    => 'float',
        'pay_method'    => 'number',
        'status'        => 'number',
        'subject'       => 'max:300',
        'body'          => 'max:500',
        'create_start'  => 'date',
        'create_end'    => 'date',
        'update_start'  => 'date',
        'update_end'    => 'date',
        'page_size'     => 'number',
        'amount'        => 'float',
        'jump_page'     => 'number'
    ];

    /* 验证消息 */
    protected $field = [
        'id'            => '订单自增主键',
        'order_id'      => '订单主键',
        'amount_start'  => '订单金额起始区间',
        'amount_end'    => '订单金额截止区间',
        'pay_method'    => '订单支付方式',
        'status'        => '订单状态',
        'subject'       => '订单主题',
        'body'          => '订单描述',
        'create_start'  => '订单创建起始时间',
        'create_end'    => '订单创建截止时间',
        'update_start'  => '订单更新起始时间',
        'update_end'    => '订单更新截止时间',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页'
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id' => 'number', 'order_id' => 'max:255', 'amount_start' => 'float', 'amount_end' => 'float', 'pay_method' => 'number', 'status' => 'number', 'subject' => 'max:255', 'body' => 'max:500', 'create_start' => 'date', 'create_end' => 'date', 'update_start' => 'date', 'update_end' => 'date', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'          => ['id' => 'number', 'amount' => 'require|float', 'pay_method' => 'number', 'status' => 'require|number|in:0,1', 'subject' => 'require|max:255', 'body' => 'require|max:500'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number'],
        'supplement'    => ['order_id' => 'require|max:255']
    ];

}