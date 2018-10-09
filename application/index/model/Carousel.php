<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:36
 * Comment: 轮播模型
 */

namespace app\index\model;

class Carousel extends BasicModel {

    /* 读写时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_carousel';

}
