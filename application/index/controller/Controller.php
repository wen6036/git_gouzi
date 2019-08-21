<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;

use think\Controller as Tk;
use think\Session;
use think\Request;
use think\Db;
class Controller extends Tk
{
	public function _initialize(){
    	$userinfo = session('userinfo');

    	$companyinfo = Db::table('tz_company')->where('id=1')->find();
    	$this->assign('companyinfo',$companyinfo);
		if($userinfo){
			$this->assign('userinfo',$userinfo);
			$this->assign('status',1);
		}else{
			$this->assign('status',2);
		}
	}
}