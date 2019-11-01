<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Service extends Controller
{
	// public function _initialize(){
  //   	$userinfo = session('userinfo');
		// if($userinfo){
		// 	$this->assign('userinfo',$userinfo);
		// 	$this->assign('status',1);
		// }else{
		// 	$this->assign('status',2);
		// }
	// }

	public function content(){
    	$list = Db::table('tz_banner')->where('type=1')->select();
        $this->assign('list',$list);    

    	$this->assign('title','服务内容');
		return $this->fetch();
	}
	public function research(){
    	$list = Db::table('tz_banner')->where('type=1')->select();
        $this->assign('list',$list);    

    	$this->assign('title','学研中心');
		return $this->fetch();
	}
	public function guide(){
    	$list = Db::table('tz_banner')->where('type=1')->select();
        $this->assign('list',$list);    

    	$this->assign('title','新手指南');
		return $this->fetch();		
	}
}