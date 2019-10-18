<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
use think\Db;
class Studioinfo extends Controller
{

	public function  init($title){
		$param = $this->request->param();
		if(!isset($param['id'])){
			$this->assign('title','页面不存在');
			return $this->fetch('public/error');
		}
		$userinfo = session('userinfo');
		$con['studio_id'] = $param['id'];
		$con['uid'] = $userinfo['id'];
		$collect = Db::table('tz_user_collect')->where($con)->find();
		if($collect)  $this->assign('collect',$collect);


		$id = $param['id'];
		$info = Db::table('tz_studio')->alias('a')->field("c.*,LPAD(a.id,6,'0') as id,b.id as uid,b.headimg,b.nickname,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') as create_time,a.studioname,a.shipan,a.celue,a.fangshi,a.zhouqi,a.price,a.futures_account,a.futures_password,a.description")->Join(['tz_userinfo'=>'b'],'b.id=a.uid','left')->Join(['tz_futures_info'=>'c'],'a.id=c.studio_id','left')->where("a.id = $id")->find();
		if(!$info){
			return '';
			// $this->assign('title','页面不存在');
			// return $this->fetch('public/error');
		}
		$userinfo = session('userinfo');
		$this->assign('info',$info);
		$this->assign('user',$userinfo);
		$this->assign('uid',$info['uid']);
		$this->assign('title',$title);
		return true;

	}
	// 工作室管理
	public function index(){
		$info = $this->init('工作室管理');
		if(!$info){
			$this->assign('title','页面不存在');
			return $this->fetch('public/error');	
		}
		return $this->fetch();
	}


    // 只需传过来工作室id   数据指标写道数据库
    public function save_info($studio_id){
            $con['id'] = $studio_id; 
            $studioinfo = Db::table('tz_studio')->where($con)->find();
            $BrokerId = $studioinfo['BrokerId'];
            $uid = $studioinfo['uid'];
            $uid = '81331531';
            $s = Db::table('tz_futures_info')->where('uid='.$uid)->find();
            if($s){
                if(time() - $s['last_time'] < 3600*3){
                    return false;
                }
            }
            $path = getcwd()."\data"."\\"."$BrokerId"."_".$uid;
            if(!is_dir($path)){
                $flag = mkdir($path,0777,true);
            }
            //综合积分
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/score.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\score.txt")) return json(['code'=>0,'msg'=>'地址不存在score']);
				$score = file_get_contents($path."\score.txt");
			}else{
	            $score = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/score.txt");
			}

            file_put_contents($path."\score.txt",$score);
            $a = parse_ini_string($score);

            $data['score_json'] = json_encode($a);
            $data['score'] = end($a);

