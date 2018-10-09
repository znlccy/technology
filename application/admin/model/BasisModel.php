<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6
 * Time: 10:55
 * Comment: 基础模型
 */

namespace app\admin\model;

use think\Model;

class BasisModel extends Model {

    /* 读存时间 */
    protected $autoWriteTimestamp = 'datetime';
}