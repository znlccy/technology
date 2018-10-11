<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:46
 * Comment: 产品模型
 */

namespace app\admin\model;

class Product extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_product';

    /* 关联的表 */
    public function user() {
        return $this->belongsTo('User');
    }
}