<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 10:58
 * Comment: 物流控制器
 */

namespace app\admin\controller;

use app\admin\model\Logistics as LogisticsModel;
use app\admin\response\Code;
use app\admin\validate\Logistics as LogisticsValidate;
use think\Request;

class Logistics extends BasisController {

    /* 声明物流模型 */
    protected $logistics_model;

    /* 声明物流验证器 */
    protected $logistics_validate;

    /* 声明物流分页 */
    protected $logistics_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->logistics_model = new LogisticsModel();
        $this->logistics_validate = new LogisticsValidate();
        $this->logistics_page = config('pagination');
    }

    /* 物流列表 */
    public function listing() {

    }

    /* 物流添加更新 */
    public function save() {

        /* 接收参数 */
    }

    /* 物流详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->logistics_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->logistics_validate->getError());
        }

        /* 返回结果 */
        $logistics = $this->logistics_model->where('id', '=', $id)->find();

        if ($logistics) {
            return $this->return_message(Code::SUCCESS, '获取物流信息成功', $logistics);
        } else {
            return $this->return_message(Code::FAILURE, '获取物流信息失败');
        }
    }

    /* 物流删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->logistics_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->logistics_validate->getError());
        }

        /* 返回结果 */
        $logistics = $this->logistics_model->where('id', '=', $id)->delete();
        
        if ($logistics) {
            return $this->return_message(Code::SUCCESS, '删除物流成功');
        } else {
            return $this->return_message(Code::FAILURE, '删除物流失败');
        }
    }

}