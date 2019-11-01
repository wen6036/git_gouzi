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

		$menuList = Db::table('tz_menu')->where('status=1')->order('order','asc')->select();
		$this->assign('menuList',$menuList);
    	$companyinfo = Db::table('tz_company')->where('id=1')->find();
    	$this->assign('companyinfo',$companyinfo);
		if($userinfo){
			$this->assign('userinfo',$userinfo);
			$this->assign('status',1);
		}else{
			$this->assign('status',2);
		}
	}


	public function _empty(){
        // $this->redirect(url());//空方法处理
        $this->assign('title','页面错误');
        return $this->fetch('./public/error');
    }

}