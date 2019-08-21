<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Studio extends Controller
{
	// 工作室管理
	public function index(){
		$this->assign('title','工作室管理');
		return $this->fetch();
	}
	public function studio(){
		
		return $this->fetch();
	}

	// 创建期货的工作室
	public function createstudio(){
        $userinfo = session('userinfo');
        $con['uid'] = $userinfo['id'];

        $info = Db::table('tz_studio')->where($con)->find();
        // dump($info);
        if($info){
        	$this->assign('info',$info);
        }
		return $this->fetch();
	}	

	// 已建期货工作室
	public function hasstudio(){
		$userinfo = session('userinfo');
		$con['uid'] = $userinfo['id'];
		$info = Db::table('tz_studio')->where($con)->find();
		$this->assign('info',$info);
		$this->assign('id',$userinfo['id']);
		return $this->fetch();
	}

	// 工作室订阅管理
	public function studiosub(){
		return $this->fetch();
	}


	public function savestudio(){
        $userinfo = session('userinfo');

        if(!$userinfo) return json(['code'=>0,'msg'=>'请确定用户登录状态']);
		$param = $this->request->param();
		unset($param['agreement']);
		$param['uid'] = $userinfo['id'];
		$param['futures_password'] = md5(md5($param['futures_password']));
		$status = Db::table('tz_studio')->insert($param);
        if($status){
            return json(['code'=>1,'msg'=>'修改成功']);
        }else{
            return json(['code'=>0,'msg'=>'修改失败']);
        }
	}

}