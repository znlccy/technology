<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 15:58
 * Comment:  区域模型
 */

namespace app\index\model;

class Region extends BasicModel {

    /* 读存时间 */
    protected $autoWriteTimestamp = false;

    /* 对应的表 */
    protected $table = 'tb_region';
}