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
		$userinfo = session('userinfo');
		$uid = $userinfo['id'];
		$time = time();
		$youxiao = Db::table('tz_ticket')->alias('a')->field("a.*,FROM_UNIXTIME(a.datetime,'%Y年%m月%d') datetime")->join(['tz_userinfo'=>'b'],'b.create_time > a.create_time','left')->where("a.datetime > $time")->select();

		foreach ($youxiao as $key => $value) {
			$youxiaoarr[$value['id']] = $value;
		}

		$wuxiao = Db::table('tz_ticket')->alias('a')->field("a.*,FROM_UNIXTIME(a.datetime,'%Y年%m月%d') datetime")->join(['tz_userinfo'=>'b'],'b.create_time > a.create_time','left')->where("a.datetime < $time")->select();

		foreach ($wuxiao as $key => $value) {
			$wuxiaoarr[$value['id']] = $value;
		}

		$use = Db::table('tz_suborder')->where("uid=$uid and paytype = 3 and status = 1")->select();

		foreach ($use as $key => $value) {
			$usearr[] = $value['act_id'];
			unset($youxiaoarr[$value['act_id']]);
			unset($wuxiaoarr[$value['act_id']]);
		}
		$shiyong = Db::table('tz_ticket')->field("*,FROM_UNIXTIME(datetime,'%Y年%m月%d') datetime")->whereIn('id',$usearr)->select();

		$this->assign('youxiao',$youxiaoarr);
		$this->assign('shiyong',$shiyong);
		$this->assign('wuxiao',$wuxiaoarr);
		return $this->fetch();
	}
}