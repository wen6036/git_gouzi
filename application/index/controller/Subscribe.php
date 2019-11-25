<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Subscribe extends Controller
{
	public function index(){
		$userinfo = session('userinfo');
		if(!$userinfo){
			$this->redirect('index/login/login');
		}
        $con['id'] = $userinfo['id'];
        $info = Db::table('tz_userinfo')->where($con)->find();
		
		$this->assign('headimg',$info['headimg']);
		$this->assign('nickname',$info['nickname']);
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

	public function ajax_studio(){
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];
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
		$info = Db::table('tz_suborder')->alias('a')->field("a.id,a.uid,a.studio_id,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') as create_time,FROM_UNIXTIME(a.end_time, '%Y-%m-%d') as end_time,a.order_time,b.studioname")->join(['tz_studio'=>'b'],'a.studio_id=b.id','left')->where('a.uid='.$uid)->where($where)->page($cur, $size)->select();
		foreach ($info as $key => $value) {
			// $m = $value['order_time'];
			// $time = $value['create_time'];
			// $info[$key]['end_time'] = date('Y-m-d', strtotime("$time +$m month"));
			// $time = strtotime($info[$key]['end_time']);
			if(date('Y-m-d')>$value['end_time']){
				$info[$key]['studio'] = '过期';
			}else{
				$info[$key]['studio'] = '有效';
			}
		}
		$data['data'] = $info;
        // $data['data'] = Db::name('admin_logs')->where($where)->page($cur, $size)->select();
    	$data['count'] = Db::table('tz_suborder')->field("id")->where('uid='.$uid)->where($where)->count();
        return json($data);


	}

	// 我得收藏
	public function collect(){
		return $this->fetch();
	}
	public function ajax_collect(){
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];

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

        $info = Db::table('tz_user_collect')->alias('a')->field("a.id,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') as create_time,b.id as studio_id,b.studioname")->join(['tz_studio'=>'b'],'a.studio_id=b.id','left')->where('a.uid='.$uid)->where($where)->page($cur, $size)->select();
        $data['data'] = $info;
        $data['count'] = Db::table('tz_user_collect')->alias('a')->field("a.id")->join(['tz_studio'=>'b'],'a.studio_id=b.id','left')->where('a.uid='.$uid)->where($where)->count();
        return json($data);
	}

	// 体验券
	public function coupon(){

		$youxiaoarr = [];
		$wuxiaoarr=[];
		$shiyong=[];
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];
		$info = Db::table('tz_userinfo')->where("id = $uid")->find();
		$time = $info['create_time'];

		$time1 = $time+15*24*60*60;
		$date1 = date('Y-m-d',$time1);

		$wuxiao = [];
		$youxiao = [];
		if(time()>$time1){
			$a['datetime']  =$date1;
			$a['type']  =1;
			$wuxiao[]=$a ;
			// 15天已过期：
		}
		if(time() <= $time1){
			// 15天有效
			$a['datetime']  =$date1;
			$a['type']  =1;
			$youxiao[] = $a;
		}

		$time2 = $time+30*24*60*60;
		$date2 = date('Y-m-d',$time2);

		if(time()>$time2){
			$b['datetime']  =$date2;
			$b['type']  =2;
			$wuxiao[]= $b;
			// 30天已过期：
		}
		if(time()<=$time2){
			$b['datetime']  =$date2;
			$b['type']  =2;
			$youxiao[] = $b;
			// 30天有效
		}
		foreach ($youxiao as $key => $value) {
			$youxiaoarr[$value['type']] = $value;
		}
		foreach ($wuxiao as $key => $value) {
			$wuxiaoarr[$value['type']] = $value;
		}

		$idlist = Db::table('tz_suborder')->field('act_id')->where("paytype = 3 and uid=$uid")->select();

		if($idlist){
			foreach ($idlist as $key => $value) {
				if(isset($youxiaoarr)){
					unset($youxiaoarr[$value['act_id']]);
				}
				if (isset($wuxiaoarr)) {
					unset($wuxiaoarr[$value['act_id']]);
				}

				if($value['act_id']==1){
					$c['datetime']  =$date1;
					$shiyong[] = $c;
				}
				if($value['act_id']==2){
					$c['datetime']  =$date2;
					$shiyong[] = $c;
				}
			}
		}

		$this->assign('wuxiao',$wuxiaoarr);
		$this->assign('youxiao',$youxiaoarr);
		$this->assign('shiyong',$shiyong);
		return $this->fetch();
	}
}