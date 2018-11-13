<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 11:32
 * Comment: 用户验证器
 */

namespace app\admin\validate;

class User extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'            => 'number',
        'type'          => 'number',
        'username'      => 'max:60',
        'mobile'        => 'max:30',
        'enterprise'    => 'max:300',
        'introduce'     => 'max:400',
        'industry'      => 'max:500',
        'capital'       => 'max:400',
        'revenue'       => 'number',
        'assets'        => 'number',
        'address'       => 'max:500',
        'duty'          => 'max:400',
        'department'    => 'max:400',
        'phone'         => 'max:300',
        'wechat'        => 'max:200',
        'email'         => 'email',
        'link'          => 'max:300',
        'location'      => 'max:400',
        'create_start'  => 'date',
        'create_end'    => 'date',
        'update_start'  => 'date',
        'update_end'    => 'date',
        'page_size'     => 'number',
        'jump_page'     => 'number',
    ];

    /* 验证消息 */
    protected $field = [
        'id'            => '用户主键',
        'type'          => '用户类型',
        'username'      => '用户姓名',
        'mobile'        => '用户手机',
        'enterprise'    => '用户企业',
        'introduce'     => '用户介绍',
        'industry'      => '用户所属行业',
        'capital'       => '注册资本',
        'revenue'       => '是否营收',
        'assets'        => '当前净资产',
        'address'       => '注册地址',
        'duty'          => '职务',
        'department'    => '所属部门',
        'phone'         => '用户电话',
        'wechat'        => '第三方QQ/微信',
        'email'         => '电子邮件',
        'link'          => '联系方式',
        'location'      => '联系地址',
        'create_start'  => '用户创建起始时间',
        'create_end'    => '用户创建截止时间',
        'update_start'  => '用户更新起始时间',
        'update_end'    => '用户更新截止时间',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页',
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id' => 'number', 'type' => 'number', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'          => ['id' => 'number', 'type' => 'require|number', 'username' => 'require|max:60', 'password' => 'require|min:8', 'confirm_password' => 'require|confirm:password', 'mobile' => 'require|max:13', 'enterprise' => 'max:300', 'introduce' => 'max:500', 'industry' => 'max:300', 'capital' => 'number', 'revenue' => 'number', 'assets' => 'number', 'address' => 'max:300', 'duty' => 'max:200', 'department' => 'max:300', 'phone' => 'max:30', 'wechat' => 'max:60', 'email' => 'email', 'link' => 'max:200', 'location' => 'max:400', 'textarea' => 'max:500'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number'],
    ];
}