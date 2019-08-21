<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Studioinfo extends Controller
{
	// 工作室管理
	public function index(){
		$param = $this->request->param();
		if(!isset($param['uid'])){
			$this->assign('title','页面不存在');
			return $this->fetch('public/error');
		}
		$uid = $param['uid'];
		$info = Db::table('tz_userinfo')->field('a.id,a.headimg,b.studioname,b.shipan,b.celue,b.fangshi,b.zhouqi,b.price,b.futures_account,b.futures_password')->alias('a')->Join(['tz_studio'=>'b'],'a.id=b.id','RIGHT')->where("a.id = $uid")->find();

		if(!$info){
			$this->assign('title','页面不存在');
			return $this->fetch('public/error');
		}
		dump($info);
		$this->assign('info',$info);
		$this->assign('uid',$uid);
		$this->assign('user',session('userinfo'));
		$this->assign('title','工作室');
		return $this->fetch();
	}


}