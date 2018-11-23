<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 18:06
 * Comment: 支付控制器
 */

namespace app\index\controller;

use app\index\response\Code;
use think\Request;
use app\index\model\Purchase as PurchaseModel;
use app\index\model\Product as ProductModel;
use app\index\model\Order as OrderModel;
use app\index\validate\Purchase as PurchaseValidate;

class Purchase extends BasicController {

    /* 支付模型 */
    protected $purchase_model;

    /* 声明产品模型 */
    protected $product_model;

    /* 声明订单模型 */
    protected $order_model;

    /* 支付验证 */
    protected $purchase_validate;

    /* 支付分页 */
    protected $purchase_page;

    /* 默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->purchase_model = new PurchaseModel();
        $this->product_model = new ProductModel();
        $this->order_model = new OrderModel();
        $this->purchase_validate = new PurchaseValidate();
        $this->purchase_page = config('pagination');
    }

    /* 用户支付 */
    public function pay() {

        /* 接收参数 */
        $product_id = request()->param('product_id');
        $pay_method = request()->param('pay_method');

        /* 验证参数 */
        $validate_data = [
            'product_id'    => $product_id
        ];

        /* 验证结果 */
        $result = $this->purchase_validate->scene('pay')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->purchase_validate->getError());
        }

        $product = $this->product_model->where('id', $product_id)->find();

        if ($product) {
            $order_id = 'TP'.date('YmdHis', time()).rand(11111,99999);
            $amount = $product['price'];


        } else {
            return $this->return_message(Code::FAILURE, '不存在该商品');
        }

        /* 首先创建订单 */

        /* 支付订单，支付方式 */

        /* 验证订单，写回数据库 */
    }

    /**
     * 查询charge 对象
     */
    public function retrieve()
    {
        $order_no = request()->param('order_no');
        if (empty($order_no)) {
            return json(['code' => 401, 'message' => '订单号必须填写']);
        }
        $charge_id = ChargeRecord::where('order_no', $order_no)->value('channel_order_no');
        try {
            $charge = \Pingpp\Charge::retrieve($charge_id);
            if ($charge && $charge->paid) {
                return json(['code' => 200, 'message' => '支付成功']);
            } else {
                return json(['code' => 400, 'message' => '未支付']);
            }
//            return json(['code' => 200, 'data' => $charge]);
        } catch (\Pingpp\Error\Base $e) {
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                return json(['code' => 404, 'message' => $e->getHttpBody()]);
            } else {
                return json(['code' => 404, 'message' => $e->getMessage()]);
            }
        }
    }

    function verify_signature($raw_data, $signature, $pub_key_path) {
        $pub_key_contents = file_get_contents($pub_key_path);
        return openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, 'sha256');
    }

    /**
     * 支付成功回调接口
     */
    public function success_return()
    {
        $charge_result = request()->param('result');
        $order_no = request()->param('out_trade_no');
        if ($charge_result === 'success') {
            return json(['code' => 200, 'message' => '支付成功']);
        }
        return json(['code' => 404, 'message' => '支付失败']);
    }
}