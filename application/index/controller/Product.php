<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/17
 * Time: 15:56
 * Comment: 产品控制器
 */

namespace app\index\controller;

use app\index\model\Product as ProductModel;
use app\index\response\Code;
use app\index\validate\Product as ProductValidate;
use think\Request;

class Product extends BasicController {

    protected $product_model;

    /* 声明产品验证器 */
    protected $product_validate;

    /* 声明产品分页器 */
    protected $product_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->product_model = new ProductModel();
        $this->product_validate = new ProductValidate();
        $this->product_page = config('pagination');
    }

    /* 产品列表 */
    public function listing() {

        /* 接收参数 */
        $page_size = request()->param('page_size', $this->product_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->product_page['JUMP_PAGE']);

        /* 验证数据 */
        $validate_data = [
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->product_validate->getError());
        }

        /* 返回结果 */
        $product = $this->product_model
            ->order('id', 'desc')
            ->where('status', '=', '1')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($product) {
            return $this->return_message(Code::SUCCESS, '获取产品成功',$product);
        } else {
            return $this->return_message(Code::FAILURE, '获取产品失败');
        }
    }

    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->product_validate->getError());
        }

        /* 验证结果 */
        $product = $this->product_model->where('id', $id)->find();

        if ($product) {
            return $this->return_message(Code::SUCCESS, '获取产品详情成功', $product);
        } else {
            return $this->return_message(Code::FAILURE, '获取产品详情失败');
        }
    }

    /* 约谈合作方 */
    public function interview() {

        /* 接收参数 */
        $pid = request()->post('pid');
        $uid = request()->param('uid');

        /* 验证数据 */
        $validate_data = [
            'pid'       => $pid,
            'uid'       => $uid
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('interview')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->product_validate->getError());
        }

        /* 返回数据 */
    }
}