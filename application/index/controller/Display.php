<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 11:51
 * Comment: 科技产品展示控制器
 */

namespace app\index\controller;

use app\index\model\Display as DisplayModel;
use app\index\response\Code;
use app\index\validate\Display as DisplayValidate;
use think\Request;

class Display extends BasicController {

    /* 科技产品展示模型 */
    protected $display_model;

    /* 科技产品展示验证器 */
    protected $display_validate;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->display_model = new DisplayModel();
        $this->display_validate = new DisplayValidate();
    }

    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->display_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->display_validate->getError());
        }

        /* 返回结果 */
        $display = $this->display_model->where('id', $id)->find();

        if ($display) {
            return $this->return_message(Code::SUCCESS, '获取科技产品成功',$display);
        } else {
            return $this->return_message(Code::FAILURE, '获取科技产品失败');
        }
    }

    /* 科技产品更多 */
    public function listing() {

        /* 接收参数 */
        $page_size = request()->param('page_size');
        $jump_page = request()->param('jump_page');

        /* 验证数据 */
        $validate_data = [
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->display_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->display_validate->getError());
        }

        /* 返回数据 */
        $display = $this->display_model
            ->where('status', '=','1')
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($display) {
            return $this->return_message(Code::SUCCESS, '获取科技产品成功', $display);
        } else {
            return $this->return_message(Code::FAILURE, '获取科技产品失败');
        }
    }

}