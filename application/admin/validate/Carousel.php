<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:44
 * Comment: 轮播验证器
 */

namespace app\admin\validate;

class Carousel extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'            => 'number',
        'title'         => 'max:255',
        'sort'          => 'number',
        'description'   => 'max:5000',
        'create_start'  => 'date',
        'create_end'    => 'date',
        'update_start'  => 'date',
        'update_end'    => 'date',
        'publish_start' => 'date',
        'publish_end'   => 'date',
        'status'        => 'number|in:0,1',
        'url'           => 'url',
        'publish_time'  => 'date',
        'page_size'     => 'number',
        'jump_page'     => 'number'
    ];

    /* 验证消息 */
    protected $field = [
        'id'            => '轮播主键',
        'title'         => '轮播标题',
        'sort'          => '轮播排序',
        'create_start'  => '轮播创建起始时间',
        'create_end'    => '轮播创建截止时间',
        'update_start'  => '轮播更新起始时间',
        'update_end'    => '轮播更新截止时间',
        'publish_start' => '轮播发布起始时间',
        'publish_end'   => '轮播发布截止时间',
        'status'        => '轮播状态',
        'url'           => '轮播网址',
        'description'   => '轮播描述',
        'publish_time'  => '轮播发布时间',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页'
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id', 'title', 'sort', 'create_start', 'create_end', 'update_start', 'update_end', 'publish_start', 'publish_end', 'status', 'page_size', 'jump_page'],
        'save'          => ['id' => 'number', 'title' => 'require', 'description' => 'require', 'url' => 'require', 'sort' => 'require', 'status' => 'require', 'publish_time' => 'date'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number']
    ];
}