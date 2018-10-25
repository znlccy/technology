<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 15:08
 * Comment: 购买明细控制器
 */

namespace app\admin\controller;

use app\admin\model\Purchase as PurchaseModel;
use app\admin\response\Code;
use app\admin\model\Order as OrderModel;
use app\admin\validate\Purchase as PurchaseValidate;
use Pingpp\Charge;
use Pingpp\Pingpp;
use think\Request;

class Purchase extends BasisController {

    /* 声明支付模型 */
    protected $purchase_model;

    /* 声明订单模型 */
    protected $order_model;

    /* 声明支付验证器 */
    protected $purchase_validate;

    /* 声明支付分页 */
    protected $purchase_page;

    /* 声明支付实例 */
    protected $pay;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->purchase_model = new PurchaseModel();
        $this->order_model = new OrderModel();
        $this->purchase_validate = new PurchaseValidate();
        $this->purchase_page = config('pagination');
        $this->pay = config('pay');
    }

    /* 支付列表 */
    public function listing() {

    }

    /* 支付添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $crowd_id = request()->param('crowd_id');
        $user_id = request()->param('user_id');
        $amount = request()->param('amount');
        $pay_time = date("Y-m-d H:i:s", time());
        $order_id = request()->param('order_id');
        $status = request()->param('status');
        $remark = request()->param('remark');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id,
            'crowd_id'  => $crowd_id,
            'user_id'   => $user_id,
            'amount'    => $amount,
            'pay_time'  => $pay_time,
            'order_id'  => $order_id,
            'status'    => $status,
            'remark'    => $remark
        ];

        /*  */

    }

    /* 支付详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->purchase_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->purchase_validate->getError());
        }

        /* 返回结果 */
    }

    /* 支付删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->purchase_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->purchase_validate->getError());
        }

        /* 返回结果 */
        $purchase = $this->purchase_model->where('id', $id)->delete();

        if ($purchase) {
            return $this->return_message(Code::SUCCESS,'删除数据成功');
        } else {
            return $this->return_message(Code::FAILURE,'删除数据失败');
        }
    }

    /* 支付发货 */
    public function delivery() {

        /* 接收参数 */
        $order_id = request()->param('order_id');
        $logistic_id = request()->param('logistic_id');

        /* 验证参数 */
        $validate_data = [
            'order_id'      => $order_id,
            'logistic_id'   => $logistic_id
        ];

        /* 验证结果 */
        $result = $this->purchase_validate->scene('delivery')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->purchase_validate->getError());
        }

        /* 返回结果 */
    }

    /* 支付退款 */
    public function refund() {

        /* 接收参数 */
        Pingpp::setApiKey($this->pay['apiKey']);
        Pingpp::setPrivateKeyPath(APP_PATH.'private.pem');
        $order_id = request()->param('order_id');
        $description = request()->param('description');

        /* 验证参数 */
        $validate_data = [
            'order_id'      => $order_id,
            'description'   => $description
        ];

        /* 验证结果 */
        $result = $this->purchase_validate->scene('refund')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->purchase_validate->getError());
        }

        /* 实现退款 */
        $order = $this->order_model->where('order_id', $order_id)->find();

        if ($order['status'] == 0) {
            return $this->return_message(Code::EXPIRED, '订单还没支付，无法退款');
        }

        if ($order['status'] == 1) {
            try {
                $charge = Charge::retrieve($order_id);
                $refund = $charge->refunds->create(
                    array(
                        'amount' => $order['amount'],
                        'description' => $description
                    )
                );
            } catch (\Exception $e) {
                return $this->return_message(Code::INVALID, '不存在支付记录');
            }
            return $this->return_message(Code::SUCCESS, '退款成功', $refund);
        }
    }

    /* 订单支付 */
    public function recharge() {

        /* 接收参数 */
        Pingpp::setApiKey($this->pay['apiKey']);
        Pingpp::setPrivateKeyPath(APP_PATH.'private.pem');
        $charge = Charge::create(array('order_no'  => 'TP2018101617372031773',
                'amount'    => '1',//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                'app'       => array('id' => $this->pay['appId']),
                'channel'   => 'alipay',
                'currency'  => 'cny',
                'client_ip' => '127.0.0.1',
                'subject'   => 'Your Subject',
                'body'      => 'Your Body')
        );

        return json([
            'code'      => '200',
            'message'   => '支付成功',
            'data'      => $charge
        ]);
    }

}
