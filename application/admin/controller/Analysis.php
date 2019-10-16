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
		$list = Db::table('tz_login_data')->select();
		foreach ($list as $key => $value) {
			$arr[] = [$value['date'],$value['num']];
		}
		$arr = isset($arr)?$arr:[];
		$this->assign('arr',json_encode($arr));
        return $this->fetch();
    }


    public function dayregnumber(){
		$list = Db::table('tz_register_data')->select();
		foreach ($list as $key => $value) {
			$arr[] = [$value['date'],$value['num']];
		}
		$arr = isset($arr)?$arr:[];
		$this->assign('arr',json_encode($arr));
        return $this->fetch();
    	return $this->fetch();
    }

    public function subfree(){
    	// 每日订阅费用
    	return $this->fetch();
    }

}