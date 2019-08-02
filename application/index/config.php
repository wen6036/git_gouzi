<?php
/**
 * 后台配置文件
 * @author yupoxiong<i@yufuping.com>
 */

return [
    // 模板参数替换
    'view_replace_str' => [
        '__STATIC__'  => '/static',
        '__CSS__'     => '/static/index/css',
        '__JS__'      => '/static/index/js',
        '__IMAGES__'  => '/static/index/images'
    ],

    //分页配置
    'paginate'                   => [
        'type'      => '\tools\Bearpage',
        'var_page'  => 'page',
        'list_rows' => 10,
    ],

    //后台生成二维码设置
    'qrcode_path'                => ROOT_PATH . 'public' . DS . 'uploads' . DS . 'admin' . DS . 'qrcode' . DS,

];
