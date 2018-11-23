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
use Pingpp\Charge;
use Pingpp\Pingpp;
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
        $user_id = session('user.id');
        Pingpp::setApiKey('sk_test_nbLa9SD84qfHezj1qD1WfPeT');
        Pingpp::setPrivateKeyPath(APP_PATH.'private.pem');

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
            $app = array('id' => 'app_jj9irPO80arTrDOm');
            $order_id = 'TP'.date('YmdHis', time()).rand(11111,99999);

            switch ($pay_method) {
                case 1:
                    $channel = 'alipay_pc_direct';
                    $extra = ['success_url' => config('charge.success_url')];
                    break;
                case 2:
                    $channel = 'wx_pub_qr';
                    $extra = ['product_id' => $product_id];
                break;
                default:
                    break;
            }

            $order_data = [
                'order_id'    => $order_id,
                'amount'      => $product['price'],
                'pay_method'  => $pay_method,
                'subject'     => $product['title'],
                'body'        => $product['title'],
                'status'      => 0
            ];

            /* 创建订单 */
            $order = $this->order_model->save($order_data);

            if ($order) {
                /*dump($order);die();*/
                /* 支付订单 */
                $charge = Charge::create(array(
                    'order_no'      => $order_id,
                    'amount'        => $product['price'],
                    'channel'       => $channel,
                    'currency'      => 'cny',
                    'client_ip'     => request()->ip(),
                    'body'          => $product['title'],
                    'subject'       => $product['title'],
                    'app'           => $app,
                    'extra'         => $extra
                ));

                /* 保存到本地数据库 */
                $purchase_data = [
                    'order_id'      => $order_id,
                    'user_id'       => $user_id,
                    'product_id'    => $product_id,
                    'status'        => 0,
                    'charge_amount' => $product['price'],
                    'charge_time'   => date('Y-m-d H:i:s', time())
                ];
                $purchase = $this->purchase_model->save($purchase_data);
                if ($purchase) {
                    return $this->return_message(Code::SUCCESS, '保存本地数据成功');
                } else {
                    return $this->return_message(Code::SUCCESS, '保存本地数据失败');
                }
            } else {
                return $this->return_message(Code::FAILURE, '创建订单失败');
            }

        } else {
            return $this->return_message(Code::FAILURE, '不存在该商品');
        }

        /* 验证订单，写回数据库 */
    }

    public function notify() {
        $raw_data = file_get_contents('php://input');
        $headers = \Pingpp\Util\Util::getRequestHeaders();
        // 签名在头部信息的 x-pingplusplus-signature 字段
        $signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : NULL;
        // 验证签名
        $pub_key_path = APP_PATH . "/pingplusplus_public_key.pem";
        $pub_key_contents = file_get_contents($pub_key_path);
        $verify_result = openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, 'sha256');
        if ($verify_result === 1) {
            // 验证通过
        } elseif ($verify_result === 0) {
            http_response_code(400);
            echo 'verification failed';
            exit;
        } else {
            http_response_code(400);
            echo 'verification error';
            exit;
        }
        $event = json_decode($raw_data, true);
        // 对异步通知做处理
        if (!isset($event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        switch ($event->type) {
            case "charge.succeeded":
                // 开发者在此处加入对支付异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
    }
}