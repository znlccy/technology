<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 18:06
 * Comment: 支付控制器
 */

namespace app\index\controller;

use think\Request;

class Purchase extends BasicController {

    /* 支付模型 */
    protected $purchase_model;

    /* 支付验证 */
    protected $purchase_validate;

    /* 支付分页 */
    protected $purchase_page;

    /* 默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
    }

    public function pay() {
        $order_no = request()->param('order_no');
        $charge_type = request()->param('charge_type');
        // 获取订单信息
        $order_type = mb_substr($order_no,0,2);
        if ($order_type == 'XS') {
            // 销售订单
            $order_type = 1;
            $order_info = RecOrder::where('order_no', $order_no)->find();
        } else {
            // 杂费订单
            $order_type = 2;
            $order_info = IncidentalOrder::where('order_no', $order_no)->find();
        }
        if (!$order_info) {
            return json(['code' => 401, 'message' => '订单号有误']);
        }
        switch ($charge_type) {
            case 1:
                // 支付宝电脑网站支付
                $channel = 'alipay_pc_direct';
                $extra = ['success_url' => config('charge.success_url')];
                break;
            case 2:
                // 微信扫码支付
                $channel = 'wx_pub_qr';
                $extra = ['product_id' => $order_no];
                break;
            case 3:
                // 支付宝扫码支付
                $channel = 'alipay_qr';
                break;
            case 4:
                // 微信公众号支付
                $channel = 'wx_pub_qr';
                $extra = ['open_id' => ''];
                break;
            default:
                $channel = '';
                break;
        }
        // 查询order对象
        $charge_record = ChargeRecord::where('order_no', $order_no)->find();
        if ($charge_record) {
            // 返回的charge对象
            $charge_id = $charge_record['channel_order_no'];
            try {
                $charge = \Pingpp\Charge::retrieve($charge_id);
                if ($charge_type === 2) {
                    $data = [];
                    $data['wx_pub_qr'] = $charge['credential']['wx_pub_qr'];
                    return json(['code' => 200, 'data' => $data]);
                } else {
                    echo $charge;
                }

            } catch (\Pingpp\Error\Base $e) {
                if ($e->getHttpStatus() != null) {
                    header('Status: ' . $e->getHttpStatus());
                    return json(['code' => 401, 'message' => $e->getHttpBody()]);
                } else {
                    return json(['code' => 401, 'message' => $e->getMessage()]);
                }
            }
        } else {
            // 在ping++平台创建charge对象
            try{
                $charge = \Pingpp\Charge::create(
                    array(
                        'amount' => intval($order_info['price'] * 100),
                        'app' => ['id' => $this->app_id],
                        'order_no' => $order_no, // 商户订单号
                        'subject' => '租赁订单',
                        'currency' => 'cny',
                        'body' => 'body',
                        'channel' => $channel,
                        'extra' => $extra,
                        'client_ip' => $_SERVER['REMOTE_ADDR']
                    )
                );
                // 创建收款记录
                $data = [
                    'order_no' => $order_no,
                    'order_type' => $order_type,
                    'channel_order_no' => $charge['id'],
                    'charge_amount' => $order_info['price'],
                    'charge_type' => $charge_type,
                    'status' => 0
                ];
                $result = $this->validate($data, 'Purchase');
                if (true != $result) {
                    return json(['code' => 401, 'message' => $result]);
                }
                $record = new ChargeRecord();
                $record->save($data);
                // Ping++ 返回的order 对象的id
                if ($charge_type === 2) {
                    $data = [];
                    $data['wx_pub_qr'] = $charge['credential']['wx_pub_qr'];
                    return json(['code' => 200, 'data' => $data]);
                } else {
                    echo $charge;
                }
                $order_id = $charge['id'];
            } catch (\Pingpp\Error\Base $e) {
                // 捕获报错信息
                if ($e->getHttpStatus() != null) {
                    header('Status: ' . $e->getHttpStatus());
                    return json(['code' => 401, 'message' => $e->getHttpBody()]);
                } else {
                    return json(['code' => 401, 'message' => $e->getMessage()]);
                }
            }
        }
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