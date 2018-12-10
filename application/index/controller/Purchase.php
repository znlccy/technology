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
use think\Log;
use think\Request;
use app\index\model\Purchase as PurchaseModel;
use app\index\model\Goods as GoodsModel;
use app\index\model\Product as ProductModel;
use app\index\model\UserCrowd as UserCrowdModel;
use app\index\model\Order as OrderModel;
use app\index\validate\Purchase as PurchaseValidate;

class Purchase extends BasicController {

    /* 支付模型 */
    protected $purchase_model;

    /* 声明商品模型 */
    protected $goods_model;

    /* 声明订单模型 */
    protected $order_model;

    /* 声明用户众筹模型 */
    protected $user_crowd_model;

    /* 支付验证 */
    protected $purchase_validate;

    /* 支付分页 */
    protected $purchase_page;

    /* 默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->purchase_model = new PurchaseModel();
        $this->user_crowd_model = new UserCrowdModel();
        $this->goods_model = new GoodsModel();
        $this->order_model = new OrderModel();
        $this->purchase_validate = new PurchaseValidate();
        $this->purchase_page = config('pagination');
    }

    /* 用户支付 */
    public function pay() {

        /* 接收参数 */
        $goods_id = request()->param('goods_id');
        $pay_method = request()->param('pay_method');
        $user_id = session('user.id');
//        Pingpp::setApiKey('sk_test_nbLa9SD84qfHezj1qD1WfPeT');
//        Pingpp::setPrivateKeyPath(APP_PATH.'private.pem');
        Pingpp::setApiKey('sk_test_DejXjD9Gqj5KHifDWDKCynHC');
        Pingpp::setPrivateKeyPath(APP_PATH . 'your_rsa_private_key.pem');

        /* 验证参数 */
        $validate_data = [
            'goods_id'    => $goods_id
        ];

        /* 验证结果 */
        $result = $this->purchase_validate->scene('pay')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->purchase_validate->getError());
        }

        $goods = $this->goods_model->where('id', $goods_id)->find();
        try {
            if ($goods) {
//                $app = array('id' => 'app_jj9irPO80arTrDOm');
                $app = array('id' => 'app_Lij9KOXDa18OGG08');
                $order_id = 'TP'.date('YmdHis', time()).rand(11111,99999);

                switch ($pay_method) {
                    case 1:
                        $channel = 'alipay_pc_direct';
                        $extra = ['success_url' => config('pay.success_url')];
                        break;
                    case 2:
                        $channel = 'wx_pub_qr';
                        $extra = ['product_id' => $goods_id];
                        break;
                    default:
                        break;
                }

                $order_data = [
                    'order_id'    => $order_id,
                    'amount'      => $goods['price'],
                    'pay_method'  => $pay_method,
                    'subject'     => $goods['title'],
                    'body'        => $goods['title'],
                    'status'      => 0
                ];

                /* 创建订单 */
                $order = $this->order_model->save($order_data);

                if ($order) {
                    /*dump($order);die();*/
                    /* 支付订单 */
                    $charge = Charge::create(array(
                        'order_no'      => $order_id,
                        'amount'        => $goods['price'],
                        'channel'       => $channel,
                        'currency'      => 'cny',
                        'client_ip'     => request()->ip(),
                        'body'          => $goods['title'],
                        'subject'       => $goods['title'],
                        'app'           => $app,
                        'extra'         => $extra
                    ));
                    /* 保存到本地数据库 */
                    $purchase_data = [
                        'order_id'      => $order_id,
                        'user_id'       => $user_id,
                        'product_id'    => $goods_id,
                        'status'        => 0,
                        'charge_amount' => $goods['price'],
                        'charge_time'   => date('Y-m-d H:i:s', time())
                    ];
                    $purchase = $this->purchase_model->save($purchase_data);
                    if ($purchase) {
                        echo $charge;
//                        return $this->return_message(Code::SUCCESS, '保存本地数据成功');
                    } else {
                        return $this->return_message(Code::SUCCESS, '保存本地数据失败');
                    }
                }
            } else {
                return $this->return_message(Code::FAILURE, '不存在该商品');
            }
        } catch (\Pingpp\Error\Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                return json(['code' => 401, 'message' => $e->getHttpBody()]);
            } else {
                return json(['code' => 401, 'message' => $e->getMessage()]);
            }
//            return $this->return_message(Code::FAILURE, '创建订单失败');
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
        if (!isset($event['type'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        $charge = $event['data']['object'];
        switch ($event['type']) {
            case "charge.succeeded":
                // 开发者在此处加入对支付异步通知的处理代码
//                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                // 更新订单状态
                $order_id =  $charge['order_no'];
                $order = $this->order_model->save(['status' => 1], ['order_id' => $order_id]);
                $purchase = $this->purchase_model->save(['status' => 1], ['order_id' => $order_id]);
                $crowd = db('tb_goods')
                    ->alias('to')
                    ->join('tb_purchase tp', 'to.id = tp.product_id')
                    ->where('tp.order_id', '=', $order_id)
                    ->field('to.crowd_id, tp.user_id')
                    ->find();
                if ($crowd) {
                    $user_crowd = $this->user_crowd_model->save(['user_id' => $crowd['user_id'], 'crowd_id' => $crowd['crowd_id']]);
                }
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                $order_id =  $charge['order_no'];
                $order = $this->order_model->save(['status' => 2], ['order_id' => $order_id]);
                break;
        }
    }

    public function success_return()
    {

    }
}