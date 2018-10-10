<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 18:41
 * Comment: 科技产品展示模型
 */

namespace app\admin\model;

class Display extends BasisModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_display';

    /* 设置富文本 */
    public function setRichTextAttr($value) {
        return htmlspecialchars($value);
    }

    /* 获取富文本 */
    public function getRichTextAttr($value) {
        return htmlspecialchars_decode($value);
    }
}