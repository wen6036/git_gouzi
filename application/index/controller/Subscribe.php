<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
class Subscribe extends Controller
{
	public function index(){

    	$this->assign('title','订阅管理');
		return $this->fetch();
	}

	// 订阅管理
	public function subscribe(){
		return $this->fetch();
	}

	// 用户订阅得工作室
	public function studio(){
		return $this->fetch();
	}

	// 我得收藏
	public function collect(){
		return $this->fetch();
	}

	// 体验券
	public function coupon(){
		return $this->fetch();
	}
}