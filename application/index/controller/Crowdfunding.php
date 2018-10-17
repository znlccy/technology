<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/17
 * Time: 16:55
 * Comment: 众筹控制器
 */

namespace app\index\controller;

use app\index\model\Crowdfunding as CrowdfundingModel;
use app\index\response\Code;
use app\index\validate\Crowdfunding as CrowdfundingValidate;
use think\Request;

class Crowdfunding extends BasicController {

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
        $page_size = request()->param('page_size', $this->crowdfunding_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->crowdfunding_page['JUMP_PAGE']);

        /* 验证数据 */
        $validate_data = [
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        /* 返回结果 */
        $crowdfunding = $this->crowdfunding_model
            ->order('id', 'desc')
            ->where('status', '=', '1')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($crowdfunding) {
            return $this->return_message(Code::SUCCESS, '获取众筹成功', $crowdfunding);
        } else {
            return $this->return_message(Code::FAILURE, '获取众筹失败');
        }
    }

    /* 众筹详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        /* 返回数据 */
        $crowdfunding = $this->crowdfunding_model->where('id', $id)->find();

        if ($crowdfunding) {
            return $this->return_message(Code::SUCCESS, '获取众筹详情成功', $crowdfunding);
        } else {
            return $this->return_message(Code::FAILURE, '获取众筹详情失败');
        }
    }

}