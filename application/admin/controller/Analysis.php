<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;
use think\Db;
//网站数据分析
class Analysis extends Base
{
    public function index()
    {

        return $this->fetch();
    }


}