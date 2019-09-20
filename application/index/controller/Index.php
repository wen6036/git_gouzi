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

       //  ////年化收益率（string）
       //  $mulProfitRatioPerYear = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/mulProfitRatioPerYear.txt");
       //  $c = parse_ini_string($mulProfitRatioPerYear);
       //  dump(end($c));

       //  //胜率
       //  $winRate = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/winRate.txt");
       //  $d = parse_ini_string($winRate);
       //  dump(end($d));

       //  //最大回撤率
       //  $maxReduceRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/maxReduceRatio.txt");
       //  $e = parse_ini_string($maxReduceRatio);
       //  dump(end($e));

       //   //截止每日盈亏比
       //  $winLossRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/winLossRatio.txt");
       //  $f = parse_ini_string($winLossRatio);
       //  dump(end($f));

       //   //截止每日夏普比率
       //  $sharpRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/sharpRatio.txt");
       //  $g = parse_ini_string($sharpRatio);
       //  dump(end($g));

       //   //截止每日盈亏效率
       //  $efficiency = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/efficiency.txt");
       //  $h = parse_ini_string($efficiency);
       //  dump(end($h));

       //   //截止每日总交易手数
       //  $lot = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/lot.txt");
       //  $i = parse_ini_string($lot);
       //  dump(end($i));

       //   //截止每日总手续费
       //  $fee = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/fee.txt");
       //  $j = parse_ini_string($fee);
       //  dump(end($j));

       //    //截止每日累计收益率（用累计净值计算）
       //  $mulProfitRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/mulProfitRatio.txt");
       //  $k = parse_ini_string($mulProfitRatio);
       //  dump(end($k));


       //  //每日初始资金、每日出入金、每日净利润、每日净利率、每日手续费、每日交易次数、每日成交额
       //  $day = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/day.txt");
       //  // dump($day);
       //  $l = parse_ini_string($day,true);
       //  // 净利润
       //  dump(end($l['netProfit']));
       //  // 资金规模（每日初始资金）
       //  dump(end($l['initialFund']));
       //  //累计出入金
       //  dump(end($l['deposit']));
        
       //    //截止每日风险回报比
       //  $rewardRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/rewardRatio.txt");
       //  $m = parse_ini_string($rewardRatio);
       //  dump(end($m));

       //   //截止每日卡玛比率
       //  $kamaRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/kamaRatio.txt");
       //  $n = parse_ini_string($kamaRatio);
       //  dump(end($n));

       //   //截止每日各品种成交偏好
       //  $prdID_trdRatio = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/prdID_trdRatio.txt");
       //  $n = parse_ini_string($prdID_trdRatio);
       //  dump(end($n));

       //    //截止每日各品种平仓净利
       //  $prdID_netProfit = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/prdID_netProfit.txt");
       //  $o = parse_ini_string($prdID_netProfit);
       //  dump(end($o));


       // //截止每日各品种胜率
       //  $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/prdID_winRate.txt");
       //  $p = parse_ini_string($prdID_winRate);
       //  dump(end($p));

       //  //截止每日总交易天数（每张结算单算一天
       //  $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/dealDays.txt");
       //  $q = parse_ini_string($prdID_winRate);
       //  dump(end($q));

       //   //截止每日总交易次数
       //  $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/deals.txt");
       //  $r = parse_ini_string($prdID_winRate);
       //  dump(end($r));

       //   //截止每日交易频率
       //  $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/6050_81331531/dealFrequency.txt");
       //  $r = parse_ini_string($prdID_winRate);
       //  dump(end($r));


        // dump($score);
        // $a = parse_ini_file($score,true);
        // dump($a);
        
    	$this->assign('title','test');	
        return $this->fetch();
    }

}