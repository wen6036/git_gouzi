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
        if($info){
        	$this->assign('info',$info);
        }

        $futures_company = Db::table('tz_futures_company')->select();

    	$this->assign('futures_company',$futures_company);
		return $this->fetch();
	}	

	// 已建期货工作室
	public function hasstudio(){
		$userinfo = session('userinfo');
		$con['uid'] = $userinfo['id'];
		$info = Db::table('tz_studio')->field("*,LPAD(id,6,'0') as id")->where($con)->find();
		// dump($info);
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
		// dump($param );exit;
		unset($param['agreement']);
		$uid = $userinfo['id'];
		$param['uid'] = $userinfo['id'];
		$pwd = $param['futures_password'];

		// // $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
		// $url = 'http://49.235.36.29/WebFunctions.asmx/cmd';
		// // $data = 'input=qryPerformance 6050 81331531 20190101';
		// $data = "app tradeAccount {'isOp':true,'brokerID':'6050','userID':$uid,'password':'$pwd'}";
		// $a = sendCurlPost($url,$data); 
		$res = Db::table('tz_studio')->where("uid=".$param['uid'])->find();
		if($res)  return json(['code'=>0,'msg'=>'已创建了']);
		
		$param['create_time'] = time();
		$param['futures_password'] = $pwd;
		$status = Db::table('tz_studio')->insert($param);
        if($status){
            return json(['code'=>1,'msg'=>'创建成功']);
        }else{
            return json(['code'=>0,'msg'=>'创建失败']);
        }
	}

	// 更改价格
	public function edit_price(){
		$param = $this->request->param();
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];
		$data['price'] = $param['price'];
		$status = Db::table('tz_studio')->where("uid=".$uid)->update($data);

        if($status){
            return json(['code'=>1,'msg'=>'编辑成功']);
        }else{
            return json(['code'=>0,'msg'=>'编辑失败']);
        }

	}
	public function edit_pwd(){
		$param = $this->request->param();
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];
		$con['futures_password'] = $param['old_pwd'];
		$con['uid'] = $uid;
		$res = Db::table('tz_studio')->where($con)->find();
		if(!$res) return json(['code'=>0,'msg'=>'原密码不正确','id'=>$uid]);

		$data['futures_password'] = $param['new_pwd'];
		$status = Db::table('tz_studio')->where("uid=".$uid)->update($data);
		$pwd = $param['new_pwd'];
		$url = 'http://49.235.36.29/WebFunctions.asmx/cmd';
		$input = "input=app tradeAccount {'isOp':true,'brokerID':'9999','userID':'$uid','password':'$pwd'}";
		$info = sendCurlPost($url,$input); 
		$a = explode('#', $info);
		$b = str_replace('</string>','', $a[2]);

		$arr = json_decode($b,true);
		foreach ($arr as $key => $value) {
			if($value['userID']==$uid && $value['brokerID']=='9999'){
				$o = $value['password'];
			} 
		}
		if($o==$pwd){
			$true = true;
		}
		// dump($o);
        if($status && $true){
            return json(['code'=>1,'msg'=>'编辑成功']);
        }else{
            return json(['code'=>0,'msg'=>'编辑失败']);
        }

	} 

}