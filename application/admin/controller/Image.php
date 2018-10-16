<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 11:20
 * Comment: 富文本图片上传控制器
 */

namespace app\admin\controller;

use app\admin\response\Code;

class Image extends BasisController {

    /* 多文本图片上传 */
    public function upload() {

        $picture = request()->file('picture');

        if ($picture) {
            $info = $picture->move(ROOT_PATH . 'public' . DS . 'images');
            if ($info) {
                $sub_path     = str_replace('\\', '/', $info->getSaveName());
                $picture = '/images/' . $sub_path;
            }else{
                return $this->return_message(Code::FAILURE, '图片上传错误');
            }
        }else{
            return $this->return_message(Code::FAILURE, '图片上传错误');
        }

        return $this->return_message(Code::SUCCESS, '图片上传成功');
    }
}