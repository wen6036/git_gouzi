<?php
/**
 * 前台用户类
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\model;

// use traits\model\SoftDelete;

class UserInfo extends Admin
{
    // use SoftDelete;
    // protected $name = 'tz_userinfo';
    protected $table = 'tz_userinfo';
    protected $autoWriteTimestamp = true;


    protected function getCreateTimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'/';
    }

    protected function getLastLoginTimeAttr($value)
    {
        return $value>0?date('Y-m-d H:i:s',$value):'/';
    }


    protected function getRegTimeAttr($value)
    {
        return $value>0?date('Y-m-d H:i:s',$value):'/';
    }
}
