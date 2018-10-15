<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:52
 * Comment: 管理员控制器
 */

namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\admin\validate\Admin as AdminValidate;
use think\Request;

class Admin extends BasisController {

    /* 声明管理员模型 */
    protected $admin_model;

    /* 声明管理员验证器 */
    protected $admin_validate;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->admin_model = new AdminModel();
        $this->admin_validate = new AdminValidate();
    }

    /* 管理员手机登录 */
    public function mobile_login() {

    }

    /* 管理员账号密码登录 */
    public function account_login() {

    }

    /* 管理员列表 */
    public function listing() {

    }

    /* 管理员详情 */
    public function detail() {

    }

    /* 管理员删除 */
    public function delete() {

    }

    /* 分配管理员角色 */
    public function assign_admin_role() {

    }

    /* 管理员信息 */
    public function info() {

    }

    /* 管理员退出 */
    public function logout() {
        
    }

}