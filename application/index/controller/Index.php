<?php
/**
 * 网站首页
 *
 */

namespace app\index\controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
    	// $userinfo = session('userinfo');
    	$list = Db::table('tz_banner')->where('type=1')->select();
        $this->assign('list',$list);    
    	$this->assign('title','首页');	
        return $this->fetch();
    }

    public function hello()
    {
        return 'hello';
    }
    
}