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
    	$this->assign('title','数据排名订阅区');
		return $this->fetch();
	}

 	public function info(){
		$param = $this->request->param();

		$userinfo = session('userinfo');
		if($userinfo){
			$uid = $userinfo['id'];
			$sinfo = Db::table('tz_studio')->where('uid='.$uid)->find();
			if($sinfo){
				$this->assign('studio_id',$sinfo['id']);
			}
		}

 		$pagenum = isset($param['pagenum'])?$param['pagenum']:1;
 		$size = 10;
 		$pagestart = ($pagenum-1)*$size;
 		// dump($pages);
 		$count = Db::table('tz_studio')->alias('a')->field("a.id")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.status =1')->order('c.score desc')->count();

 		$list = Db::table('tz_studio')->alias('a')->field("c.*,LPAD(b.id,6,'0') as uid,a.id,a.studioname,a.price,b.username,a.ranking")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.status =1')->order('c.score desc')->limit($pagestart,$size)->select();
		// dump(array_keys(json_decode($list[0]['equitySeries'],true)));
 		foreach ($list as $key => $value) {
 			$list[$key]['x'] = json_encode(array_keys(json_decode($value['equitySeries'],true)));
 			$list[$key]['y'] = json_encode(array_values(json_decode($value['equitySeries'],true)));
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

		$data['pay_money'] = $info['price'] * $time;
		$data['paytype'] = $param['paytype'];
		$data['studio_id'] = $param['id'];//订阅房间id
		$data['uid'] = $uid;
		$data['create_time'] = time();
		$data['order_time'] = $time;
		// $data['status'] = 0;



		$status = Db::table('tz_suborder')->insert($data);
		$userId = Db::table('tz_suborder')->getLastInsID();
		dump($userId);
	}


}