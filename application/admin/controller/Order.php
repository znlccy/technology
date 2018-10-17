<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:40
 * Comment: 订单控制器
 */

namespace app\admin\controller;

use app\admin\model\Order as OrderModel;
use app\admin\response\Code;
use app\admin\validate\Order as OrderValidate;
use think\Request;

class Order extends BasisController {

    /* 声明订单模型 */
    protected $order_model;

    /* 声明订单验证器 */
    protected $order_validate;

    /* 声明订单分页器 */
    protected $order_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->order_model = new OrderModel();
        $this->order_validate = new OrderValidate();
        $this->order_page = config('pagination');
    }

    /* 订单列表 */
    public function listing() {

        /* 接收参数 */
        $id = request()->param('id');
        $order_id = request()->param('order_id');
        $amount_start = request()->param('amount_start');
        $amount_end = request()->param('amount_end');
        $pay_method = request()->param('pay_method');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size');
        $jump_page = request()->param('jump_page');

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'order_id'      => $order_id,
            'amount_start'  => $amount_start,
            'amount_end'    => $amount_end,
            'pay_method'    => $pay_method,
            'status'        => $status,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->order_validate->getError());
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($order_id) {
            $conditions['order_id'] = $order_id;
        }

        if ($amount_start && $amount_end) {
            $conditions['amount'] = ['between', [$amount_start, $amount_end]];
        }

        if (is_null($pay_method)) {
            $conditions['pay_method'] = ['in',[0,1]];
        } else {
            switch ($pay_method) {
                case 0:
                    $conditions['pay_method'] = $pay_method;
                    break;
                case 1:
                    $conditions['pay_method'] = $pay_method;
                    break;
                default:
                    break;
            }
        }

        if (is_null($status)) {
            $conditions['status'] = ['in',[0,1]];
        } else {
            switch ($status) {
                case 0:
                    $conditions['status'] = $status;
                    break;
                case 1:
                    $conditions['status'] = $status;
                    break;
                default:
                    break;
            }
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time', [$create_start, $create_end]];
        }

        if ($update_start && $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        /* 返回结果 */
        $order = $this->order_model
            ->where($conditions)
            ->order('id', 'asc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($order) {
            return $this->return_message(Code::SUCCESS, '获取订单列表成功', $order);
        } else {
            return $this->return_message(Code::FAILURE, '获取订单列表失败');
        }

    }

    /* 订单添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $order_id = 'TP'.date('YmdHis', time()).rand(11111,99999);
        $amount = request()->param('amount');
        $pay_method = request()->param('pay_method');
        $status = request()->param('status');
        $recharge_time = date('Y-m-d H:i:d', time());

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'pay_method'    => $pay_method,
            'status'        => $status,
            'recharge_time' => $recharge_time
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->order_validate->getError());
        }

        /* 返回结果 */
        if (empty($id)) {
            $order = $this->order_model->save($validate_data);
        } else {
            $order = $this->order_model->save($validate_data, ['id' => $id]);
        }

        if ($order) {
            return $this->return_message(Code::SUCCESS, '数据操作成功');
        } else {
            return $this->return_message(Code::FAILURE, '数据操作失败');
        }
    }

    /* 订单详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->order_validate->getError());
        }

        /* 返回结果 */
        $order = $this->order_model->where('id', '=', $id)->find();

        if ($order) {
            return $this->return_message(Code::SUCCESS,'获取订单详情成功', $order);
        } else {
            return $this->return_message(Code::FAILURE, '获得订单详情失败');
        }
    }

    /* 订单删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->order_validate->getError());
        }

        /* 返回结果 */
        $order = $this->order_model->where('id', '=', $id)->delete();

        if ($order) {
            return $this->return_message(Code::SUCCESS, '删除订单成功');
        } else {
            return $this->return_message(Code::FAILURE, '删除订单失败');
        }
    }

    /* 订单补单 */
    public function supplement() {

        /* 接收参数 */
        $order_id = request()->param('order_id');

        /* 验证数据 */
        $validate_data = [
            'order_id'      => $order_id
        ];

        /* 验证结果 */
        $result = $this->order_validate->scene('supplement')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->order_validate->getError());
        }

        /* 补单结果 */

        
    }
}