<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:53
 * Comment: 用户控制器
 */

namespace app\admin\controller;

use app\admin\model\User as UserModel;
use app\admin\response\Code;
use app\admin\validate\User as UserValidate;
use think\Request;

class User extends BasisController {

    /* 声明用户模型 */
    protected $user_model;

    /* 声明用户验证器 */
    protected $user_validate;

    /* 声明用户分页器 */
    protected $user_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->user_model = new UserModel();
        $this->user_validate = new UserValidate();
        $this->user_page = config('pagination');
    }

    /* 用户列表 */
    public function listing() {

        /* 接收参数 */
        $id = request()->param('id');
        $type = intval(request()->param('type/d'));
        $username = request()->param('username');
        $mobile = request()->param('mobile');
        $enterprise = request()->param('enterprise');
        $introduce = request()->param('introduce');
        $industry = request()->param('industry');
        $capital = request()->param('capital');
        $revenue = request()->param('revenue');
        $assets = request()->param('assets');
        $address = request()->param('address');
        $duty = request()->param('duty');
        $department = request()->param('department');
        $phone = request()->param('phone');
        $wechat = request()->param('wechat');
        $email = request()->param('email');
        $link = request()->param('link');
        $location = request()->param('location');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size', $this->user_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->user_page['JUMP_PAGE']);

        /* 验证参数 */
        $validate_data = [
            'id'        => $id,
            'type'      => $type,
            'username'  => $username,
            'mobile'    => $mobile,
            'enterprise'=> $enterprise,
            'introduce' => $introduce,
            'industry'  => $industry,
            'capital'   => $capital,
            'revenue'   => $revenue,
            'assets'    => $assets,
            'address'   => $address,
            'duty'      => $duty,
            'department'=> $department,
            'phone'     => $phone,
            'wechat'    => $wechat,
            'email'     => $email,
            'link'      => $link,
            'location'  => $location,
            'create_start'=> $create_start,
            'create_end'=> $create_end,
            'update_start'=> $update_start,
            'update_end'=> $update_end,
            'page_size' => $page_size,
            'jump_page' => $jump_page,
        ];

        /* 验证结果 */
        $result = $this->user_validate->scene('listing')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->user_validate->getError());
        }

        /* 筛选条件 */
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
        }

        if ($type) {
            $conditions['type'] = $type;
        }

        if ($username) {
            $conditions['username'] = ['like', '%' . $username .'%'];
        }

        if ($mobile) {
            $conditions['mobile'] = ['like', '%' . $mobile . '%'];
        }

        if ($enterprise) {
            $conditions['enterprise'] = ['like', '%' . $enterprise . '%'];
        }

        if ($introduce) {
            $conditions['introduce'] = ['like', '%' . $introduce . '%'];
        }

        if ($industry) {
            $conditions['industry'] = ['like', '%' . $industry . '%'];
        }

        if ($capital) {
            $conditions['capital'] = $capital;
        }

        if ($revenue) {
            $conditions['revenue'] = $revenue;
        }

        if ($assets) {
            $conditions['assets'] = $assets;
        }

        if ($address) {
            $conditions['address'] = ['like', '%' . $address . '%'];
        }

        if ($duty) {
            $conditions['duty'] = ['like', '%' . $duty . '%'];
        }

        if ($department) {
            $conditions['department'] = ['like', '%' . $department . '%'];
        }

        if ($phone) {
            $conditions['phone'] = ['like', '%' . $phone . '%'];
        }
        if ($wechat) {
            $conditions['wechat'] = ['like', '%' . $wechat . '%'];
        }

        if ($email) {
            $conditions['email'] = ['like', '%' . $email . '%'];
        }

        if ($link) {
            $conditions['link'] = ['like', '%' . $link . '%'];
        }

        if ($location) {
            $conditions['location'] = ['like', '%' . $location . '%'];
        }

        if ($create_start && $create_end) {
            $conditions['create_time'] = ['between time',[$create_start, $create_end]];
        }

        if ($update_start && $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        /* 返回结果 */
        $user = $this->user_model
            ->where($conditions)
            ->order('id', 'asc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($user) {
            return $this->return_message(Code::SUCCESS, '获取用户列表成功',$user);
        } else {
            return $this->return_message(Code::FAILURE, '获取用户列表失败');
        }
    }

    /* 用户添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');

        $type = intval(request()->param('type/d'));
        $username = request()->param('username');
        $password = request()->param('password');
        $confirm_password = request()->param('confirm_password');
        $mobile = request()->param('mobile');
        $enterprise = request()->param('enterprise');
        $introduce = request()->param('introduce');
        $industry = request()->param('industry');
        $capital = request()->param('capital');
        $revenue = request()->param('revenue');
        $assets = request()->param('assets');
        $address = request()->param('address');
        $duty = request()->param('duty');
        $department = request()->param('department');
        $phone = request()->param('phone');
        $wechat = request()->param('wechat');
        $email = request()->param('email');
        $link = request()->param('link');
        $location = request()->param('location');
        $textarea = request()->param('textarea');

        /* 验证数据 */
        $validate_data = [
            'id'                => $id,
            'username'          => $username,
            'password'          => md5($password),
            'confirm_password'  => $confirm_password,
            'mobile'            => $mobile,
            'enterprise'        => $enterprise,
            'introduce'         => $introduce,
            'industry'          => $industry,
            'capital'           => $capital,
            'revenue'           => $revenue,
            'assets'            => $assets,
            'address'           => $address,
            'duty'              => $duty,
            'department'        => $department,
            'phone'             => $phone,
            'wechat'            => $wechat,
            'email'             => $email,
            'link'              => $link,
            'location'          => $location,
            'textarea'          => $textarea,
            'type'              => $type
        ];

        switch ($type) {
            case 1:
                $result = $this->user_validate->scene('entrepreneur')->check($validate_data);
                if (true !== $result) {
                    return $this->return_message(Code::INVALID, $this->user_validate->getError());
                }
                unset($validate_data['location']);
                unset($validate_data['textarea']);
                unset($validate_data['confirm_password']);
                if (empty($id)) {
                    $user = $this->user_model->save($validate_data);
                } else {
                    $user = $this->user_model->save($validate_data, ['id' => $id]);
                }
                break;
            case 2:
                $result = $this->user_validate->scene('collaborator')->check($validate_data);
                if (true !== $result) {
                    return $this->return_message(Code::INVALID, $this->user_validate->getError());
                }
                unset($validate_data['introduce']);
                unset($validate_data['revenue']);
                unset($validate_data['confirm_password']);
                if (empty($id)) {
                    $user = $this->user_model->save($validate_data);
                } else {
                    $user = $this->user_model->save($validate_data, ['id' => $id]);
                }
                break;
            default:
                break;
        }

        if ($user) {
            return $this->return_message(Code::SUCCESS, '数据操作成功');
        } else {
            return $this->return_message(Code::FAILURE, '数据操作失败');
        }
    }

    /* 用户详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->user_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->user_validate->getError());
        }

        /* 返回结果 */
        $user = $this->user_model->where('id', '=', $id)->find();

        if ($user) {
            return $this->return_message(Code::SUCCESS, '获取用户详情成功',$user);
        } else {
            return $this->return_message(Code::FAILURE, '获取用户详情失败');
        }
    }

    /* 用户删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->user_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return $this->return_message(Code::INVALID, $this->user_validate->getError());
        }

        /* 返回结果 */
        $user = $this->user_model->where('id', '=', $id)->delete();

        if ($user) {
            return $this->return_message(Code::SUCCESS, '删除数据成功');
        } else {
            return $this->return_message(Code::FAILURE, '删除数据失败');
        }
    }

}