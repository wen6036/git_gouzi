<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;

// use app\common\model\Attachments;
use app\admin\model\Offices;
use think\Db;
class Office extends Base
{
    public function index()
    {
        $model = new Offices();
        $this->showDataHeaderAddButton = false;
        $this->showDataHeaderDeleteButton = false;

        $pageParam = ['query' => []];
        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            // $model->whereLike('studioname', "%" . $this->param['keywords'] . "%");
            $list = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d %H:%i:%s') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->whereLike('a.studioname', "%" . $this->param['keywords'] . "%")->where("a.status!=-1")->paginate($this->webData['list_rows'], false, $pageParam);
            $this->assign('keywords', $this->param['keywords']);
        }else{
            $list = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d %H:%i:%s') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->where("a.status!=-1")->paginate($this->webData['list_rows'], false, $pageParam);
        }
        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
                $pageParam['query']['keywords'] = $this->param['keywords'];
                // $model->whereLike('studioname', "%" . $this->param['keywords'] . "%");
                $list = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d %H:%i:%s') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->whereLike('a.studioname', "%" . $this->param['keywords'] . "%")->where("a.status!=-1")->select();
            }else{
                $list = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d %H:%i:%s') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->where("a.status!=-1")->select();
            }


            $header = ['创建日期', '工作室名称', '工作室UID', '用户名称','收费价格','期货账户','开户供货公司','类型','状态','是否禁止订阅'];
            $body   = [];
            $data   = $list;
            foreach ($data as $item) {
                $record                    = [];
                $record['create_time']              = $item['create_time'];
                $record['studioname']            = $item['studioname'];
                $record['id']            = $item['id'];
                $record['username']            = $item['username'];
                $record['price']            = $item['price'];
                $record['futures_account']            = $item['futures_account'];
                $record['futures_company']            = $item['futures_company'];
                $record['studiotype']            = $item['studiotype']>0 ?'订阅区':'展示区';
                $record['status']            = $item['status']>0 ?'审核成功':'待审核';
                $record['is_sub']            = $item['is_sub']>0 ?'允许':'禁止';;
                $body[]                    = $record;
            }
            return $this->export($header, $body, "工作室-" . date('Y-m-d-H-i-s'), '2007');
        }

        $this->assign([
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }

    //启用/禁用
    public function disable()
    {
        $user         = UserInfo::get($this->id);
        $user->status = $user->status == 1 ? 0 : 1;
        $result       = $user->save();
        if ($result) {
            return $this->success();
        }
        return $this->error();
    }

    public  function is_sub(){
        $con['id'] = $this->param['id'];
        $data['is_sub'] = $this->param['is_sub'];
        Db::table('tz_studio')->where($con)->update($data);
    }

    public function showinfo(){
        $con['id'] = $this->param['id'];
        $info = Db::table('tz_studio')->field('*,FROM_UNIXTIME(start_time, "%Y-%m-%d") AS start_time')->where($con)->find();
        return json($info);
    }


    public function del()
    {

        $id     = $this->id;
        $con['status']= -1;
        $con['delete_time'] = time();
        $result = Db::table('tz_studio')->whereIn('id', $id)->update($con);
        // $result = UserInfo::destroy(function ($query) use ($id) {
        //     $query->whereIn('id', $id);
        // });
        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }




    public function editstatus(){
        $data['studiotype'] = $this->param['type'];
        $data['status'] = $this->param['status'];
        $con['id'] = $this->param['id'];
        $status = Db::table('tz_studio')->where($con)->update($data); 
        if($this->param['status']==1){
             $this->save_info($con['id']);
        }
        

    }

    public function save_service(){
        $con['id'] = (int)$this->param['id'];
        $this->save_info($con['id']);
        $data['start_time'] = strtotime($this->param['start_time']);

        $status = Db::table('tz_studio')->where($con)->update($data);
    }


    // 只需传过来工作室id   数据指标写道数据库
    public function save_info($studio_id){
            $con['id'] = $studio_id; 
            $studioinfo = Db::table('tz_studio')->where($con)->find();
            $BrokerId = $studioinfo['BrokerId'];
            $BrokerId = 6050;
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
            $score = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/score.txt");
            file_put_contents($path."\score.txt",$score);
            $a = parse_ini_string($score);

            $data['score_json'] = json_encode($a);
            $data['score'] = end($a);

            //每日净值
            $netValue = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/netValue.txt");
            file_put_contents($path."\\netValue.txt",$netValue);
            $b = parse_ini_string($netValue);
            $data['netValue'] = end($b);

            //年化收益率（string）
            $mulProfitRatioPerYear = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatioPerYear.txt");
            file_put_contents($path."\mulProfitRatioPerYear.txt",$mulProfitRatioPerYear);
            $c = parse_ini_string($mulProfitRatioPerYear);
            $data['mulProfitRatioPerYear'] = end($c);


            //胜率
            $winRate = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winRate.txt");
            file_put_contents($path."\winRate.txt",$winRate);
            $d = parse_ini_string($winRate);
            $data['winRate'] = end($d);

            //最大回撤率
            $maxReduceRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxReduceRatio.txt");
            file_put_contents($path."\maxReduceRatio.txt",$maxReduceRatio);
            $e = parse_ini_string($maxReduceRatio);
            $data['maxReduceRatio'] = end($e);

            //截止每日盈亏比
            $winLossRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winLossRatio.txt");
            file_put_contents($path."\winLossRatio.txt",$winLossRatio);
            $f = parse_ini_string($winLossRatio);
            $data['winLossRatio'] = end($f);

            //截止每日卡玛比率
            $kamaRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/kamaRatio.txt");
            file_put_contents($path."\kamaRatio.txt",$kamaRatio);
            $g = parse_ini_string($kamaRatio);
            $data['kamaRatio'] = end($g);

            //截止每日夏普比率
            $sharpRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/sharpRatio.txt");
            file_put_contents($path."\sharpRatio.txt",$sharpRatio);
            $k= parse_ini_string($sharpRatio);
            $data['sharpRatio'] = end($k);

            //截止每日夏普比率http://49.235.36.29/accountPerformance/6050_81331531/efficiency.txt //截止每日盈亏效率
            $sharpRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/efficiency.txt");
            file_put_contents($path."\sharpRatio.txt",$sharpRatio);
            $i= parse_ini_string($sharpRatio);
            $data['efficiency'] = end($i);

            //交易频率
            $dealFrequency = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dealFrequency.txt");
            file_put_contents($path."\dealFrequency.txt",$dealFrequency);
            $m= parse_ini_string($dealFrequency);
            $data['dealFrequency'] = end($m);

            //交易次数
            $lot = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/lot.txt");
            file_put_contents($path."\lot.txt",$lot);
            $n= parse_ini_string($lot);
            $data['lot'] = end($n);

            //交易天数
            $dealDays = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dealDays.txt");
            file_put_contents($path."\dealDays.txt",$dealDays);
            $n= parse_ini_string($dealDays);
            $data['dealDays'] = end($n);

            //风暴比
            $rewardRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/rewardRatio.txt");
            file_put_contents($path."\\rewardRatio.txt",$rewardRatio);
            $o= parse_ini_string($rewardRatio);
            $data['rewardRatio'] = end($o);

             //风总手续费
            $fee = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/fee.txt");
            file_put_contents($path."\\fee.txt",$fee);
            $p= parse_ini_string($fee);
            $data['fee'] = end($p);

             //风总手续费
            $equity = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/equity.txt");
            file_put_contents($path."\\equity.txt",$equity);
            $q= parse_ini_string($equity);
            $data['equity'] = end($q);


             //最大盈利次数
            $maxSucWinDeals = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucWinDeals.txt");
            $r= parse_ini_string($maxSucWinDeals);
            file_put_contents($path."\maxSucWinDeals.txt",$maxSucWinDeals);
            $data['maxSucWinDeals'] = end($r);


            //最大亏损次数
            $maxSucLossDeals = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxSucLossDeals.txt");
            file_put_contents($path."\maxSucLossDeals.txt",$maxSucLossDeals);
            $s= parse_ini_string($maxSucLossDeals);
            $data['maxSucLossDeals'] = end($s);

            //【每天仓位】
            $riskRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/riskRatio.txt");
            file_put_contents($path."\\riskRatio.txt",$riskRatio);
            $t= parse_ini_string($riskRatio);
            $data['riskRatio'] = end($t);

            // 【品种净利】
            $prdID_netProfit = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_netProfit.txt");
            file_put_contents($path."\prdID_netProfit.txt",$prdID_netProfit);
            $aa= parse_ini_string($prdID_netProfit,true);
            $data['prdID_netProfit'] = json_encode(end($aa));

            // 【品种胜率】
            $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winRate.txt");
            file_put_contents($path."\prdID_winRate.txt",$prdID_winRate);
            $bb= parse_ini_string($prdID_winRate,true);
            $data['prdID_winRate'] = json_encode(end($bb));

            //【品种盈亏比】
            $prdID_winLossRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winLossRatio.txt");
            file_put_contents($path."\prdID_winLossRatio.txt",$prdID_winLossRatio);
            $cc= parse_ini_string($prdID_winLossRatio,true);
            $data['prdID_winLossRatio'] = json_encode(end($cc));

            //【品种手续费】
            $prdID_fee = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_fee.txt");
            file_put_contents($path."\prdID_fee.txt",$prdID_fee);
            $dd= parse_ini_string($prdID_fee,true);
            $data['prdID_fee'] = json_encode(end($dd));

            //每日日内交易天数
            $dayinDealDays = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/dayinDealDays.txt");
            file_put_contents($path."\dayinDealDays.txt",$dayinDealDays);
            $ee= parse_ini_string($dayinDealDays);
            $data['dayinDealDays'] = end($ee);



            //截止每日各品种交易次数
            $prdID_deals = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_deals.txt");
            file_put_contents($path."\prdID_deals.txt",$prdID_deals);
            $gg= parse_ini_string($prdID_deals,true);
            $data['prdID_deals'] = json_encode(end($gg));



            //每日初始资金、每日出入金、每日净利润、每日净利率、每日手续费、每日交易次数、每日成交额
            $day = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/day.txt");
            file_put_contents($path."\day.txt",$day);
            // dump($day);
            $h = parse_ini_string($day,true);
            // 净利润
            $data['netProfit'] = end($h['netProfit']);
            // 出入金
            $data['deposit'] = end($h['deposit']);
            // 资金规模（每日初始资金）
            $data['initialFund'] = end($h['initialFund']);


            //leiji收益率（string）
            $mulProfitRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatio.txt");
            $l = parse_ini_string($mulProfitRatio);
            $data['mulProfitRatio'] = end($l);
            $data['last_time'] = time();
            if($s){
                $status = Db::table('tz_futures_info')->where('uid='.$uid)->update($data); 
            }else{
                $status = Db::table('tz_futures_info')->insert($data); 
            }

    }



    public function detail_exl()
    {
        $id = $this->id;
        // $info = Db::table('tz_userinfo')->field("*,FROM_UNIXTIME(create_time, '%Y-%m-%d') as create_time")->where("id=$id")->find();

        $info = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->where("a.id=$id")->find();



            $header = ['创建日期', '工作室名称', '工作室UID', '用户名称','用户UID', '收费价格','期货账户', '开户供货公司', '类型','状态','是否禁止订阅'];
            $body   = [];
                $record['create_time']              = $info['create_time'];
                $record['studioname']            = $info['studioname'];
                $record['id']            = $info['id'];
                $record['username']            = $info['username'];
                $record['uid']            = $info['uid'];
                $record['price']            = $info['price'];
                $record['futures_account']            = $info['futures_account'];
                $record['futures_company']            = $info['futures_company'];
                $record['studiotype']            = $info['studiotype']>0 ?'订阅区':'展示区';
                $record['status']            = $info['status']>0 ?'审核成功':'待审核';
                $record['is_sub']            = $info['is_sub']>0 ?'允许':'禁止';;

                $body[]                    = $record;
            return $this->export($header, $body, $info['studioname']."工作室-" . date('Y-m-d-H-i-s'), '2007');
    }

}