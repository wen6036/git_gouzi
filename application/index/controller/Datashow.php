<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Datashow extends Controller
{

	public function index(){
    	$list = Db::table('tz_banner')->where('type=2')->select();
        $this->assign('list',$list);  
    	$this->assign('title','数据排名订阅区');
		return $this->fetch();
	}

 	public function info(){
		$param = $this->request->param();

 		$pagenum = isset($param['pagenum'])?$param['pagenum']:1;
 		$size = 1;
 		$pagestart = ($pagenum-1)*$size;
 		// dump($pages);
 		$count = Db::table('tz_studio')->alias('a')->field("a.id")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.ranking=1 and a.status =1')->order('c.score desc')->count();

 		$list = Db::table('tz_studio')->alias('a')->field("c.*,LPAD(b.id,6,'0') as uid,a.id,a.studioname,a.price,b.username")->join(['tz_userinfo'=>'b'],'a.uid = b.id')->join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where('a.ranking=1 and a.status =1')->order('c.score desc')->limit($pagestart,$size)->select();
		$arr=[];
 		foreach ($list as $key => $value) {
 			foreach (json_decode($value['equitySeries'],true) as $k => $v) {
 				$arr[]=[$k,$v];
 			}
 			$list[$key]['data'] = json_encode($arr);
		}
 		// dump($list);
        $this->assign('pagesize',$size); //每页显示数量 
        $this->assign('pagenum',$pagenum);//当前页
        $this->assign('count',$count);//总数量
        $this->assign('list',$list);  
		return $this->fetch();

	}
}