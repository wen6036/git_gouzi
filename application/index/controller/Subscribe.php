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
		$uid = $userinfo['id'];
		$info = Db::table('tz_suborder')->alias('a')->field("a.id,a.uid,a.studio_id,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') as create_time,a.order_time,b.studioname")->join(['tz_studio'=>'b'],'a.studio_id=b.id','left')->where('a.uid='.$uid)->select();
		foreach ($info as $key => $value) {
			$m = $value['order_time'];
			$time = $value['create_time'];
			$info[$key]['end_time'] = date('Y-m-d', strtotime("$time +$m month"));

			// $time = $info[$key]['end_time'];
			// $arr = date_parse_from_format("Y年m月日",$time);
			// $time = mktime(0,0,0,$arr['month'],$arr['day'],$arr['year']);
			$time = strtotime($info[$key]['end_time']);
			if(time()>$time){
				$info[$key]['studio'] = 0;
			}else{
				$info[$key]['studio'] = 1;
			}
		}
		// dump($info);
    	$this->assign('title','订阅管理');
		return $this->fetch();
	}

	// 订阅管理
	public function subscribe(){

		return $this->fetch();
	}

	// 用户订阅得工作室
	public function studio(){
		// 查询状态为1的用户数据 并且每页显示10条数据
		// $list = Db::name('admin_logs')->paginate(10);
		// // 把分页数据赋值给模板变量list
		// $this->assign('list', $list);
		// 渲染模板输出
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
        
		$info = Db::table('tz_suborder')->alias('a')->field("a.id,a.uid,a.studio_id,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') as create_time,a.order_time,b.studioname")->join(['tz_studio'=>'b'],'a.studio_id=b.id','left')->where('a.uid='.$uid)->where($where)->page($cur, $size)->select();
		foreach ($info as $key => $value) {
			$m = $value['order_time'];
			$time = $value['create_time'];
			$info[$key]['end_time'] = date('Y-m-d', strtotime("$time +$m month"));
			$time = strtotime($info[$key]['end_time']);
			if(time()>$time){
				$info[$key]['studio'] = '无效';
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
		return $this->fetch();
	}
}