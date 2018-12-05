<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/5
 * Time: 14:09
 * Comment: 众筹产品模型
 */

namespace app\admin\model;

class Goods extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_goods';

    /* 关联的表 */
    public function Crowdfunding() {
        return $this->belongsTo('Crowdfunding', 'crowd_id', 'id');
    }
}