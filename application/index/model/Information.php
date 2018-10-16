<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 18:18
 * Comment: 信息模型
 */
namespace app\index\model;

use think\Model;

class Information extends Model {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_information';

    /* 设置富文本 */
    public function setRichTextAttr($value)
    {
        return htmlspecialchars($value);
    }

    /* 获取富文本 */
    public function getRichTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /* 关联的表 */
    public function user()
    {
        return $this->hasOne('User', 'id', 'publisher');
    }
}
