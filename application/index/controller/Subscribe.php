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
		$list = Db::name('admin_logs')->paginate(10);
		// 把分页数据赋值给模板变量list
		$this->assign('list', $list);
		// 渲染模板输出
		return $this->fetch();
	}

	public function ajax_studio(){
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

        
        $data['data'] = Db::name('admin_logs')->where($where)->page($cur, $size)->select();
    	$data['count'] = Db::name('admin_logs')->where($where)->count();
        return json($data);


	}

	// 我得收藏
	public function collect(){
		return $this->fetch();
	}

	// 体验券
	public function coupon(){
		return $this->fetch();
	}
}