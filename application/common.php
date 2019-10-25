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


function sendCurlPost($url, $data = 'input'){
    //初始化，创建一个cURL资源
    $ch = curl_init();
    //设置cURL选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "user-agent:Mozilla/5.0 (Windows NT 5.1; rv:24.0) Gecko/20100101 Firefox/24.0");
    curl_setopt($ch, CURLOPT_HEADER, 0);    //是否返回文件头信息
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //不直接打印输出
    curl_setopt($ch, CURLOPT_POST, 1);  //是否post请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //post传输数据
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded; charset=utf-8", "Content-length: ".strlen($data)));
    //执行cURL会话
    $response = curl_exec($ch);
    if (!curl_errno($ch)){
        $result =  $response;
    }else{
    //    echo 'Curl error: ' . curl_error($ch);
        $result = false;
    }
 
    //关闭cURL释放资源
    curl_close($ch);
 
    return $result;
}


function varify_url($url)
{
$check = @fopen($url,"r");
if($check)
 $status = true;
else
 $status = false;  
 
return $status;
}

function base64url_encode($plainText) {
 
    $base64 = base64_encode($plainText);
    $base64url = strtr($base64, '+/=', '-_,');
    return $base64url;  
}
 
function base64url_decode($plainText) {
 
    $base64url = strtr($plainText, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;  
}