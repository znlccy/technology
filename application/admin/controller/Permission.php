<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:52
 * Comment: 权限控制器
 */

namespace app\admin\controller;

use app\admin\model\Permission as PermissionModel;
use app\admin\model\AdminRole as AdminRoleModel;
use app\admin\response\Code;
use app\admin\validate\Permission as PermissionValidate;
use gmars\rbac\Rbac;
use think\Request;
use think\Session;

class Permission extends BasisController {

    /* 声明权限模型 */
    protected $permission_model;

    /* 声明用户角色模型 */
    protected $admin_role_model;

    /* 声明权限验证器 */
    protected $permission_validate;

    /* 声明权限分页器 */
    protected $permission_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->permission_model = new PermissionModel();
        $this->admin_role_model = new AdminRoleModel();
        $this->permission_validate = new PermissionValidate();
        $this->permission_page = config('pagination');
    }

    /* 权限节点 */
    public function node() {
        //获得权限节点
        $admin = Session::get('admin');
        $id = $admin['id'];
        $node = $this->admin_role_model->alias('ar')
            ->where('level', '<>', '3')
            ->where('ar.user_id', '=', $id)
            ->where('status', '=', '1')
            ->join('tb_role_permission rp', 'ar.role_id = rp.role_id')
            ->join('tb_permission tp', 'tp.id = rp.permission_id')
            ->select();

        //生成权限树
        $permission_ids = [];
        $nodes = [];
        foreach ($node as $value) {
            if (!in_array($value['permission_id'], $permission_ids)) {
                $nodes[] = $value;
            }
            $permission_ids[] = $value['permission_id'];
        }

        $tree = $this->build_trees($node, 0);

        //返回数据
        return json([
            'code'      => '200',
            'message'   => '获得权限节点成功',
            'data'      => $tree
        ]);

    }

    /* 权限列表 */
    public function listing() {
        /* 获取客户端提供的数据 */
        $id = request()->param('id');
        $status = request()->param('status');
        $name = request()->param('name');
        $path = request()->param('path');
        $pid = request()->param('pid');
        $description = request()->param('description');
        $sort = request()->param('sort');
        $level = request()->param('level');
        $create_start = request()->param('create_start');
        $create_end = request()->param('create_end');
        $update_start = request()->param('update_start');
        $update_end = request()->param('update_end');
        $page_size = request()->param('page_size', $this->permission_page['PAGE_SIZE']);
        $jump_page = request()->param('jump_page', $this->permission_page['JUMP_PAGE']);

        //验证数据
        $validate_data = [
            'id'             => $id,
            'status'         => $status,
            'name'           => $name,
            'path'           => $path,
            'pid'            => $pid,
            'description'    => $description,
            'sort'           => $sort,
            'level'          => $level,
            'create_start'   => $create_start,
            'create_end'     => $create_end,
            'update_start'   => $update_start,
            'update_end'     => $update_end,
            'page_size'      => $page_size,
            'jump_page'      => $jump_page,
        ];

        //验证结果
        $result   = $this->permission_validate->scene('entry')->check($validate_data);
        if (!$result) {
            return json(['code' => '401', 'message' => $this->role_validate->getError()]);
        }

        //筛选条件
        $conditions = [];

        if ($id) {
            $conditions['id'] = $id;
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
        if ($name) {
            $conditions['name'] = ['like', '%' . $name . '%'];
        }
        if ($name) {
            $conditions['path'] = ['like', '%' . $path . '%'];
        }
        if ($pid) {
            $conditions['pid'] = $pid;
        }
        if ($description) {
            $conditions['description'] = ['like', '%' . $description . '%'];
        }
        if ($sort) {
            $conditions['sort'] = $sort;
        }
        if ($level) {
            $conditions['level'] = $level;
        }
        if ($create_start || $create_end) {
            $conditions['create_time'] = ['between time', [$create_start, $create_end]];
        }
        if ($update_start || $update_end) {
            $conditions['update_time'] = ['between time', [$update_start, $update_end]];
        }

        //返回数据
        $role = $this->permission_model->where($conditions)
            ->order('sort', 'desc')
            ->order('id', 'asc')
            ->paginate($page_size, false, ['page' => $jump_page]);

        if ($role) {
            return $this->return_message(Code::SUCCESS, '获取角色名称成功',$role);
        } else {
            return $this->return_message(Code::FAILURE, '获取角色名称失败');
        }
    }

    /* 权限添加更新 */
    public function save() {

        /* 获取客户端提交的数据 */
        $id = request()->param('id');
        $name = request()->param('name');
        $status = request()->param('status');
        $description = request()->param('description');
        $path = request()->param('path');
        $sort = request()->param('sort');
        $icon = request()->param('icon');
        $level = request()->param('level');
        $pid = request()->param('pid');

        //验证数据
        $validate_data = [
            'id'            => $id,
            'name'          => $name,
            'status'        => $status,
            'description'   => $description,
            'path'          => $path,
            'sort'          => $sort,
            'icon'          => $icon,
            'level'         => $level,
            'pid'           => $pid
        ];

        //验证结果
        $result   = $this->permission_validate->scene('save')->check($validate_data);
        if (!$result) {
            return $this->return_message(Code::INVALID, $this->permission_validate->getError());
        }

        //返回结果
        $rbac = new Rbac();
        if (!empty($id)) {
            $update_data = [
                'id'            => $id,
                'name'          => $name,
                'status'        => $status,
                'description'   => $description,
                'path'          => $path,
                'sort'          => $sort,
                'icon'          => $icon,
                'level'         => $level,
                'pid'           => $pid,
                'update_time'   => date('Y-m-d H:i:s', time())
            ];
            $update_result = $rbac->editPermission($update_data);
            if ($update_result) {
                return $this->return_message(Code::SUCCESS, '更新权限成功');
            }
        } else {
            $data = [
                'name'          => $name,
                'status'        => $status,
                'description'   => $description,
                'path'          => $path,
                'sort'          => $sort,
                'icon'          => $icon,
                'level'         => $level,
                'pid'           => $pid,
                'create_time'   => date('Y-m-d H:i:s', time())
            ];
            $add_result = $rbac->createPermission($data);
            if ($add_result) {
                return $this->return_message(Code::SUCCESS, '添加权限成功');
            }
        }
    }

    /* 权限详情 */
    public function detail() {
        //获取客户端提交的数据
        $id = request()->param('id');

        //验证数据
        $validate_data = [
            'id'            => $id
        ];

        //验证结果
        $result = $this->permission_validate->scene('detail')->check($validate_data);
        if (!$result) {
            return $this->return_message(Code::INVALID, $this->permission_validate->getError());
        }

        //返回数据
        $permission = $this->permission_model->where('id', $id)->find();
        if ($permission) {
            return $this->return_message(Code::SUCCESS, '获取权限详情成功', $permission);
        } else {
            return $this->return_message(Code::FAILURE, '获取权限详情失败');
        }
    }

    /* 删除权限 */
    public function delete() {

        //获取客户端提交过来的数据
        $id = request()->param('id');

        //验证数据
        $validate_data = [
            'id'            => $id
        ];

        //验证结果
        $result = $this->permission_validate->scene('delete')->check($validate_data);
        if (!$result) {
            return $this->return_message(Code::INVALID, $this->permission_validate->getError());
        }

        //返回结果
        if ($id == 1) {
            return $this->return_message(Code::FORBIDDEN, '超级管理员不允许删除');
        } else {
            $delete = $this->permission_model->where('id', $id)->delete();
            if ($delete) {
                return $this->return_message(Code::SUCCESS, '删除权限成功');
            } else {
                return $this->return_message(Code::FAILURE, '删除权限失败');
            }
        }
    }


    /* 实现无限极分类 */
    private function get_tree($arr,$pid,$step){
        global $tree;
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                $flg = str_repeat('└―',$step);
                $val['name'] = $flg.$val['name'];
                $tree[] = $val;
                $this->get_tree($arr , $val['id'] ,$step+1);
            }
        }
        return $tree;
    }

    /* 生成树结构 */
    public function build_trees($data, $pId)
    {
        $tree_nodes = array();
        foreach($data as $k => $v)
        {
            if($v['pid'] == $pId)
            {
                $v['child'] = $this->build_trees($data, $v['id']);
                $tree_nodes[] = $v;
            }
        }
        return $tree_nodes;
    }
}