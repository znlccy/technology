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

    /* 设置富文本 */
    public function setRichText($value) {
        return htmlspecialchars($value);
    }

    /* 获取富文本 */
    public function getRichText($value) {
        return htmlspecialchars_decode($value);
    }
}