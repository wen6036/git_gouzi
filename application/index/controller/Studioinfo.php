<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Studioinfo extends Controller
{

	public function  init($title){
		$param = $this->request->param();
		if(!isset($param['id'])){
			$this->assign('title','页面不存在');
			return $this->fetch('public/error');
		}
		$userinfo = session('userinfo');
		$con['studio_id'] = $param['id'];
		$con['uid'] = $userinfo['id'];
		$collect = Db::table('tz_user_collect')->where($con)->find();
		if($collect)  $this->assign('collect',$collect);


		$id = $param['id'];
		$info = Db::table('tz_studio')->alias('a')->field("c.*,LPAD(a.id,6,'0') as id,b.id as uid,b.headimg,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') as create_time,a.studioname,a.shipan,a.celue,a.fangshi,a.zhouqi,a.price,a.futures_account,a.futures_password,a.description")->Join(['tz_userinfo'=>'b'],'b.id=a.uid','left')->Join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where("a.id = $id")->find();
		if(!$info){
			$this->assign('title','页面不存在');
			return $this->fetch('public/error');
		}
		$userinfo = session('userinfo');
		$this->assign('info',$info);
		$this->assign('user',$userinfo);
		$this->assign('uid',$info['uid']);
		$this->assign('title',$title);

	}
	// 工作室管理
	public function index(){

		$this->init('工作室管理');
		return $this->fetch();
	}

	public function get_info(){
		$param = $this->request->param();
		$id = $param['id'];
		$type = $param['type'];

		$info = Db::table('tz_futures_info')->field("$type")->where('studio_id='.$id)->find();
		$data['x'] = array_keys(json_decode($info[$type],true));
		$v = array_values(json_decode($info[$type],true));
		if($type=='prdID_trdRatio'){
			foreach ($v as $key => $value) {
				$v[$key] =(int)$value;
			}
			$data['y'] = $v;
		}else{
			$data['y'] = $v;
		}
		// dump($data);
		return json($data);

	}

	// 收藏
	public function collect(){
		$userinfo = session('userinfo');
		$con['uid'] = $userinfo['id'];
		
		$param = $this->request->param();
		$con['studio_id'] = $param['id'];
		$status = Db::table('tz_user_collect')->where($con)->find();
		if($status) return json(['code'=>1,'msg'=>'已收藏']);
		$con['create_time'] = time();

		$status1 = Db::table('tz_user_collect')->insert($con);
		if($status1){
			return json(['code'=>1,'msg'=>'收藏成功']);
		}else{
			return json(['code'=>0,'msg'=>'收藏失败']);
		}
	}

	// 取消收藏
	public function cancle_collect(){
		$userinfo = session('userinfo');
		$con['uid'] = $userinfo['id'];
		
		$param = $this->request->param();
		$con['studio_id'] = $param['id'];
		$status = Db::table('tz_user_collect')->where($con)->find();
		if(!$status) return json(['code'=>1,'msg'=>'已取消']);

		$status1 = Db::table('tz_user_collect')->where($con)->delete();
		if($status1){
			return json(['code'=>1,'msg'=>'取消成功']);
		}else{
			return json(['code'=>0,'msg'=>'取消失败']);
		}
	}

	// 实时记录
	public function timelog(){
		$this->init('实时记录');
		return $this->fetch();
	}

	public function ajax_log(){
        $cur = input('get.cur');
        $cur = !empty($cur) ? $cur : 1;
        $size = input("get.size");
        $size = !empty($size) ? $size : 10;
        $start = 10*($cur-1);
        $BrokerId = '6050';
        $uid = '81331531';
        $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
        $input = "input=qryOrd $BrokerId $uid";
        $info = sendCurlPost($url,$input); 
        $a = explode('#', $info);
        $b = str_replace('</string>','', $a[2]);
        $arr = json_decode($b,true);
        // dump($arr);
        $a = array_slice($arr,$start,$size);
        $data['count'] = count($arr);
        $data['data'] = $a;
        return json($data);
	}


	// 实时持仓
	public function timehold(){
		$this->init('实时持仓');
		return $this->fetch();
	}
	public function ajax_hold(){
        $cur = input('get.cur');
        $cur = !empty($cur) ? $cur : 1;
        $size = input("get.size");
        $size = !empty($size) ? $size : 10;
        $start = 10*($cur-1);
        $BrokerId = '6050';
        $uid = '81331531';
        $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
        $input = "input=qryPos $BrokerId $uid";
        $info = sendCurlPost($url,$input); 
        $a = explode('#', $info);
        $b = str_replace('</string>','', $a[2]);
        $arr = json_decode($b,true);
        // dump($arr);
        $a = array_slice($arr,$start,$size);
        $data['count'] = count($arr);
        $data['data'] = $a;
        return json($data);
	}


	// 历史记录
	public function historylog(){
		$this->init('历史记录');
		return $this->fetch();
	}
	//工作室简介
	public function studio_instruct(){
		$param = $this->request->param();
		$id = $param['id'];

		$this->init('工作室简介');
		return $this->fetch();
	}

}