<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:46
 * Comment: 产品控制器
 */

namespace app\admin\controller;

use app\admin\model\Product as ProductModel;
use app\admin\status\Status;
use app\admin\model\User as UserModel;
use app\admin\validate\Product as ProductValidate;
use think\Request;

class Product extends BasisController {

    /* 声明产品模型 */
    protected $product_model;

    /* 声明合作者模型 */
    protected $user_model;

    /* 声明产品验证器 */
    protected $product_validate;

    /* 声明产品分页器 */
    protected $product_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->product_model = new ProductModel();
        $this->user_model = new UserModel();
        $this->product_validate = new ProductValidate();
        $this->product_page = config('pagination');
    }

    /* 产品列表 */
    public function listing() {

        /* 接收参数 */
        $id = request()->param('id');
        $title = request()->param('title');
        $region = request()->param('region');
        $industry = request()->param('industry');
        $turnover_start = request()->param('turnover_start');
        $turnover_end = request()->param('turnover_end');
        $assets_start = request()->param('assets_start');
        $assets_end = request()->param('assets_end');
        $purpose = request()->param('purpose');
        $amount_start = request()->param('amount_start');
        $amount_end = request()->param('amount_end');
        $recommend = request()->param('recommend');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size', $this->product_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->product_page['JUMP_PAGE']);

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'title'         => $title,
            'region'        => $region,
            'industry'      => $industry,
            'turnover_start'=> $turnover_start,
            'turnover_end'  => $turnover_end,
            'assets_start'  => $assets_start,
            'assets_end'    => $assets_end,
            'purpose'       => $purpose,
            'amount_start'  => $amount_start,
            'amount_end'    => $amount_end,
            'recommend'     => $recommend,
            'status'        => $status,
            'create_start'  => $create_start,
            'create_end'    => $create_end,
            'update_start'  => $update_start,
            'update_end'    => $update_end,
            'page_size'     => $page_size,
            'jump_page'     => $jump_page
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Status::INVALID, $this->product_validate->getError());
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($title) {
            $conditions['title'] = ['like', '%' . $title . '%'];
        }

        if ($region) {
            $conditions['region'] = ['like', '%' . $region . '%'];
        }

        if ($industry) {
            $conditions['industry'] = ['like', '%' . $industry . '%'];
        }

        if ($turnover_start && $turnover_end) {
            $conditions['turnover'] = ['between', [$turnover_start, $turnover_end]];
        }

        if ($assets_start && $assets_end) {
            $conditions['assets'] = ['between', [$assets_start, $assets_end]];
        }

        if ($purpose) {
            $conditions['purpose'] = ['like', '%' . $purpose . '%'];
        }

        if ($amount_start && $amount_end) {
            $conditions['amount'] = ['between', [$amount_start, $amount_end]];
        }

        if (is_null($recommend)) {
            $conditions['recommend'] = ['in',[0,1]];
        } else {
            switch ($recommend) {
                case 0:
                    $conditions['recommend'] = $recommend;
                    break;
                case 1:
                    $conditions['recommend'] = $recommend;
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
        $product = $this->product_model
            ->where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['jump' => $jump_page]);

        if ($product) {
            return $this->return_message(Status::SUCCESS, '获取产品列表成功', $product);
        } else {
            return $this->return_message(Status::FAILURE, '获取产品列表失败');
        }
    }

    /* 产品添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');
        $title = request()->param('title');
        $region = request()->param('region');
        $industry = request()->param('industry');
        $turnover = request()->param('turnover');
        $assets = request()->param('assets');
        $purpose = request()->param('purpose');
        $amount = request()->param('amount');
        $proposal = request()->file('proposal');
        $picture = request()->file('picture');
        $recommend = request()->param('recommend');
        $status = request()->param('status');

        /* 移动文件 */
        if ($proposal) {
            $info = $proposal->move(ROOT_PATH . 'public' . DS . 'files');
            if ($info) {
                 $sub_path = str_replace('\\', '/', $info->getSaveName());
                 $proposal = '/files/' . $sub_path;
            }
        }

        /* 移动图片 */
        if ($picture) {
            $info = $picture->move(ROOT_PATH . 'public' . DS . 'images');
            if ($info) {
                $sub_path = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
            }
        }

        /* 验证数据 */
        $validate_data = [
            'id'        => $id,
            'title'     => $title,
            'region'    => $region,
            'industry'  => $industry,
            'turnover'  => $turnover,
            'assets'    => $assets,
            'purpose'   => $purpose,
            'amount'    => $amount,
            'proposal'  => $proposal,
            'picture'   => $picture,
            'recommend' => $recommend,
            'status'    => $status
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Status::INVALID, $this->product_validate->getError());
        }

        /* 返回结果 */
        if (empty($id)) {
            $product = $this->product_model->save($validate_data);
        } else {
            if (empty($proposal)) {
                unset($validate_data['proposal']);
            }
            if (empty($picture)) {
                unset($validate_data['picture']);
            }
            $product = $this->product_model->save($validate_data, ['id' => $id]);
        }

        if ($product) {
            return $this->return_message(Status::SUCCESS, '操作数据成功');
        } else {
            return $this->return_message(Status::FAILURE, '操作数据失败');
        }
    }

    /* 产品详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Status::INVALID, $this->product_validate->getError());
        }

        /* 返回结果 */
        $product = $this->product_model->where('id', $id)->find();

        if ($product) {
            return $this->return_message(Status::SUCCESS, '获取产品详情成功', $product);
        } else {
            return $this->return_message(Status::FAILURE, '获取产品详情失败');
        }
    }

    /* 产品删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Status::INVALID, $this->product_validate->getError());
        }

        /* 返回结果 */
        $product = $this->product_model->where('id', $id)->delete();

        if ($product) {
            return $this->return_message(Status::SUCCESS, '删除产品成功');
        } else {
            return $this->return_message(Status::FAILURE, '删除产品失败');
        }
    }

    /* 产品分配 */
    public function allocation() {

        /* 接收参数 */
        $pid = request()->param('pid');
        $uid = request()->param('uid');

        /* 验证数据 */
        $validate_data = [
            'pid'       => $pid,
            'uid'       => $uid
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('allocation')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Status::INVALID, $this->product_validate->getError());
        }

        /* 返回数据 */
        $user = $this->user_model->where(['type' => '2', 'id' => $uid])->find();
        $allocation = $user->product()->save();

        if ($allocation) {
            return $this->return_message(Status::SUCCESS, '产品分配成功');
        } else {
            return $this->return_message(Status::FAILURE, '产品分配失败');
        }
    }

    /* 产品审核 */
    public function auditing() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->product_validate->scene('auditing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Status::INVALID, $this->product_validate->getError());
        }

        /* 返回结果 */
        $auditing = $this->product_model->where('id', $id)->update(['status' => 1]);

        if ($auditing) {
            return $this->return_message(Status::SUCCESS, '审核通过');
        } else {
            return $this->return_message(Status::FAILURE, '审核失败');
        }
    }
}
