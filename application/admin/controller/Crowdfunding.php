<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:32
 * Comment: 众筹控制器
 */

namespace app\admin\controller;

use app\admin\model\Crowdfunding as CrowdfundingModel;
use app\admin\validate\Crowdfunding as CrowdfundingValidate;
use think\Request;

class Crowdfunding extends BasisController {

    /* 声明众筹模型 */
    protected $crowdfunding_model;

    /* 声明众筹验证器 */
    protected $crowdfunding_validate;

    /* 声明众筹分页器 */
    protected $crowdfunding_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->crowdfunding_model = new CrowdfundingModel();
        $this->crowdfunding_validate = new CrowdfundingValidate();
        $this->crowdfunding_page = config('pagination');
    }

    /* 众筹列表 */
    public function listing() {

        /* 接收参数 */
    }

    /* 众筹添加更新 */
    public function save() {

        /* 接收参数 */
    }

    /* 众筹详情 */
    public function detail() {

        /* 接收参数 */
    }

    /* 众筹删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('delete')->check($validate_data);

        if (true !== $result) {

        }
    }

    /* 众筹审核 */
    public function auditing() {

    }

}