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
				 return json(['code'=>0,'msg'=>'验证码错误1']);
			};

			$con['yzm'] = $param['smyzm'];
			$con['tel'] = $param['usertel'];
			$con['token'] = $param['yzmtoken'];
			$info = Db::table('tz_yzm')->where($con)->find();
		dump(Db::table('tz_yzm')->getLastSql());
			if($info){
				$create_time = $info['create_time'];
				if(time() - $create_time >300){
					return json(['code'=>0,'msg'=>'验证码超时']);
				}
			}else{
				return json(['code'=>0,'msg'=>'验证码错误2']);
			}

			$status = Db::table('tz_userinfo')->insert($data);

			if($status){
				return json(['code'=>1,'msg'=>'注册成功']);
			}else{
				return json(['code'=>0,'msg'=>'注册失败']);
			}
		}

		return $this->fetch();
	}
    public function login(){
        return $this->fetch();
    }
}