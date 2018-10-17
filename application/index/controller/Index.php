<?php
namespace app\index\controller;

use app\index\model\Carousel as CarouselModel;
use app\index\model\Display as DisplayModel;
use app\index\model\Product as ProductModel;
use app\index\model\Crowdfunding as CrowdfundingModel;
use app\index\response\Code;
use think\Request;

class Index extends BasicController {

    /* 声明轮播模型 */
    protected $carousel_model;

    /* 声明科技产品模型 */
    protected $display_model;

    /* 声明产品模型 */
    protected $product_model;

    /* 声明众筹模型 */
    protected $crowdfunding_model;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->carousel_model = new CarouselModel();
        $this->display_model = new DisplayModel();
        $this->product_model = new ProductModel();
        $this->crowdfunding_model = new CrowdfundingModel();
    }

    /* 首页展示 */
    public function index() {

        /* 返回轮播 */
        $carousel = $this->carousel_model
            ->order('id', 'desc')
            ->where('status', '=', '1')
            ->limit(4)
            ->select();

        /* 返回科技产品 */
        $display = $this->display_model
            ->order('id', 'desc')
            ->where('status', '=', '1')
            ->limit(3)
            ->select();

        /* 返回产品 */
        $product = $this->product_model
            ->order('id', 'desc')
            ->where('status', '=', '1')
            ->limit(3)
            ->select();

        /* 返回众筹 */
        $crowdfunding = $this->crowdfunding_model
            ->order('id', 'desc')
            ->limit(3)
            ->select();

        /* 返回最后数据 */
        $index = array_merge(['carousel' => $carousel, 'display' => $display, 'product' => $product, 'crowdfunding' => $crowdfunding]);

        if ($index) {
            return $this->return_message(Code::SUCCESS, '获取首页数据成功', $index);
        } else {
            return $this->return_message(Code::FAILURE, '获取首页数据失败');
        }
    }
}
