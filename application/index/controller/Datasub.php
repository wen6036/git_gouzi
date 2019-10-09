<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Datasub extends Controller
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

	public function index(){
    	$list = Db::table('tz_banner')->where('type=2')->select();
        $this->assign('list',$list);  

        $vlist = Db::table('tz_varieties')->where('pid=0')->select();
    	$this->assign('title','数据排名订阅区');
    	$this->assign('vlist',$vlist);
		return $this->fetch();
	}

 	public function info(){
		$param = $this->request->param();
		$type = $param['type'];
		$userinfo = session('userinfo');
		if($userinfo){
			$uid = $userinfo['id'];
			$sinfo = Db::table('tz_studio')->where('uid='.$uid)->find();
			if($sinfo){
				$this->assign('studio_id',$sinfo['id']);
			}
		}

 		$pagenum = isset($param['pagenum'])?$param['pagenum']:1;
 		$size = 20;
 		$pagestart = ($pagenum-1)*$size;
 		// dump($pages);
 		$count = Db::table('tz_studio')->alias('a')->field("a.id")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.status =1')->order('c.score desc')->count();

 		$list = Db::table('tz_studio')->alias('a')->field("c.*,LPAD(b.id,6,'0') as uid,a.id,a.studioname,a.price,b.username,a.ranking")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.ranking=1 and a.status =1 and is_sub = 1')->order("c.$type desc")->limit($pagestart,$size)->select();
		$arr=[];
 		foreach ($list as $key => $value) {
 			foreach (json_decode($value['score_json']) as $k => $v) {
 				$arr[]=[$k, round($v,2)];
 			}
 			$list[$key]['data'] = json_encode($arr);
		}
        $this->assign('pagesize',$size); //每页显示数量 
        $this->assign('pagenum',$pagenum);//当前页
        $this->assign('count',$count);//总数量
        $this->assign('list',$list);  
		return $this->fetch();
	}


	public function varieties_info(){
		$param = $this->request->param();
		$id = $param['varieties_id'];
		$vinfo = Db::table('tz_varieties')->field('code')->where('id='.$id)->find();
		$v_code = $vinfo['code'];
		$userinfo = session('userinfo');
		if($userinfo){
			$uid = $userinfo['id'];
			$sinfo = Db::table('tz_studio')->where('uid='.$uid)->find();
			if($sinfo){
				$this->assign('studio_id',$sinfo['id']);
			}
		}
 		$pagenum = isset($param['pagenum'])?$param['pagenum']:1;
 		$size = 20;
 		$pagestart = ($pagenum-1)*$size;
		$count = Db::table('tz_studio')->alias('a')->field("a.id")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.status =1')->order('c.score desc')->count();

		$list = Db::table('tz_studio')->alias('a')->field("c.*,LPAD(b.id,6,'0') as uid,a.id,a.studioname,a.price,b.username,a.ranking")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.ranking=1 and a.status =1 and is_sub = 1')->order("c.id asc")->limit($pagestart,$size)->select();
		$arr=[];
 		foreach ($list as $key => $value) {
			$a = json_decode($value['prdID_netProfit'],true);
			$list[$key]['prdID_netProfit'] = isset($a[$v_code])?$a[$v_code]:0;

			$b = json_decode($value['prdID_winRate'],true);
			$list[$key]['prdID_winRate'] = isset($b[$v_code])?$b[$v_code]:0;

			$c = json_decode($value['prdID_winLossRatio'],true);
			$list[$key]['prdID_winLossRatio'] = isset($c[$v_code])?$c[$v_code]:0;

			$d = json_decode($value['prdID_fee'],true);
			$list[$key]['prdID_fee'] = isset($d[$v_code])?$d[$v_code]:0;

			$f = json_decode($value['prdID_deals'],true);
			$list[$key]['prdID_deals'] = isset($f[$v_code])?$f[$v_code]:0;


			$list[$key]['pday'] = ceil($list[$key]['prdID_deals']/$value['dayinDealDays']);
		}

        $this->assign('pagesize',$size); //每页显示数量 
        $this->assign('pagenum',$pagenum);//当前页
        $this->assign('count',$count);//总数量
        $this->assign('list',$list);  
		return $this->fetch();	
	}

	public function order_pay(){
		if(!session('userinfo')){
			$this->redirect('index/login/login');
		}
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];//用户id

		$param = $this->request->param();
		$id = $param['id'];
		$info = Db::table('tz_studio')->field('id,uid,price')->where('id='.$id)->find();


		if(!$info || $info['uid'] == $uid){
			$this->assign('title','页面错误');
			return $this->fetch('public/error');
		}
		// dump($info);
		$this->assign('info',$info);
		$this->assign('title','订阅支付');
		return $this->fetch();
	}

	public  function payorder(){
		$param = $this->request->param();
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];//用户id
		$index = $param['index'];
		// 订阅时间
		if($index==1){
			$time = 1;
		}else if($index==2){
			$time = 2;
		}else if($index==3){
			$time = 3;
		}else{
			$time = 6;
		}
		$info = Db::table('tz_studio')->where("id=".$param['id'])->find();


		$con['uid'] = $uid;
		$con['studio_id'] = $param['id'];
		$con['status'] = 1;
		$tz_suborder = Db::table('tz_suborder');
		$at = time();
		$s = Db::table('tz_suborder')->where($con)->where("$at < end_time")->find();
		if($s){
			return json(['code'=>0,'msg'=>'你已购买，还未到期']);
		}


		$data['pay_money'] = $info['price'] * $time;
		$data['paytype'] = $param['paytype'];
		$data['studio_id'] = $param['id'];//订阅房间id
		$data['uid'] = $uid;
		$data['create_time'] = $at;
		$data['status'] = 1;
		$data['end_time'] = strtotime("+$time month");
		$data['order_time'] = $time;

		$status =  Db::table('tz_suborder')->insert($data);
		$userId =  Db::table('tz_suborder')->getLastInsID();

		if($userId){
			$this->subtotle($data['studio_id'],$data['pay_money']);
			return json(['code'=>1,'msg'=>'购买成功','info'=>$userId]);
		}else{
			return json(['code'=>0,'msg'=>'购买失败']);
		}
	}


	public function subtotle($studio_id,$money){
		$con['studio_id'] = $studio_id;
		$con['status'] =  1;
		$info = Db::table('tz_subtotle_status')->where($con)->find();

		if(!$info){
			$data['order_num'] = 1;
			$data['order_totle'] = $money;
			$data['status'] = 1;
			$data['studio_id'] = $studio_id;
			$data['last_time'] = time();
			$st_status = Db::table('tz_subtotle_status')->insert($data);
		}else{
			$data['order_num'] = $info['order_num'] + 1;
			$data['order_totle'] = $info['order_totle'] + $money;
			$data['last_time'] = time();
			$st_status = Db::table('tz_subtotle_status')->where($con)->update($data);
		}
	}

	public function get_varieties(){
		$param = $this->request->param();
		$id = $param['vid'];//用户id
		$data = Db::table('tz_varieties')->where('pid='.$id)->select();
		return json($data);
	}
}