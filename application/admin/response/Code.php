<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 14:21
 * Comment: 状态码配置
 */

namespace app\admin\response;

class Code {

    /* 声明成功状态码 */
    const SUCCESS = 200;

    /* 声明失败状态码 */
    const FAILURE = 404;

    /* 声明无效状态码 */
    const INVALID = 401;

    /* 声明过期状态码 */
    const EXPIRED = 402;

    /* 声明禁止状态码 */
    const FORBIDDEN = 403;

    /* 声明认证状态码 */
    const AUTH = 302;
}