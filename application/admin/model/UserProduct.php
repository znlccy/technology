<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/13
 * Time: 15:47
 * Comment: 用户产品模型
 */

namespace app\admin\model;

class UserProduct extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = false;

    /* 对应的表 */
    protected $table = 'tb_user_product';
}