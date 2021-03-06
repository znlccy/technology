<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/17
 * Time: 15:13
 * Comment: 众筹模型
 */

namespace app\index\model;

class Crowdfunding extends BasicModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_crowdfunding';

    /* 关联的表 */
    public function Goods() {
        return $this->hasMany('Goods', 'crowd_id', 'id');
    }

    /* 设置富文本 */
    public function setRichTextAttr($value) {
        return htmlspecialchars($value);
    }

    /* 获取富文本 */
    public function getRichTextAttr($value) {
        return htmlspecialchars_decode($value);
    }
}