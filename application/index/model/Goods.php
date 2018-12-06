<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/5
 * Time: 16:37
 * Comment: 众筹商品列表
 */

namespace app\index\model;

class Goods extends BasicModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_goods';

    /* 关联的表 */
    public function Crowdfunding() {
        return $this->belongsTo('Crowdfunding', 'crowd_id', 'id');
    }
}