<?php
/**
 * 前台用户类
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\model;

// use traits\model\SoftDelete;

class Offices extends Admin
{
    // use SoftDelete;
    // protected $name = 'tz_userinfo';
    protected $table = 'tz_studio';
    protected $autoWriteTimestamp = true;


    protected function getCreateTimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'/';
    }



    // public function showinfo(){
       
    // }

    // protected function getRegTimeAttr($value)
    // {
    //     return $value>0?date('Y-m-d H:i:s',$value):'/';
    // }
}
