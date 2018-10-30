<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:32
 * Comment: 众筹控制器
 */

namespace app\admin\controller;

use app\admin\model\Crowdfunding as CrowdfundingModel;
use app\admin\response\Code;
use app\admin\validate\Crowdfunding as CrowdfundingValidate;
use think\Request;

class Crowdfunding extends BasisController {

    /* 声明众筹模型 */
    protected $crowdfunding_model;

    /* 声明众筹验证器 */
    protected $crowdfunding_validate;

    /* 声明众筹分页器 */
    protected $crowdfunding_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->crowdfunding_model = new CrowdfundingModel();
        $this->crowdfunding_validate = new CrowdfundingValidate();
        $this->crowdfunding_page = config('pagination');
    }

    /* 众筹列表 */
    public function listing() {

        /* 接收参数 */
        $id = request()->param('id');
        $title = request()->param('title');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $expired_start = request()->param('expired_start');
        $expired_end = request()->param('expired_end');
        $page_size = request()->param('page_size', $this->crowdfunding_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->crowdfunding_page['JUMP_PAGE']);

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'title'         => $title,
            'status'        => $status,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'expired_start' => $expired_start,
            'expired_end'   => $expired_end,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($title) {
            $conditions['title'] = ['like', '%' . $title . '%'];
        }

        if (is_null($status)) {
            $conditions['status'] = ['in',[0,1,2,3]];
        } else {
            switch ($status) {
                case 0:
                    $conditions['status'] = $status;
                    break;
                case 1:
                    $conditions['status'] = $status;
                    break;
                case 2:
                    $conditions['status'] = $status;
                    break;
                case 3:
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

        if ($expired_start && $expired_end) {
            $conditions['expired_time'] = ['between time', [$expired_start, $expired_end]];
        }

        /* 返回结果 */
        $crowdfunding = $this->crowdfunding_model
            ->where($conditions)
            ->order('id', 'asc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if($crowdfunding) {
            return $this->return_message(Code::SUCCESS, '获取众筹列表成功', $crowdfunding);
        } else {
            return $this->return_message(Code::FAILURE, '获取众筹列表失败');
        }

    }

    /* 众筹添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $title = request()->param('title');
        $current_amount = request()->param('current_amount');
        $target_amount = request()->param('target_amount');
        $status = request()->param('status', 0);
        $expired_time = request()->param('expired_time');

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'title'         => $title,
            'current_amount'=> $current_amount,
            'target_amount' => $target_amount,
            'status'        => $status,
            'expired_time'  => $expired_time
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        /* 返回数据 */
        if (empty($id)) {
            $crowdfunding = $this->crowdfunding_model->save($validate_data);
        } else {
            $crowdfunding = $this->crowdfunding_model->save($validate_data, ['id' => $id]);
        }

        if ($crowdfunding) {
            return $this->return_message(Code::SUCCESS, '数据操作成功');
        } else {
            return $this->return_message(Code::FAILURE, '数据操作失败');
        }

    }

    /* 众筹详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        /* 返回结果 */
        $crowdfunding = $this->crowdfunding_model->where('id', '=', $id)->find();

        if ($crowdfunding) {
            return $this->return_message(Code::SUCCESS, '获取众筹成功', $crowdfunding);
        } else {
            return $this->return_message(Code::FAILURE, '获取众筹失败');
        }

    }

    /* 众筹删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        /* 返回结果 */
        $crowdfunding = $this->crowdfunding_model->where('id', '=', $id)->delete();

        if ($crowdfunding) {
            return $this->return_message(Code::SUCCESS, '删除众筹成功');
        } else {
            return $this->return_message(Code::FAILURE, '删除众筹失败');
        }
    }

    /* 众筹审核 */
    public function auditing() {

        /* 接收参数 */
        $id = request()->param('id');
        $status = request()->param('status');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id,
            'status'    => $status
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('auditing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        $crowdfunding = $this->crowdfunding_model->where('id',$id)->find();

        if (empty($crowdfunding)) {
            return $this->return_message(Code::FAILURE, '不存在众筹');
        } else {

            if ($status == 1) {
                return $this->return_message(Code::FORBIDDEN, '审核状态错误');
            } else {

                $auditing = $this->crowdfunding_model->where('id', '=', $id)->update(['status' => $status]);

                if ($auditing) {
                    if ($status == 2) {
                        return $this->return_message(Code::SUCCESS, '审核成功');
                    }
                    if ($status == 3) {
                        return $this->return_message(Code::FORBIDDEN, '审核失败');
                    }
                } else {
                    return $this->return_message(Code::FAILURE, '已经审核了');
                }
            }
        }

    }

}