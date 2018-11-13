<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 11:32
 * Comment: 用户模型
 */

namespace app\admin\model;

class User extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_user';

    /* 关联的表 */
    public function products() {
        return $this->belongsToMany('Product', 'tb_user_product', 'product_id', 'user_id');
    }

    /* 关联的表 */
    public function information() {
        return $this->belongsToMany('Information', 'tb_user_info','info_id', 'user_id');
    }
}