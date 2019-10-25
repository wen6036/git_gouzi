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
            $futures_company = $studioinfo['futures_company'];
            $uid = $studioinfo['futures_account'];
            $sinfo = Db::table('tz_futures_info')->where('uid='.$uid)->find();
            if($sinfo){
                if(time() - $sinfo['last_time'] < 3600*3){
                    return false;
                }
            }
            $path = getcwd()."\data"."\\"."$futures_company"."_".$uid;
            if(!is_dir($path)){
                $flag = mkdir($path,0777,true);
            }
            //综合积分
			$url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/score.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\$type.txt");	
			}
	        file_put_contents($path."\\score.txt",$info);
	        $a = parse_ini_string($info);


            $data['score_json'] = json_encode($a);
            $data['score'] = end($a);

            //每日净值
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/netValue.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\netValue.txt");	
			}
	        file_put_contents($path."\\netValue.txt",$info);
	        $b = parse_ini_string($info);


            $data['netValue_json'] = json_encode($b);
            $data['netValue'] = end($b);
            //年化收益率（string）
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/mulProfitRatioPerYear.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\mulProfitRatioPerYear.txt");	
			}
	        file_put_contents($path."\mulProfitRatioPerYear.txt",$info);
	        $c = parse_ini_string($info);
            $data['mulProfitRatioPerYear'] = end($c);


            //胜率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/winRate.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\winRate.txt");	
			}
	        file_put_contents($path."\winRate.txt",$info);
	        $d = parse_ini_string($info);
            $data['winRate'] = end($d);

            //最大回撤率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/maxReduceRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\maxReduceRatio.txt");	
			}
	        file_put_contents($path."\maxReduceRatio.txt",$info);
	        $e = parse_ini_string($info);

            $data['maxReduceRatio'] = end($e);

            //截止每日盈亏比
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/winLossRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\winLossRatio.txt");	
			}
	        file_put_contents($path."\winLossRatio.txt",$info);
	        $f = parse_ini_string($info);
            $data['winLossRatio'] = end($f);

            //截止每日卡玛比率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/kamaRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\kamaRatio.txt");	
			}
	        file_put_contents($path."\kamaRatio.txt",$info);
	        $g = parse_ini_string($info);
            $data['kamaRatio'] = end($g);

            //截止每日夏普比率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/sharpRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\sharpRatio.txt");	
			}
	        file_put_contents($path."\sharpRatio.txt",$info);
	        $k = parse_ini_string($info);
            $data['sharpRatio'] = end($k);

            //截止每日夏普比率http://49.235.36.29/accountPerformance/6050_81331531/efficiency.txt //截止每日盈亏效率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/efficiency.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\efficiency.txt");	
			}
	        file_put_contents($path."\efficiency.txt",$info);
	        $i = parse_ini_string($info);
            $data['efficiency'] = end($i);

            //交易频率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/dealFrequency.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\dealFrequency.txt");	
			}
	        file_put_contents($path."\dealFrequency.txt",$info);
	        $m = parse_ini_string($info);
            $data['dealFrequency'] = end($m);

            //交易次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/deals.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\deals.txt");	
			}
	        file_put_contents($path."\deals.txt",$info);
	        $n = parse_ini_string($info);
            $data['deals'] = end($n);

            //交易天数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/dealDays.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\dealDays.txt");	
			}
	        file_put_contents($path."\dealDays.txt",$info);
	        $n = parse_ini_string($info);
            $data['dealDays'] = end($n);

            //风暴比
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/rewardRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\rewardRatio.txt");	
			}
	        file_put_contents($path."\\rewardRatio.txt",$info);
	        $o = parse_ini_string($info);

            $data['rewardRatio'] = end($o);

             //风总手续费
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/fee.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\fee.txt");	
			}
	        file_put_contents($path."\fee.txt",$info);
	        $p = parse_ini_string($info);

            $data['fee'] = end($p);

             //风总手续费
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/equity.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\equity.txt");	
			}
	        file_put_contents($path."\equity.txt",$info);
	        $q = parse_ini_string($info);

            $data['equity'] = end($q);

             //最大盈利次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/maxSucWinDeals.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\maxSucWinDeals.txt");	
			}
	        file_put_contents($path."\maxSucWinDeals.txt",$info);
	        $r = parse_ini_string($info);

            $data['maxSucWinDeals'] = end($r);


            //最大亏损次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/maxSucLossDeals.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\maxSucLossDeals.txt");	
			}
	        file_put_contents($path."\maxSucLossDeals.txt",$info);
	        $s = parse_ini_string($info);

            $data['maxSucLossDeals'] = end($s);

            //【每天仓位】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/riskRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\riskRatio.txt");	
			}
	        file_put_contents($path."\\riskRatio.txt",$info);
	        $t = parse_ini_string($info);

            $data['riskRatio'] = end($t);

            // 【品种净利】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_netProfit.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\prdID_netProfit.txt");	
			}
	        file_put_contents($path."\prdID_netProfit.txt",$info);
	        $aa= parse_ini_string($info,true);

            $data['prdID_netProfit'] = json_encode(end($aa));

            // 【品种胜率】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_winRate.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\prdID_winRate.txt");	
			}
	        file_put_contents($path."\prdID_winRate.txt",$info);
	        $bb= parse_ini_string($info,true);

            $data['prdID_winRate'] = json_encode(end($bb));

            //【品种盈亏比】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_winLossRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\prdID_winLossRatio.txt");	
			}
	        file_put_contents($path."\prdID_winLossRatio.txt",$info);
	        $cc= parse_ini_string($info,true);

            $data['prdID_winLossRatio'] = json_encode(end($cc));

            //【品种手续费】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_fee.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\prdID_fee.txt");	
			}
	        file_put_contents($path."\prdID_fee.txt",$info);
	        $dd= parse_ini_string($info,true);

            $data['prdID_fee'] = json_encode(end($dd));

            //每日日内交易天数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/dayinDealDays.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\dayinDealDays.txt");	
			}
	        file_put_contents($path."\dayinDealDays.txt",$info);
	        $ee= parse_ini_string($info,true);

            $data['dayinDealDays'] = json_encode(end($ee));

            //截止每日各品种交易次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_deals.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\prdID_deals.txt");	
			}
	        file_put_contents($path."\prdID_deals.txt",$info);
	        $gg= parse_ini_string($info,true);

            $data['prdID_deals'] = json_encode(end($gg));


            //每日初始资金、每日出入金、每日净利润、每日净利率、每日手续费、每日交易次数、每日成交额
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/day.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\day.txt");	
			}
	        file_put_contents($path."\day.txt",$info);
	        $h= parse_ini_string($info,true);


            // 净利润
            $data['netProfit'] = array_sum($h['netProfit']);
            // 出入金
            $data['deposit'] = round(array_sum($h['deposit']),2);
            // 资金规模（每日初始资金）
            $data['initialFund'] = end($h['initialFund']);

            //leiji收益率（string）
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/mulProfitRatio.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\mulProfitRatio.txt");	
			}
	        file_put_contents($path."\mulProfitRatio.txt",$info);
	        $l= parse_ini_string($info);


            $data['mulProfitRatio'] = end($l);
            $data['last_time'] = time();
            if($sinfo){
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
		$a = $this->save_info($id);
		$info = Db::table('tz_studio')->field("uid,futures_account,BrokerId,futures_company")->where('id='.$id)->find();
		$type = $param['type'];
		$futures_account = $info['futures_account'];//期货账户
		//综合积分
		$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/".$type.".txt";
		$months = $param['time'];
		if($months=='all'){
			$time = 0;
		}else{
			$time = date('Ymd',strtotime("-$months months",time()));
		}

		$futures_company = $info['futures_company'];
		$path = getcwd()."\data"."\\"."$futures_company"."_".$futures_account;
		if($type=='netProfit'){
			$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/day.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\$type.txt");	
			}
	        file_put_contents($path."\\day.txt",$info);
	        $a = parse_ini_string($info,true);


			$array=[];
	        foreach ($arr['netProfit'] as $key => $value) {
	        	if($key>$time){
		        	$array[]=["$key",round($value,2)];
	        	}
	        }
	        return $array;
		}else if($type=='deposit'){
			$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/day.txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\$type.txt");	
			}
	        file_put_contents($path."\\day.txt",$info);
	        $a = parse_ini_string($info,true);

			$array=[];
	        foreach ($arr['deposit'] as $key => $value) {
	        	if($key>$time){
		        	$array[]=["$key",round($value,2)];
	        	}
	        }
	        return $array;
		}

		$enurl = iconv('utf-8','gbk',$url);
		$info = @file_get_contents($enurl);

		if(!$info){
			$info = file_get_contents($path."\\$type.txt");	
		}

        file_put_contents($path."\\$type.txt",$info);
        $a = parse_ini_string($info);

        if(!$a) return json(['code'=>0,'msg'=>'暂无数据']);
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
        // return json(['code'=>0,'msg'=>'地址不存在']);
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
			$info = Db::table('tz_studio')->field("futures_account,uid,BrokerId,futures_company")->where('id='.$id)->find();
			$type = $param['type'];
			$futures_account = $info['futures_account'];

			$months = $param['time'];
			if($months=='all'){
				$time = 0;
			}else{
				$time = date('Ymd',strtotime("-$months months",time()));
			}
			$futures_company = $info['futures_company'];
			$path = getcwd()."\data"."\\"."$futures_company"."_".$futures_account;

            if(!is_dir($path)){
                $flag = mkdir($path,0777,true);
            }

			if($type=='netProfit'){//每日净利
				$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/day.txt";

				$enurl = iconv('utf-8','gbk',$url);
				$info = @file_get_contents($enurl);

				if(!$info){
					$info = file_get_contents($path."\\$type.txt");	
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
				$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/day.txt";
				$enurl = iconv('utf-8','gbk',$url);
				$info = @file_get_contents($enurl);

				if(!$info){
					$info = file_get_contents($path."\\$type.txt");	
				}

		        file_put_contents($path."\\$type.txt",$info);
		        $arr = parse_ini_string($info);


				$array=[];
		        foreach ($arr['deposit'] as $key => $value) {
		        	if($key>$time){
			        	$array[]=["$key",round($value,2)];
		        	}
		        }
		        return $array;
			}else if($type=='dayinDealDays'){
				$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/dayinDealDays.txt";
				//截止每日日内交易天数
				$enurl = iconv('utf-8','gbk',$url);
				$info = @file_get_contents($enurl);

				if(!$info){
					$info = file_get_contents($path."\\$type.txt");	
				}

		        file_put_contents($path."\\$type.txt",$info);
		        $p = parse_ini_string($info);



	            $dayinDealDays = end($p);
	            $url1 = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/dealDays.txt";
				$enurl = iconv('utf-8','gbk',$url);
				$info = @file_get_contents($enurl);

				if(!$info){
					$info = file_get_contents($path."\\$type.txt");	
				}

		        file_put_contents($path."\\$type.txt",$info);
		        $n = parse_ini_string($info);
	            $dealDays = end($n);
				// 日内交易比例＝日内交易天数÷总交易天数
				$data[] = ['日内',$dayinDealDays/$dealDays];
				$data[] = ['隔夜',1 - $dayinDealDays/$dealDays];

		        return $data;
			}else if($type=='monthProfit'){//出入金
				$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/monthProfit.txt";
				$enurl = iconv('utf-8','gbk',$url);
				$info = @file_get_contents($enurl);

				if(!$info){
					$info = file_get_contents($path."\\$type.txt");	
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
			$url = "http://49.235.36.29/accountPerformance/".$info['futures_company']."_" .$futures_account."/".$type.".txt";
			$enurl = iconv('utf-8','gbk',$url);
			$info = @file_get_contents($enurl);
			if(!$info){
				$info = file_get_contents($path."\\$type.txt");	
			}
	        file_put_contents($path."\\$type.txt",$info);
	        $a = parse_ini_string($info);
	        if(!$a) return json(['code'=>0,'msg'=>'暂无数据']);
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


	// 原始数据
	public function ajax_start_data(){
		$param = $this->request->param();
		
        $con['id'] = $param['id'];
        $studioinfo = Db::table('tz_studio')->where($con)->find();
        $BrokerId = $studioinfo['BrokerId'];
        $futures_company = $studioinfo['futures_company'];
        $uid = $studioinfo['futures_account'];

        $cur = input('get.cur');
        $size = input('get.size');
        $cur = !empty($cur) ? $cur : 1;
        $size = input("get.size");
        $size = !empty($size) ? $size : 10;
        $start = $size*($cur-1);

		$url = "http://49.235.36.29/accountPerformance/$futures_company"."_$uid/stmt.txt";
		$path = getcwd()."\data"."\\"."$futures_company"."_".$uid;

		$enurl = iconv('utf-8','gbk',$url);
		$str = @file_get_contents($enurl);

		if(!$str){
			$str = file_get_contents($path."\stmt.txt");	
		}

        file_put_contents($path."\stmt.txt",$str);

		// $sarr = get_headers($url,1); 
		// if(!preg_match('/200/',$sarr[0])){ 
		// 	if(!file_exists($url)) return json(['code'=>0,'msg'=>'地址不存在stmt']);
		// 	$str = file_get_contents($path."\stmt.txt");
		// }else{
  //           $str = file_get_contents($url);
		// }
  //       file_put_contents($path."\stmt.txt",$str);
	    // $str = file_get_contents($url);//将整个文件内容读入到一个字符串中
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

		if(!isset($array)) return json([]);

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
		$studio_info = Db::table('tz_studio')->where("id=$id")->field("futures_account,BrokerID,futures_company,uid")->find();
		// $where['brokerID'] = $studio_info['BrokerID'];
		$where['futures_company'] = $studio_info['futures_company'];
		$where['userID'] = $studio_info['futures_account'];
	
		$time = date('Ymd');
		$list = Db::table('tz_time_info')->where($where)->where("status = 1 and type='newTrade' and FROM_UNIXTIME(create_time,  '%Y%m%d' ) = $time")->order('id','desc')->select();
		foreach ($list as $key => $value) {
			$data[$key] = json_decode($value['info'],true);
            $posRatio = isset($value['posRatio'])?$value['posRatio']:0;
            $data[$k]['posRatio'] = round($posRatio * 100,2)."%";
		}

		$this->assign([
			'list'=>isset($data)?$data:'',
			'futures_company'=>$studio_info['futures_company'],
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
		$studio_info = Db::table('tz_studio')->where("id=$id")->field("futures_account,BrokerID,uid")->find();
		// $where['brokerID'] = $studio_info['BrokerID'];
		$where['futures_company'] = $studio_info['futures_company'];
		$where['userID'] = $studio_info['futures_account'];
		// ROM_UNIXTIME( datex,  '%Y%m%d' ) =20120711
		$time = date('Ymd');
		$list = Db::table('tz_time_info')->where($where)->where("status = 1 and type='positions' and FROM_UNIXTIME(create_time,  '%Y%m%d' ) = $time")->order('id','desc')->select();
		foreach ($list as $key => $value) {
			$data[$key] = json_decode($value['info'],true);
		}

		$this->assign([
			'list'=>isset($data)?$data:'',
			'futures_company'=>$studio_info['futures_company'],
			'userID'=>$where['userID']
			]);
		return $this->fetch();
	}

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
		$studio_info = Db::table('tz_studio')->where("id=$id")->field("futures_account,BrokerID,futures_company,uid")->find();
		$where['futures_company'] = $studio_info['futures_company'];
		// $where['brokerID'] = $studio_info['BrokerID'];
		$where['userID'] = $studio_info['futures_account'];
	
		$time = date('Ymd');
		$list = Db::table('tz_time_info')->where($where)->where("type='newTrade'")->page($cur,$size)->order('id','desc')->select();

		$count = Db::table('tz_time_info')->where($where)->where("type='newTrade'")->count();

		foreach ($list as $key => $value) {
			$arr[$key] = json_decode($value['info'],true);
		}

        foreach ($arr as $k => $v) {
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
            $posRatio = isset($v['posRatio'])?$v['posRatio']:0;
            $arr[$k]['posRatio'] = round($posRatio * 100,2)."%";
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