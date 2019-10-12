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
		$userinfo = session('userinfo');
		if(!$userinfo){
			$this->redirect('index/login/login');
		}
        $con['id'] = $userinfo['id'];
        $info = Db::table('tz_userinfo')->where($con)->find();
		
		$this->assign('headimg',$info['headimg']);
		$this->assign('nickname',$info['nickname']);

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
		$this->assign('info',$info);
		$this->assign('id',$info['id']);
		return $this->fetch();
	}

	// 工作室订阅管理
	public function studiosub(){
		return $this->fetch();
	}

	public function ajax_agreement(){
		$info = Db::table('tz_agreement')->field('content')->where("id=2")->find();
		return json($info);
	}


	// 创建工作室
	public function savestudio(){
        $userinfo = session('userinfo');
        if(!$userinfo) return json(['code'=>0,'msg'=>'请确定用户登录状态']);
		$param = $this->request->param();
		// dump($param );exit;
		unset($param['agreement']);
		$uid = $userinfo['id'];
		$param['uid'] = $userinfo['id'];
		$pwd = $param['futures_password'];
		$BrokerId = $param['BrokerId'];


		// 期货账户 交易密码 期货账户注册
		// $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
		$url = 'http://49.235.36.29/WebFunctions.asmx/cmd';
		// $data = 'input=qryPerformance 6050 81331531 20190101';
		$data = "app tradeAccount {'isOp':true,'brokerID':'$BrokerId','userID':$uid,'password':'$pwd'}";
		$a = sendCurlPost($url,$data); 
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
		$brokerID = $res['BrokerID'];
		$url = 'http://49.235.36.29/WebFunctions.asmx/cmd';
		$input = "input=app tradeAccount {'isOp':true,'brokerID':'$brokerID','userID':'$uid','password':'$pwd'}";
		$info = sendCurlPost($url,$input); 
		$a = explode('#', $info);
		$b = str_replace('</string>','', $a[2]);

		$arr = json_decode($b,true);
		foreach ($arr as $key => $value) {
			if($value['userID']==$uid && $value['brokerID']== $brokerID){
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



	public function get_studio(){
        $userinfo = session('userinfo');
        $con['uid'] = $userinfo['id'];
        $info = Db::table('tz_studio')->where($con)->find();
        $time = time()-3600*24;
        // 现在时间大于结束时间 可提现
		$list = Db::table('tz_suborder')->where("studio_id=".$info['id'])->where("$time > end_time")->select();
		$money = 0;
		foreach ($list as $key => $value) {
			$idarr[] = $value['id']; 
			$money +=  $value['pay_money'];
		}
		$idarr = isset($idarr)?$idarr:'';
		$con1['studio_id'] = $info['id'];
		$con1['status'] = 1;
		$c = Db::table('tz_subtotle_status')->where($con1)->find();

		if($idarr){	
			$data['studio_id'] = $info['id'];
			$data['last_time'] = time();
			$data['order_num'] = count($list);
			$data['order_totle'] = $money;
			$data['idlist'] = json_encode($idarr);
			$data['status'] = 1;
			if($c){
				Db::table('tz_subtotle_status')->where($con1)->update($data);
			}else{
				Db::table('tz_subtotle_status')->where($con1)->insert($data);
			}
		}

		// 现在时间小于结束时间 buke提现
		$list2 = Db::table('tz_suborder')->where("studio_id=".$info['id'])->where("$time < end_time")->select();
		$money2 = 0;
		foreach ($list2 as $key => $value) {
			$idarr2[] = $value['id']; 
			$money2 +=  $value['pay_money'];
		}
		$idarr2 = isset($idarr2)?$idarr2:'';
		$con2['studio_id'] = $info['id'];
		$con2['status'] = 0;
		$c2 = Db::table('tz_subtotle_status')->where($con2)->find();
		if($idarr2){
			$data2['studio_id'] = $info['id'];
			$data2['last_time'] = time();
			$data2['order_num'] = count($list2);
			$data2['order_totle'] = $money2;
			$data2['idlist'] = json_encode($idarr2); 
			$data2['status'] = 0;
			if($c2){
				Db::table('tz_subtotle_status')->where($con2)->update($data2);
			}else{
				Db::table('tz_subtotle_status')->where($con2)->insert($data2);
			}
		}


        $cur = input('get.cur');
        $cur = !empty($cur) ? $cur : 1;
        $size = input("get.size");
        $size = !empty($size) ? $size : 10;
        $key = input("get.key");
        if($key){
            $where = "title LIKE '%$key%'" ;
        }else{
            $where="";
        }

		$con3['studio_id'] = $info['id'];
		$subinfo = Db::table('tz_subtotle_status')->field("*,FROM_UNIXTIME(last_time,'%Y-%m-%d %H:%i:%s') last_time")->where($con3)->where($where)->page($cur, $size)->select();

		foreach ($subinfo as $key => $value) {
			if($value['status']==2){
				$subinfo[$key]['status_name'] = '已提现';
				$subinfo[$key]['status_name1'] = '';
			}else if($value['status']==1){
				$subinfo[$key]['status_name'] = '可提现';
				$subinfo[$key]['status_name1'] = '申请提现';
			}else{
				$subinfo[$key]['status_name'] = '不可提现';
				$subinfo[$key]['status_name1'] = '';
			}	
		}

		$ppp['data'] = $subinfo;
    	$ppp['count'] = Db::table('tz_subtotle_status')->where($con3)->where($where)->count();
        return json($ppp);
	}

}