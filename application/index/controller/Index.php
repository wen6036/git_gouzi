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
    	$list = Db::table('tz_banner')->where('type=1')->select();
        $linklist = Db::table('tz_link')->where('status=1')->select();
        $this->assign('linklist',$linklist);    
        $this->assign('list',$list);    
        $this->assign('title','首页');    
        return $this->fetch();
    }

    public function hello()
    {
        return 'hello';
    }
    
    public function test()
    {
        //综合积分
        $BrokerId = 6050;
        $uid = '81331531';
        $score = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/score.txt");
        $a = parse_ini_string($score);
        // dump($a);
        dump(end($a));

       //  //每日净值
       //  $netValue = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/netValue.txt");
       //  $b = parse_ini_string($netValue);
       //  dump(round(end($b),2));
        
    	$this->assign('title','test');	
        return $this->fetch();
    }

}