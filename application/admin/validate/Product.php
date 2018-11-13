<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:46
 * Comment: 产品验证器
 */

namespace app\admin\validate;

class Product extends BasisValidate {

    /* 验证规则 */
    protected $rule = [
        'id'            => 'number',
        'title'         => 'max:255',
        'region'        => 'max:300',
        'industry'      => 'max:400',
        'turnover_start'=> 'number',
        'turnover_end'  => 'number',
        'assets_start'  => 'number',
        'assets_end'    => 'number',
        'purpose'       => 'max:500',
        'amount_start'  => 'number',
        'amount_end'    => 'number',
        'recommend'     => 'number',
        'status'        => 'number',
        'create_start'  => 'date',
        'create_end'    => 'date',
        'update_start'  => 'date',
        'update_end'    => 'date',
        'page_size'     => 'number',
        'jump_page'     => 'number',
        'turnover'      => 'number',
        'amount'        => 'number',
    ];

    /* 验证消息 */
    protected $field = [
        'id'            => '产品主键',
        'title'         => '产品标题',
        'region'        => '产品区域',
        'industry'      => '产品行业',
        'turnover_start'=> '去年营业额起始区间',
        'turnover_end'  => '去年营业额截止区间',
        'assets_start'  => '当前净资产起始区间',
        'assets_end'    => '当前净资产截止区间',
        'purpose'       => '融资用途',
        'amount_start'  => '融资金额起始区间',
        'amount_end'    => '融资金额截止区间',
        'recommend'     => '产品推荐',
        'status'        => '产品状态',
        'create_start'  => '产品创建起始时间',
        'create_end'    => '产品创建截止时间',
        'update_start'  => '产品更新起始时间',
        'update_end'    => '产品更新截止时间',
        'page_size'     => '分页大小',
        'jump_page'     => '跳转页',
        'turnover'      => '去年营业额',
        'amount'        => '融资金额',
    ];

    /* 验证场景 */
    protected $scene = [
        'listing'       => ['id' => 'number', 'title' => 'max:255', 'region' => 'max:300', 'industry' => 'max:400', 'turnover_start' => 'number', 'turnover_end' => 'number', 'assets_start' => 'number', 'assets_end' => 'number', 'purpose' => 'max:500', 'amount_start' => 'number', 'amount_end' => 'number', 'recommend' => 'number', 'status' => 'number', 'create_start' => 'date', 'create_end' => 'date', 'update_start' => 'date', 'update_end' => 'date', 'page_size' => 'number', 'jump_page' => 'number'],
        'save'          => ['id' => 'number', 'title' => 'require|max:255', 'region' => 'require|max:500', 'industry' => 'require|max:400', 'turnover' => 'require|number', 'assets' => 'require|number', 'purpose' => 'require|max:500', 'amount' => 'require|number', 'recommend' => 'require|number', 'status' => 'require|number'],
        'detail'        => ['id' => 'require|number'],
        'delete'        => ['id' => 'require|number'],
        'allocation'    => ['pid' => 'require|number', 'uid' => 'require|number'],
        'auditing'      => ['id' => 'require|number', 'status' => 'require|number']
    ];
}