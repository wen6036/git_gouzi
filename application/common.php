<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// base64保存到本地图片
function basepic($img, $fileurl = '') //保存base图片
{
    if ($fileurl) {
        $fileurl = 'uploads/'.$fileurl.'/';
    } else {
        $fileurl = 'uploads/Web/';
    }
    if (!file_exists($fileurl)) {
        mkdir("$fileurl", 0777, true);
    }
    // $img = isset($img ? $img : '';
    list($type, $data) = explode(',', $img);
    if (strstr($type, 'image/jpeg') !== '') {
        $ext = '.jpg';
    } elseif (strstr($type, 'image/gif') !== '') {
        $ext = '.gif';
    } elseif (strstr($type, 'image/png') !== '') {
        $ext = '.png';
    }
    $photo = time() . implode(array_rand(range(1, 30), 5)) . $ext;
    $re = file_put_contents($fileurl . $photo, base64_decode($data), true);
//    var_dump($re);
    if ($re) {
        return '/'.$fileurl . $photo;
        // echo $photo;
    } else {
        // echo "请重试";
        return false;
    }
}
