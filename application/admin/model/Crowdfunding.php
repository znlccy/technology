<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:39
 * Comment: 众筹模型
 */

namespace app\admin\model;

class Crowdfunding extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_crowdfunding';

    /* 设置富文本 */
    public function setRichTextAttr($value) {
        return htmlspecialchars($value);
    }

    /* 获取富文本 */
    public function getRichTextAttr($value) {
        return htmlspecialchars_decode($value);
    }
}
