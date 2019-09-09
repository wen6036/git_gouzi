<?php
/**
 * 用户中心
 * @author yupoxiong<i@yufuping.com>
 */
namespace app\index\controller;
use think\Db;
// use think\Controller;
use think\Session;
use think\Request;
class Login extends Controller
{
	public function verify(){
		$param = $this->request->param();
		$data['tel'] = $param['tel'];
		$data['yzm']= rand(1000,9999);
		$data['token'] = md5($data['yzm']);

		$res = Db::table('tz_yzm')->where("tel=".$data['tel'])->find();
		if($res){
			$data['tel'] = $data['tel'];
			$data['create_time'] = time();
			$status = Db::table('tz_yzm')->where("tel=".$data['tel'])->update($data);
		}else{
			$data['tel'] = $data['tel'];
			$data['create_time'] = time();
			$status = Db::table('tz_yzm')->insert($data);
		}
		if($status){
			return json(['code'=>1,'msg'=>$data['token']]);
		}else{
			return json(['code'=>0,'msg'=>'error']);
		}
	}

	public function register(){
		if ($this->request->isPost()) {
			$param = $this->request->param();
			$data['username'] = $param['username'];
			$data['usertel'] = $param['usertel'];
			$data['password'] = md5(md5($param['password']));
			$captcha = $param['yzm'];
			if(!captcha_check($captcha)){
				 return json(['code'=>0,'msg'=>'验证码错误']);
			};

			$username = $param['username'];
			$have_name = Db::table('tz_userinfo')->where("username = '$username'")->find();
			if($have_name){
				return json(['code'=>0,'msg'=>'用户名已存在']);
			}
			$tel = $param['usertel'];
			$have_tel = Db::table('tz_userinfo')->where("usertel = '$tel'")->find();
			if($have_tel){
				return json(['code'=>0,'msg'=>'手机号已经注册过']);
			}
			$con['yzm'] = $param['smyzm'];
			$con['tel'] = $param['usertel'];
			$con['token'] = $param['yzmtoken'];
			$info = Db::table('tz_yzm')->where($con)->find();
			// dump(Db::table('tz_yzm')->getLastSql());
			if($info){
				$create_time = $info['create_time'];
				if(time() - $create_time >300){
					return json(['code'=>0,'msg'=>'验证码超时']);
				}
			}else{
				return json(['code'=>0,'msg'=>'验证码错误']);
			}

			$status = Db::table('tz_userinfo')->insert($data);

			if($status){
				$con1['date'] = date("Y-m-d");
				$registerdata = Db::table('tz_register_data')->where($con1)->find();
				if($registerdata){
					Db::table('tz_register_data')->where($con1)->setInc('num');
				}else{
					Db::table('tz_register_data')->insert($con1);
				}


				return json(['code'=>1,'msg'=>'注册成功']);
			}else{
				return json(['code'=>0,'msg'=>'注册失败']);
			}
		}

		$this->assign('title','注册');
		return $this->fetch();
	}

	//验证是否存在
	public function have_user(){
		$param = $this->request->param();
		$info = $param['info'];
		$status = Db::table('tz_userinfo')->where("username = '$info' OR usertel = '$info'")->find();
		if($status){
			return json(['code'=>0,'msg'=>'error']);
		}else{
			return json(['code'=>1,'msg'=>'ok']);
		}
	}

    public function login(){
    	if ($this->request->isPost()) {
    		$param = $this->request->param();
			$userinfo = $param['userinfo'];
			$captcha = $param['yzm'];
			$password = md5(md5($param['password']));

			if(!captcha_check($captcha)){
				 return json(['code'=>0,'msg'=>'验证码错误']);
			};

			$info = Db::table('tz_userinfo')->where("username = '$userinfo' OR usertel = '$userinfo'")->find();
			// dump($info);
			if(!$info){
				return json(['code'=>0,'msg'=>'用户信息不存在']);
			}

			if($info['status']==0) return json(['code'=>0,'msg'=>'用户被禁用,请联系客服']);
			
			
			if($info['password'] == $password){
				$con1['date'] = date("Y-m-d");
				$logindata = Db::table('tz_login_data')->where($con1)->find();
				if(date( "Y-m-d",$info['last_login_time']) != $con1['date'] ){
					if($logindata){
						Db::table('tz_login_data')->where($con1)->setInc('num');
					}else{
						Db::table('tz_login_data')->insert($con1);
					}
				}
				$con['last_login_time'] = time();
				Db::table('tz_userinfo')->where("username = '$userinfo' OR usertel = '$userinfo'")->update($con);

				session('userinfo',$info);
				return json(['code'=>1,'msg'=>'登入成功']);
			}else{
				return json(['code'=>0,'msg'=>'密码错误']);
			}
    	}
    	$this->assign('title','欢迎登录');
        return $this->fetch();
    }

    public function loginout(){
    	session('userinfo',null);
    	$this->redirect('index/index');
    }

    public function edit_pwd(){

    	if ($this->request->isPost()) {
    		$param = $this->request->param();

    		if($param['newpassword'] != $param['againpassword']){
    			return json(['code'=>0,'msg'=>'两次密码不一致']);
    		}

			$captcha = $param['yzm'];
			$password = md5(md5($param['newpassword']));
			if(!captcha_check($captcha)){
				 return json(['code'=>0,'msg'=>'验证码错误']);
			};



			$con['yzm'] = $param['smyzm'];
			$con['tel'] = $param['usertel'];
			$con['token'] = $param['yzmtoken'];
			$info = Db::table('tz_yzm')->where($con)->find();
			// dump(Db::table('tz_yzm')->getLastSql());
			if($info){
				$create_time = $info['create_time'];
				if(time() - $create_time >300){
					return json(['code'=>0,'msg'=>'短信验证超时']);
				}
			}else{
				return json(['code'=>0,'msg'=>'短信验证错误']);
			}


			$tel = $param['usertel'];
			$data['password'] = md5(md5($param['newpassword']));
			$info = Db::table('tz_userinfo')->where("usertel = '$tel'")->update($data);
			

			if($info){
				return json(['code'=>1,'msg'=>'更改成功']);
			}else{
				return json(['code'=>0,'msg'=>'更改失败']);
			}
    	}



    	$this->assign('title','重置密码');
    	return $this->fetch();
    }
}