            //每日净值
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/netValue.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\netValue.txt")) return json(['code'=>0,'msg'=>'地址不存在netValue']);
				$netValue = file_get_contents($path."\\netValue.txt");
			}else{
	            $netValue = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/netValue.txt");
			}

            file_put_contents($path."\\netValue.txt",$netValue);
            $b = parse_ini_string($netValue);
            $data['netValue'] = end($b);

            //年化收益率（string）
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatioPerYear.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\mulProfitRatioPerYear.txt")) return json(['code'=>0,'msg'=>'地址不存在mulProfitRatioPerYear']);
				$mulProfitRatioPerYear = file_get_contents($path."\mulProfitRatioPerYear.txt");
			}else{
	            $mulProfitRatioPerYear = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatioPerYear.txt");
			}

            file_put_contents($path."\mulProfitRatioPerYear.txt",$mulProfitRatioPerYear);
            $c = parse_ini_string($mulProfitRatioPerYear);
            $data['mulProfitRatioPerYear'] = end($c);


            //胜率
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winRate.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\winRate.txt")) return json(['code'=>0,'msg'=>'地址不存在winRate']);
				$winRate = file_get_contents($path."\winRate.txt");
			}else{
	            $winRate = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winRate.txt");
			}
            file_put_contents($path."\winRate.txt",$winRate);
            $d = parse_ini_string($winRate);
            $data['winRate'] = end($d);

            //最大回撤率
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxReduceRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\maxReduceRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在maxReduceRatio']);
				$maxReduceRatio = file_get_contents($path."\maxReduceRatio.txt");
			}else{
	            $maxReduceRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxReduceRatio.txt");
			}

            file_put_contents($path."\maxReduceRatio.txt",$maxReduceRatio);
            $e = parse_ini_string($maxReduceRatio);
            $data['maxReduceRatio'] = end($e);

            //截止每日盈亏比
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winLossRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\winLossRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在winLossRatio']);
				$winLossRatio = file_get_contents($path."\winLossRatio.txt");
			}else{
	            $winLossRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winLossRatio.txt");
			}

            file_put_contents($path."\winLossRatio.txt",$winLossRatio);
            $f = parse_ini_string($winLossRatio);
            $data['winLossRatio'] = end($f);

            //截止每日卡玛比率
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/kamaRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\kamaRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在kamaRatio']);
				$kamaRatio = file_get_contents($path."\kamaRatio.txt");
			}else{
	            $kamaRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/kamaRatio.txt");
			}

            file_put_contents($path."\kamaRatio.txt",$kamaRatio);
            $g = parse_ini_string($kamaRatio);
            $data['kamaRatio'] = end($g);

            //截止每日夏普比率
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/sharpRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\sharpRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在sharpRatio']);
				$sharpRatio = file_get_contents($path."\sharpRatio.txt");
			}else{
	            $sharpRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/sharpRatio.txt");
			}

            file_put_contents($path."\sharpRatio.txt",$sharpRatio);
            $k= parse_ini_string($sharpRatio);
            $data['sharpRatio'] = end($k);

            //截止每日夏普比率http://49.235.36.29/accountPerformance/6050_81331531/efficiency.txt //截止每日盈亏效率
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/efficiency.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\efficiency.txt")) return json(['code'=>0,'msg'=>'地址不存在efficiency']);
				$efficiency = file_get_contents($path."\efficiency.txt");
			}else{
	            $efficiency = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/efficiency.txt");
			}
            file_put_contents($path."\\efficiency.txt",$efficiency);
            $i= parse_ini_string($efficiency);
            $data['efficiency'] = end($i);

            //交易频率
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dealFrequency.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\dealFrequency.txt")) return json(['code'=>0,'msg'=>'地址不存在dealFrequency']);
				$dealFrequency = file_get_contents($path."\dealFrequency.txt");
			}else{
	            $dealFrequency = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dealFrequency.txt");
			}

            file_put_contents($path."\dealFrequency.txt",$dealFrequency);
            $m= parse_ini_string($dealFrequency);
            $data['dealFrequency'] = end($m);

            //交易次数
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/lot.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\lot.txt")) return json(['code'=>0,'msg'=>'地址不存在lot']);
				$lot = file_get_contents($path."\lot.txt");
			}else{
	            $lot = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/lot.txt");
			}

            file_put_contents($path."\lot.txt",$lot);
            $n= parse_ini_string($lot);
            $data['lot'] = end($n);

            //交易天数
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dealDays.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\dealDays.txt")) return json(['code'=>0,'msg'=>'地址不存在dealDays']);
				$dealDays = file_get_contents($path."\dealDays.txt");
			}else{
	            $dealDays = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dealDays.txt");
			}
            file_put_contents($path."\dealDays.txt",$dealDays);
            $n= parse_ini_string($dealDays);
            $data['dealDays'] = end($n);

            //风暴比
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/rewardRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\rewardRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在rewardRatio']);
				$rewardRatio = file_get_contents($path."\rewardRatio.txt");
			}else{
	            $rewardRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/rewardRatio.txt");
			}
            

            file_put_contents($path."\\rewardRatio.txt",$rewardRatio);
            $o= parse_ini_string($rewardRatio);
            $data['rewardRatio'] = end($o);

             //风总手续费
            
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/fee.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\fee.txt")) return json(['code'=>0,'msg'=>'地址不存在fee']);
				$fee = file_get_contents($path."\fee.txt");
			}else{
	            $fee = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/fee.txt");
			}
            file_put_contents($path."\\fee.txt",$fee);
            $p= parse_ini_string($fee);
            $data['fee'] = end($p);

             //风总手续费
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucWinDeals.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\equity.txt")) return json(['code'=>0,'msg'=>'地址不存在equity']);
				$equity = file_get_contents($path."\equity.txt");
			}else{
	            $equity = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/equity.txt");
			}
            file_put_contents($path."\\equity.txt",$equity);
            $q= parse_ini_string($equity);
            $data['equity'] = end($q);

             //最大盈利次数
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucWinDeals.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\maxSucWinDeals.txt")) return json(['code'=>0,'msg'=>'地址不存在maxSucWinDeals']);
				$maxSucWinDeals = file_get_contents($path."\maxSucWinDeals.txt");
			}else{
	            $maxSucWinDeals = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucWinDeals.txt");
			}
            $r= parse_ini_string($maxSucWinDeals);
            file_put_contents($path."\maxSucWinDeals.txt",$maxSucWinDeals);
            $data['maxSucWinDeals'] = end($r);


            //最大亏损次数
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucLossDeals.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\maxSucLossDeals.txt")) return json(['code'=>0,'msg'=>'地址不存在maxSucLossDeals']);
				$maxSucLossDeals = file_get_contents($path."\maxSucLossDeals.txt");
			}else{
	            $maxSucLossDeals = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucLossDeals.txt");
			}
            file_put_contents($path."\maxSucLossDeals.txt",$maxSucLossDeals);
            $s= parse_ini_string($maxSucLossDeals);
            $data['maxSucLossDeals'] = end($s);

            //【每天仓位】
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/riskRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\\riskRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在riskRatio']);
				$riskRatio = file_get_contents($path."\\riskRatio.txt");
			}else{
	            $riskRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/riskRatio.txt");
			}
            file_put_contents($path."\\riskRatio.txt",$riskRatio);
            $t= parse_ini_string($riskRatio);
            $data['riskRatio'] = end($t);

            // 【品种净利】
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_netProfit.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\prdID_netProfit.txt")) return json(['code'=>0,'msg'=>'地址不存在prdID_netProfit']);
				$prdID_netProfit = file_get_contents($path."\prdID_netProfit.txt");
			}else{
	            $prdID_netProfit = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_netProfit.txt");
			}
            

            file_put_contents($path."\prdID_netProfit.txt",$prdID_netProfit);
            $aa= parse_ini_string($prdID_netProfit,true);
            $data['prdID_netProfit'] = json_encode(end($aa));

            // 【品种胜率】
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winRate.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\prdID_winRate.txt")) return json(['code'=>0,'msg'=>'地址不存在prdID_winRate']);
				$prdID_winRate = file_get_contents($path."\prdID_winRate.txt");
			}else{
	            $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winRate.txt");
			}
            file_put_contents($path."\prdID_winRate.txt",$prdID_winRate);
            $bb= parse_ini_string($prdID_winRate,true);
            $data['prdID_winRate'] = json_encode(end($bb));

            //【品种盈亏比】
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winLossRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\prdID_winLossRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在prdID_winLossRatio']);
				$prdID_winLossRatio = file_get_contents($path."\prdID_winLossRatio.txt");
			}else{
	            $prdID_winLossRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winLossRatio.txt");
			}
            file_put_contents($path."\prdID_winLossRatio.txt",$prdID_winLossRatio);
            $cc= parse_ini_string($prdID_winLossRatio,true);
            $data['prdID_winLossRatio'] = json_encode(end($cc));

            //【品种手续费】
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_fee.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\prdID_fee.txt")) return json(['code'=>0,'msg'=>'地址不存在prdID_fee']);
				$prdID_fee = file_get_contents($path."\prdID_fee.txt");
			}else{
	            $prdID_fee = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_fee.txt");
			}
            file_put_contents($path."\prdID_fee.txt",$prdID_fee);
            $dd= parse_ini_string($prdID_fee,true);
            $data['prdID_fee'] = json_encode(end($dd));

            //每日日内交易天数
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dayinDealDays.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\dayinDealDays.txt")) return json(['code'=>0,'msg'=>'地址不存在dayinDealDays']);
				$dayinDealDays = file_get_contents($path."\dayinDealDays.txt");
			}else{
	            $dayinDealDays = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dayinDealDays.txt");
			}
            file_put_contents($path."\dayinDealDays.txt",$dayinDealDays);
            $ee= parse_ini_string($dayinDealDays);
            $data['dayinDealDays'] = end($ee);



            //截止每日各品种交易次数
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_deals.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\prdID_deals.txt")) return json(['code'=>0,'msg'=>'地址不存在prdID_deals']);
				$prdID_deals = file_get_contents($path."\prdID_deals.txt");
			}else{
	            $prdID_deals = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_deals.txt");
			}

            file_put_contents($path."\prdID_deals.txt",$prdID_deals);
            $gg= parse_ini_string($prdID_deals,true);
            $data['prdID_deals'] = json_encode(end($gg));



            //每日初始资金、每日出入金、每日净利润、每日净利率、每日手续费、每日交易次数、每日成交额
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/day.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\day.txt")) return json(['code'=>0,'msg'=>'地址不存在day']);
				$day = file_get_contents($path."\day.txt");
			}else{
	            $day = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/day.txt");
			}
            file_put_contents($path."\day.txt",$day);
            $h = parse_ini_string($day,true);
            // 净利润
            $data['netProfit'] = end($h['netProfit']);
            // 出入金
            $data['deposit'] = end($h['deposit']);
            // 资金规模（每日初始资金）
            $data['initialFund'] = end($h['initialFund']);


            //leiji收益率（string）
			$sarr = get_headers("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatio.txt",1); 
			if(!preg_match('/200/',$sarr[0])){ 
				if(!file_exists($path."\mulProfitRatio.txt")) return json(['code'=>0,'msg'=>'地址不存在mulProfitRatio']);
				$mulProfitRatio = file_get_contents($path."\mulProfitRatio.txt");
			}else{
	            $mulProfitRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatio.txt");
			}
            file_put_contents($path."\mulProfitRatio.txt",$mulProfitRatio);
            $l = parse_ini_string($mulProfitRatio);
            $data['mulProfitRatio'] = end($l);
            $data['last_time'] = time();
            if($s){
                $status = Db::table('tz_futures_info')->where('uid='.$uid)->update($data); 
            }else{
                $status = Db::table('tz_futures_info')->insert($data); 
            }

    }
	// 图形表 获取数据
	public function get_info(){
		$list = Db::table('tz_varieties')->select();
		foreach ($list as $key => $value) {
			$varieties_arr[$value['code']] = $value['v_name'];
		}


		$param = $this->request->param();
		$id = $param['id'];
		$this->save_info($id);
		$info = Db::table('tz_studio')->field("uid,BrokerId")->where('id='.$id)->find();
		$type = $param['type'];
		$info['uid'] = 81331531;
		//综合积分
		$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/".$type.".txt";

		$months = $param['time'];
		if($months=='all'){
			$time = 0;
		}else{
			$time = date('Ymd',strtotime("-$months months",time()));
		}

		$BrokerId = $info['BrokerId'];
		$path = getcwd()."\data"."\\"."$BrokerId"."_".$info['uid'];
		if($type=='netProfit'){
			$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/day.txt";
			$sarr = get_headers($url,1); 
			if(!preg_match('/200/',$sarr[0])){ 
				return json(['code'=>0,'msg'=>'地址不存在']);
			}

			$info = file_get_contents($url);
			file_put_contents($path."\day.txt",$info);
			$arr = parse_ini_string($info,true);
			$array=[];
	        foreach ($arr['netProfit'] as $key => $value) {
	        	if($key>$time){
		        	$array[]=["$key",round($value,2)];
	        	}
	        }
	        return $array;
		}else if($type=='deposit'){
			$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/day.txt";
			$sarr = get_headers($url,1); 
			if(!preg_match('/200/',$sarr[0])){ 
				return json(['code'=>0,'msg'=>'地址不存在']);
			}

			$info = file_get_contents($url);
			file_put_contents($path."\day.txt",$info);
			$arr = parse_ini_string($info,true);
			$array=[];
	        foreach ($arr['deposit'] as $key => $value) {
	        	if($key>$time){
		        	$array[]=["$key",round($value,2)];
	        	}
	        }
	        return $array;
		}

		$sarr = get_headers($url,1); 
		if(!preg_match('/200/',$sarr[0])){ 
			return json(['code'=>0,'msg'=>'地址不存在']);
		}

	
        $score = file_get_contents($url);
        file_put_contents($path."\\$type.txt",$score);
        $a = parse_ini_string($score);
        foreach ($a as $key => $value) {
    		$varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
        	if($type=='prdID_trdRatio' || $type=='prdID_netProfit' || $type=='prdID_winRate'){
		        	$arr[]=["$varieties ($key)",round($value,2)];
        	}else{
	        	if($key>$time){
		        	$arr[]=["$key",round($value,2)];
	        	}
        	}
        }
        return $arr;
	}

	// 收藏
	public function collect(){
		$userinfo = session('userinfo');
		if(!$userinfo) return json(['code'=>-1,'msg'=>'请先登录']);
		$con['uid'] = $userinfo['id'];
		
		$param = $this->request->param();
		$con['studio_id'] = $param['id'];
		$status = Db::table('tz_user_collect')->where($con)->find();
		if($status) return json(['code'=>1,'msg'=>'已收藏']);
		$con['create_time'] = time();

		$status1 = Db::table('tz_user_collect')->insert($con);
		if($status1){
			return json(['code'=>1,'msg'=>'收藏成功']);
		}else{
			return json(['code'=>0,'msg'=>'收藏失败']);
		}
	}


	public function get_info2(){
		$param = $this->request->param();
		try{
			$list = Db::table('tz_varieties')->select();
			foreach ($list as $key => $value) {
				$varieties_arr[$value['code']] = $value['v_name'];
			}

			$id = $param['id'];
			$info = Db::table('tz_studio')->field("uid,BrokerId")->where('id='.$id)->find();
			$type = $param['type'];
			$info['uid'] = 81331531;

			$months = $param['time'];
			if($months=='all'){
				$time = 0;
			}else{
				$time = date('Ymd',strtotime("-$months months",time()));
			}
			$BrokerId = $info['BrokerId'];
			$path = getcwd()."\data"."\\"."$BrokerId"."_".$info['uid'];
			if($type=='netProfit'){//每日净利
				$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/day.txt";
				$sarr = get_headers($url,1); 
				if(!preg_match('/200/',$sarr[0])){ 
					if(!file_exists($path."\\$type.txt")) return json(['code'=>0,'msg'=>'地址不存在']);
					$info = file_get_contents($path."\\$type.txt");
				}else{
					$info = file_get_contents($url);
				}

				file_put_contents($path."\\$type.txt",$info);
				$arr = parse_ini_string($info,true);
				$array=[];
		        foreach ($arr['netProfit'] as $key => $value) {
		        	if($key>$time){
			        	$array[]=["$key",round($value,2)];
		        	}
		        }
		        return $array;
			}else if($type=='deposit'){//出入金
				$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/day.txt";
				$sarr = get_headers($url,1); 
				if(!preg_match('/200/',$sarr[0])){ 
					if(!file_exists($path."\\$type.txt")) return json(['code'=>0,'msg'=>'地址不存在']);
					$info = file_get_contents($path."\\$type.txt");
				}else{
					$info = file_get_contents($url);
				}
				file_put_contents($path."\\$type.txt",$info);
				$arr = parse_ini_string($info,true);
				$array=[];
		        foreach ($arr['deposit'] as $key => $value) {
		        	if($key>$time){
			        	$array[]=["$key",round($value,2)];
		        	}
		        }
		        return $array;
			}else if($type=='dayinDealDays'){
				$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/dayinDealDays.txt";
				//截止每日日内交易天数
				$sarr = get_headers($url,1); 
				if(!preg_match('/200/',$sarr[0])){ 
					if(!file_exists($path."\\dayinDealDays.txt")) return json(['code'=>0,'msg'=>'地址不存在']);
					$dayinDealDays = file_get_contents($path."\dayinDealDays.txt");
				}else{
					$dayinDealDays = file_get_contents($url);
				}
				file_put_contents($path."\dayinDealDays.txt",$dayinDealDays);
	            $p= parse_ini_string($dayinDealDays);
	            $dayinDealDays = end($p);
	            $url1 = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/dealDays.txt";
				$sarr = get_headers($url1,1); 
				if(!preg_match('/200/',$sarr[0])){ 
					if(!file_exists($path."\\dealDays.txt")) return json(['code'=>0,'msg'=>'地址不存在']);
					$dealDays = file_get_contents($path."\dealDays.txt");
				}else{
		            $dealDays = file_get_contents($url1);
				}

	            //交易天数
	            file_put_contents($path."\dealDays.txt",$dealDays);
	            $n= parse_ini_string($dealDays);
	            $dealDays = end($n);
				// 日内交易比例＝日内交易天数÷总交易天数
				$data[] = ['日内',$dayinDealDays/$dealDays];
				$data[] = ['隔夜',1 - $dayinDealDays/$dealDays];

		        return $data;
			}else if($type=='monthProfit'){//出入金
				$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/monthProfit.txt";
				$sarr = get_headers($url,1); 
				if(!preg_match('/200/',$sarr[0])){ 
					if(!file_exists($path."\\dealDays.txt")) return json(['code'=>0,'msg'=>'地址不存在']);
					$info = file_get_contents($path."\\$type.txt");
				}else{
			        $info = file_get_contents($url);
				}
				
		        file_put_contents($path."\\$type.txt",$info);
		        $a = parse_ini_string($info);
		        foreach ($a as $key => $value) {
		        	if($key>$time){
			        	$arr[substr("$key",0,6)]=[round($value,2)];
		        	}
		        }
		        foreach ($arr as $key => $value) {
		        	if($key>$time){
			        	$array[]=["$key",$value];
		        	}
		        }
		        return $array;
			}


				//综合积分
			$url = "http://49.235.36.29/accountPerformance/".$info['BrokerId']."_" .$info['uid']."/".$type.".txt";
			$sarr = get_headers($url,1); 
			if(!preg_match('/200/',$sarr[0])){
				if(!file_exists($path."\\$type.txt")) return json(['code'=>0,'msg'=>'地址不存在']);
				$info = file_get_contents($path."\\$type.txt");
			}else{
		        $info = file_get_contents($url);
			}
	        file_put_contents($path."\\$type.txt",$info);
	        $a = parse_ini_string($info);
	         // || $type=='prdID_winRate' 品种胜率
	         // || $type=='prdID_netProfit' 净利润
	         // || $type=='prdID_posTimeRatio' 品种持仓偏好
	        foreach ($a as $key => $value) {
	        	if($type=='prdID_netProfit'||$type=="prdID_trdRatio"||$type=="prdID_posTimeRatio"){//成交偏好 持仓偏好
	        		$varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
		        	$arr[]=["$varieties ($key)",round($value,2)];
	        	}elseif ($type=='prdID_winRate' ||$type=="prdID_winLossRatio"||$type=="prdID_fee"||$type=="prdID_deals") {
		        	$arr[$key]=round($value,2);
	        	} else{
		        	if($key>$time){
			        	$arr[]=["$key",round($value,2)];
		        	}
	        	}
	        }

	        if($type=='prdID_winRate'){
	        	arsort($arr);
	        	$html = '';
	        	$html .= '<div class="js-content"><table class="table table-striped" style="width: 80%;margin: 0 auto;"><tr><th>品种</th><th>胜率</th></tr><tbody id="content" class="table-b">';

	        	foreach ($arr as $key => $value) {
	        		$varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
	        		$html.="<tr><td>$varieties($key)</td><td>$value%</td></tr>";
	        	}
	        	$html .= '</tbody></table></div>';
		        return $html;
	        }else if($type=="prdID_winLossRatio"){
	        	arsort($arr);
	        	// dump($arr);
	        	$html = '';
	        	$html .= '<div class="js-content"><table class="table table-striped" style="width: 80%;margin: 0 auto;"><tr><th>品种</th><th>盈亏比</th></tr><tbody id="content" class="table-b">';

	        	foreach ($arr as $key => $value) {
	        		$varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
	        		$html.="<tr><td>$varieties($key)</td><td>$value</td></tr>";
	        	}
	        	$html .= '</tbody></table></div>';
		        return $html;
	        }else if($type=="prdID_fee"){
	        	arsort($arr);
	        	// dump($arr);
	        	$html = '';
	        	$html .= '<div class="js-content"><table class="table table-striped" style="width: 80%;margin: 0 auto;"><tr><th>品种</th><th>手续费</th></tr><tbody id="content" class="table-b">';

	        	foreach ($arr as $key => $value) {
	        		$varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
	        		$html.="<tr><td>$varieties($key)</td><td>$value</td></tr>";
	        	}
	        	$html .= '</tbody></table></div>';
		        return $html;
	        }else if($type=="prdID_deals"){
	        	arsort($arr);
	        	// dump($arr);
	        	$html = '';
	        	$html .= '<div class="js-content"><table class="table table-striped" style="width: 80%;margin: 0 auto;"><tr><th>品种</th><th>品种交易次数</th></tr><tbody id="content" class="table-b">';

	        	foreach ($arr as $key => $value) {
	        		$varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
	        		$html.="<tr><td>$varieties($key)</td><td>$value</td></tr>";
	        	}
	        	$html .= '</tbody></table></div>';
		        return $html;
	        }
	        return $arr;
		}catch (Exception $e) {
		     // echo $e->getMessage();
		     return json(['code'=>0,'msg'=>'errir']);
		}
		
	}


	public function ajax_start_data(){
        $cur = input('get.cur');
        $size = input('get.size');
        $cur = !empty($cur) ? $cur : 1;
        $size = input("get.size");
        $size = !empty($size) ? $size : 10;
        $start = $size*($cur-1);
        $BrokerId = '6050';
        $uid = '81331531'; 

		$url = "http://49.235.36.29/accountPerformance/$BrokerId"."_$uid/stmt.txt";
	    $str = file_get_contents($url);//将整个文件内容读入到一个字符串中
	    $str_encoding = mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//转换字符集（编码）
	    $arr = explode("\r\n", $str_encoding);//转换成数组

	    //去除值中的空格
	    foreach ($arr as &$row) {
	        $row = trim($row);
	    }
		foreach( $arr as $k=>$v){   
		    if( !$v ){
		    	unset( $arr[$k] );
		    }else{
		    	$array[$k] = explode(',',$v);
		    }
		}  

	    // dump($arr);
        // dump($arr);
        $a = array_slice($array,$start,$size);
        $data['count'] = count($array);
        foreach ($a as $key => $value) {
        	foreach ($value as $k => $v) {
        		$a[$key]["test".$k] = $v;
        	}
        }
        $data['data'] = $a;
        return json($data);
	}


	// 取消收藏
	public function cancle_collect(){
		$userinfo = session('userinfo');
		$con['uid'] = $userinfo['id'];
		
		$param = $this->request->param();
		$con['studio_id'] = $param['id'];
		$status = Db::table('tz_user_collect')->where($con)->find();
		if(!$status) return json(['code'=>1,'msg'=>'已取消']);

		$status1 = Db::table('tz_user_collect')->where($con)->delete();
		if($status1){
			return json(['code'=>1,'msg'=>'取消成功']);
		}else{
			return json(['code'=>0,'msg'=>'取消失败']);
		}
	}

	// 实时记录
	public function timelog(){
		$this->init('实时记录');
		// ROM_UNIXTIME( datex,  '%Y%m%d' ) =20120711
		$param = $this->request->param();
		$id = $param['id'];
		$studio_info = Db::table('tz_studio')->where("id=$id")->field("BrokerID,uid")->find();
		$where['brokerID'] = $studio_info['BrokerID'];
		$where['userID'] = $studio_info['uid'];
		$where['userID'] = 81331531;


	
		$time = date('Ymd');
		$list = Db::table('tz_time_info')->where($where)->where("type='newTrade' and FROM_UNIXTIME(create_time,  '%Y%m%d' ) = $time")->order('id','desc')->select();
		foreach ($list as $key => $value) {
			$data[$key] = json_decode($value['info'],true);
		}


		$this->assign([
			'list'=>isset($data)?$data:'',
			'brokerID'=>$studio_info['BrokerID'],
			'userID'=>$where['userID']
			]);

		return $this->fetch();
	}

	// public function ajax_log(){
 //        $cur = input('get.cur');
 //        $cur = !empty($cur) ? $cur : 1;
 //        $size = input("get.size");
 //        $size = !empty($size) ? $size : 10;
 //        $start = 10*($cur-1);
 //        $BrokerId = '6050';
 //        $uid = '81331531';
 //        $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
 //        $input = "input=qryOrd $BrokerId $uid";
 //        $info = sendCurlPost($url,$input); 
 //        $a = explode('#', $info);
 //        $b = str_replace('</string>','', $a[2]);
 //        $arr = json_decode($b,true);
 //        // dump($arr);
 //        $a = array_slice($arr,$start,$size);
 //        $data['count'] = count($arr);
 //        $data['data'] = $a;
 //        return json($data);
	// }


	// 实时持仓
	public function timehold(){
		$this->init('实时持仓');
		$param = $this->request->param();
		$id = $param['id'];
		$studio_info = Db::table('tz_studio')->where("id=$id")->field("BrokerID,uid")->find();
		$where['brokerID'] = $studio_info['BrokerID'];
		$where['userID'] = $studio_info['uid'];
		$where['userID'] = 81331531;
		// dump($id);
		// ROM_UNIXTIME( datex,  '%Y%m%d' ) =20120711
		$time = date('Ymd');
		$list = Db::table('tz_time_info')->where($where)->where("status = 1 and type='positions' and FROM_UNIXTIME(create_time,  '%Y%m%d' ) = $time")->order('id','desc')->select();
		foreach ($list as $key => $value) {
			$data[$key] = json_decode($value['info'],true);
		}

		$this->assign([
			'list'=>isset($data)?$data:'',
			'brokerID'=>$studio_info['BrokerID'],
			'userID'=>$where['userID']
			]);
		return $this->fetch();
	}
	// public function ajax_hold(){
	// 	// qryPos 9999 071988
 //        $cur = input('get.cur');
 //        $cur = !empty($cur) ? $cur : 1;
 //        $size = input("get.size");
 //        $size = !empty($size) ? $size : 10;
 //        $start = 10*($cur-1);
 //        $BrokerId = '6050';
 //        $BrokerId = '9999';
 //        $uid = '81331531';
 //        $uid = '071988';
 //        $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
 //        $input = "input=qryPos $BrokerId $uid";
 //        $info = sendCurlPost($url,$input); 
 //        $a = explode('#', $info);
 //        $b = str_replace('</string>','', $a[2]);
 //        $arr = json_decode($b,true);
 //        $a = array_slice($arr,$start,$size);
 //        foreach ($a as $key => $value) {
 //        	if($value['vol'] < 0){
 //        		$a[$key]['vol']='无仓';
 //        	}elseif ($value['vol']==0) {
 //        		$a[$key]['vol']='空仓';
 //        	}else{
 //        		$a[$key]['vol']='多仓';
 //        	}
 //        }
 //        $data['count'] = count($arr);
 //        $data['data'] = $a;
 //        return json($data);
	// }


	// 历史记录
	public function historylog(){
		$this->init('历史记录');

		return $this->fetch();
	}
	public function ajax_log(){

		$list = Db::table('tz_varieties')->select();
		foreach ($list as $key => $value) {
			$varieties_arr[$value['code']] = $value['v_name'];
		}
        $cur = input('get.cur');
        $cur = !empty($cur) ? $cur : 3;
        $size = input("get.size");
        $size = !empty($size) ? $size : 10;

		$param = $this->request->param();
		$id = $param['id'];
		$studio_info = Db::table('tz_studio')->where("id=$id")->field("BrokerID,uid")->find();
		$where['brokerID'] = $studio_info['BrokerID'];
		$where['userID'] = $studio_info['uid'];
		$where['userID'] = 81331531;
	
		$time = date('Ymd');
		$list = Db::table('tz_time_info')->where($where)->where("type='newTrade'")->page($cur,$size)->order('id','desc')->select();

		$count = Db::table('tz_time_info')->where($where)->where("type='newTrade'")->count();

		foreach ($list as $key => $value) {
			$arr[$key] = json_decode($value['info'],true);
		}

        foreach ($arr as $k => $v) {
        	// $varieties = isset($varieties_arr[$key])?$varieties_arr[$key]:'';
        	$str = preg_replace( '/[^a-z]/i', '', $v['insID']);
        	$varieties = isset($varieties_arr[$str])?$varieties_arr[$str]:'';

        	$arr[$k]['insID'] = $varieties . "(".$v['insID'] . ")";

            if($v['BS']=='B'){
                $arr[$k]['BS'] = '买入';
            }else{
                $arr[$k]['BS'] = '卖出';
            }

            if($v['OC']=='O'){
                $arr[$k]['OC'] = '开仓';
            }else{
                $arr[$k]['OC'] = '平仓';
            }
        }


        $data['count'] = $count;
        $data['data'] = isset($arr)?$arr:'';
        return json($data);
	}

	//工作室简介
	public function studio_instruct(){
		$param = $this->request->param();
		$id = $param['id'];

		$this->init('工作室简介');
		return $this->fetch();
	}

}