<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 15:58
 * Comment: 区域控制器
 */

namespace app\admin\controller;

use app\admin\model\Region as RegionModel;
use app\admin\response\Code;
use app\admin\validate\Region as RegionValidate;
use think\Request;

class Region extends BasisController {

    /* 声明区域模型 */
    protected $region_model;

    /* 声明区域验证器 */
    protected $region_validate;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->region_model = new RegionModel();
        $this->region_validate = new RegionValidate();
    }

    /* 省市三级联动列表 */
    public function listing() {

        /* 接收参数 */
        $level = request()->param('level');
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id,
            'level'     => $level
        ];

        /* 验证结果 */
        $result = $this->region_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->region_validate->getError());
        }

        if (empty($level)) {
            return $this->return_message(Code::FAILURE, '获取地区列表失败');
        }

        switch ($level) {
            case 1:

                $region = $this->region_model->where('level', '=', $level)->field('id,name')->order('id')->select();
                break;
            case 2:
                if (empty($id)) {
                    return $this->return_message(Code::FAILURE, '获取地区列表失败');
                }
                $region = $this->region_model->where(['level' => $level, 'top_id' => $id])->field('id,name')->order('id')->select();
                break;
            case 3:
                if (empty($id)) {
                    return $this->return_message(Code::FAILURE, '获取地区列表失败');
                }
                $region = $this->region_model->where(['level' => $level, 'top_id' => $id])->field('id,name')->order('id')->select();
                break;
            default:
                return $this->return_message(Code::FAILURE, '获取地区列表失败');

        }

        /* 返回数据 */
        if (!empty($region)) {
            return $this->return_message(Code::SUCCESS, '地区列表获取成功',$region);
        } else {
            return $this->return_message(Code::FAILURE, '地区列表获取失败');
        }
    }

}