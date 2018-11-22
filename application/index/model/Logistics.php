<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 15:59
 * Comment: 物流模型
 */

namespace app\index\model;

class Logistics extends BasicModel {

    /* 对应的表 */
    protected $table = 'tb_logistics';

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';
}