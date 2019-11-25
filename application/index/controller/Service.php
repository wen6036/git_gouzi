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

        $fuwulist = Db::table('tz_webpage')->where('type=2')->select();

        $this->assign('fuwulist',$fuwulist);  
    	$this->assign('title','服务内容');
		return $this->fetch();
	}
	public function research(){
         $xueyanlist = Db::table('tz_webpage')->where('type=3')->select();
        $this->assign('xueyanlist',$xueyanlist);  
    	$this->assign('title','学研中心');
		return $this->fetch();
	}
	public function guide(){
       $xinshoulist = Db::table('tz_webpage')->where('type=4')->select();
        $this->assign('xinshoulist',$xinshoulist); 

    	$this->assign('title','新手指南');
		return $this->fetch();		
	}

	public function personnel(){   
		 $info = Db::table('tz_webpage')->where("id=34")->find();
		 $this->assign('info',$info);
    	$this->assign('title','人才选拔');
		return $this->fetch();		
	}


}