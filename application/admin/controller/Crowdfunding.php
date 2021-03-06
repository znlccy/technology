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
use app\admin\model\Goods as GoodsModel;
use app\admin\response\Code;
use app\admin\validate\Crowdfunding as CrowdfundingValidate;
use think\Request;

class Crowdfunding extends BasisController {

    /* 声明众筹模型 */
    protected $crowdfunding_model;

    /* 声明产品模型 */
    protected $goods_model;

    /* 声明众筹验证器 */
    protected $crowdfunding_validate;

    /* 声明众筹分页器 */
    protected $crowdfunding_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->crowdfunding_model = new CrowdfundingModel();
        $this->goods_model = new GoodsModel();
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
        $expired_time = request()->param('expired_time');
        $rich_text = request()->param('rich_text');
        $goods = request()->param('goods/a');
        $pictures = request()->file('picture');

        $picture_path = [];
        foreach($pictures as $key => $picture){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $config = [
                'ext'       => 'jpg,jpeg,png,bmp'
            ];
            $info = $picture->validate($config)->move(ROOT_PATH . 'public' . DS . 'images');
            if($info){
                // 成功上传后 获取上传信息
                $sub_path = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
                $picture_path[$key] = $picture;
            }else{
                // 上传失败获取错误信息
                return json([
                    'code'      => '200',
                    'message'   => $this->crowdfunding_validate->getError()
                ]);
            }
        }

        /* 验证数据 */
        $validate_data = [
            'title'         => $title,
            'current_amount'=> $current_amount,
            'target_amount' => $target_amount,
            'expired_time'  => $expired_time,
            'rich_text'     => $rich_text,
            'create_time'   => date('Y-m-d H:i:s', time())
        ];

        /* 验证结果 */
        $result = $this->crowdfunding_validate->scene('crowd_funding')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->crowdfunding_validate->getError());
        }

        if (empty($id)) {
            $crowd_id = $this->crowdfunding_model->insertGetId($validate_data);

            $crowdfund_instance = $this->crowdfunding_model->where('id',$crowd_id)->find();

            foreach ($goods as $key => $good) {
                $product_result = $crowdfund_instance->Goods()->save(['price' => $good['price'], 'introduce' => $good['introduce'], 'picture' => $picture_path[$key], 'title' => $good['title']]);
            }
        } else {
            if (!empty($validate_data['create_time'])) {
                unset($validate_data['create_time']);
            }
            $validate_data['update_time'] = date('Y-m-d H:i:s', time());
            $crowd_id = $this->crowdfunding_model->save($validate_data, ['id' => $id]);
            $crowdfund_instance = $this->crowdfunding_model->where('id',$id)->find();

            if (empty($pictures)) {
                foreach ($goods as $key => $good) {
                    $product_result = $crowdfund_instance->Goods()->save(['price' => $good['price'], 'introduce' => $good['introduce'],  'title' => $good['title']]);
                }
            } else {
                foreach ($goods as $key => $good) {
                    $product_result = $crowdfund_instance->Goods()->save(['price' => $good['price'], 'introduce' => $good['introduce'], 'picture' => $picture_path[$key], 'title' => $good['title']]);
                }
            }
        }

        if ($product_result) {
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
        $crowdfunding = $this->crowdfunding_model
            ->where('id', $id)
            ->with('Goods', function ($query) use ($id) {
                $query->where('crowd_id', '=', $id);
            })
            ->find();

        if ($crowdfunding) {
            return $this->return_message(Code::SUCCESS,  '获取众筹成功', $crowdfunding);
        } else {
            return $this->return_message(Code::FAILURE,  '获取众筹失败');
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

        /* 级联删除 */
        $product = $this->goods_model->where('crowd_id', $id)->delete();

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

            if ($status == 0) {
                return $this->return_message(Code::FORBIDDEN, '审核状态错误');
            } else {

                $auditing = $this->crowdfunding_model->where('id', '=', $id)->update(['status' => $status]);

                if ($auditing) {
                    if ($status == 1) {
                        return $this->return_message(Code::SUCCESS, '审核通过成功');
                    }
                    if ($status == 2) {
                        return $this->return_message(Code::FORBIDDEN, '审核拒绝成功');
                    }
                } else {
                    return $this->return_message(Code::FAILURE, '已经审核了');
                }
            }
        }

    }

}