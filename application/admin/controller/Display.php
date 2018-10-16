<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:41
 * Comment: 科技产品展示控制器
 */

namespace app\admin\controller;

use app\admin\model\Display as DisplayModel;
use app\admin\response\Code;
use app\admin\validate\Display as DisplayValidate;
use think\Request;

class Display extends BasisController {

    /* 声明产品展示模型 */
    protected $display_model;

    /* 声明产品展示验证器 */
    protected $display_validate;

    /* 声明产品展示分页器 */
    protected $display_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null){
        parent::__construct($request);
        $this->display_model = new DisplayModel();
        $this->display_validate = new DisplayValidate();
        $this->display_page = config('pagination');
    }

    /* 科技产品列表 */
    public function listing() {

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $recommend = request()->param('recommend');
        $status = request()->param('status');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size', $this->display_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->display_page['JUMP_PAGE']);

        /* 验证数据 */
        $validate_data = [
            'id'            => $id,
            'name'          => $name,
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
        $result = $this->display_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->display_validate->getError());
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($name) {
            $conditions['name'] = ['like', '%' . $name . '%'];
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
        $display = $this->display_model
            ->where($conditions)
            ->order('id', 'desc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($display) {
            return $this->return_message(Code::SUCCESS, '获取科技产品列表成功', $display);
        } else {
            return $this->return_message(Code::FAILURE, '获取科技产品列表失败');
        }

    }

    /* 科技产品添加更新 */
    public function save(){

        /* 接收参数 */
        $id = request()->param('id');
        $name = request()->param('name');
        $picture = request()->file('picture');
        $recommend = request()->param('recommend',1);
        $status = request()->param('status',1);
        $rich_text = request()->param('rich_text');

        /* 移动图片 */
        if ($picture) {
            $info = $picture->move(ROOT_PATH . 'public' . DS . 'images' );
            if ($info) {
                $sub_path = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
            }
        }

        /* 验证数据 */
        $validate_data = [
            'id'        => $id,
            'name'      => $name,
            'picture'   => $picture,
            'recommend' => $recommend,
            'status'    => $status,
            'rich_text' => $rich_text
        ];

        /* 验证结果 */
        $result = $this->display_validate->scene('save')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->display_validate->getError());
        }

        /* 返回结果 */
        if (empty($id)) {
            $display = $this->display_model->save($validate_data);
        } else {
            $display = $this->display_model->save($validate_data, ['id' => $id]);
        }

        if ($display) {
            return $this->return_message(Code::SUCCESS, '数据操作成功');
        } else {
            return $this->return_message(Code::FAILURE, '数据操作失败');
        }
    }

    /* 科技产品详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->display_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->display_validate->getError());
        }

        /* 返回结果 */
        $display = $this->display_model->where('id', '=', $id)->find();

        if ($display) {
            return $this->return_message(Code::SUCCESS, '获取科技产品成功', $display);
        } else {
            return $this->return_message(Code::FAILURE, '获取科技产品失败');
        }
    }

    /* 科技产品删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->display_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->display_validate->getError());
        }

        /* 返回数据 */
        $display = $this->display_model->where('id', '=', $id)->delete();

        if ($display) {
            return $this->return_message(Code::SUCCESS, '删除科技产品成功');
        } else {
            return $this->return_message(Code::FAILURE, '删除科技产品失败');
        }
    }

}