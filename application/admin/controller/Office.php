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
                $info = @file_get_contents($path."\score.txt");  
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
                $info = @file_get_contents($path."\\netValue.txt");  
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
                $info = @file_get_contents($path."\mulProfitRatioPerYear.txt");  
            }
            file_put_contents($path."\mulProfitRatioPerYear.txt",$info);
            $c = parse_ini_string($info);
            $data['mulProfitRatioPerYear'] = end($c);


            //胜率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/winRate.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\winRate.txt");    
            }
            file_put_contents($path."\winRate.txt",$info);
            $d = parse_ini_string($info);
            $data['winRate'] = end($d);

            //最大回撤率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/maxReduceRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\maxReduceRatio.txt"); 
            }
            file_put_contents($path."\maxReduceRatio.txt",$info);
            $e = parse_ini_string($info);

            $data['maxReduceRatio'] = end($e);

            //截止每日盈亏比
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/winLossRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\winLossRatio.txt");   
            }
            file_put_contents($path."\winLossRatio.txt",$info);
            $f = parse_ini_string($info);
            $data['winLossRatio'] = end($f);

            //截止每日卡玛比率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/kamaRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\kamaRatio.txt");  
            }
            file_put_contents($path."\kamaRatio.txt",$info);
            $g = parse_ini_string($info);
            $data['kamaRatio'] = end($g);

            //截止每日夏普比率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/sharpRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\sharpRatio.txt"); 
            }
            file_put_contents($path."\sharpRatio.txt",$info);
            $k = parse_ini_string($info);
            $data['sharpRatio'] = end($k);

            //截止每日夏普比率http://49.235.36.29/accountPerformance/6050_81331531/efficiency.txt //截止每日盈亏效率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/efficiency.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\\efficiency.txt");    
            }
            file_put_contents($path."\\efficiency.txt",$info);
            $i = parse_ini_string($info);
            $data['efficiency'] = end($i);

            //交易频率
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/dealFrequency.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\dealFrequency.txt");  
            }
            file_put_contents($path."\dealFrequency.txt",$info);
            $m = parse_ini_string($info);
            $data['dealFrequency'] = end($m);

            //交易次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/deals.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\deals.txt");  
            }
            file_put_contents($path."\deals.txt",$info);
            $n = parse_ini_string($info);
            $data['deals'] = end($n);

            //交易天数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/dealDays.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\dealDays.txt");   
            }
            file_put_contents($path."\dealDays.txt",$info);
            $n = parse_ini_string($info);
            $data['dealDays'] = end($n);

            //风暴比
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/rewardRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\\rewardRatio.txt");   
            }
            file_put_contents($path."\\rewardRatio.txt",$info);
            $o = parse_ini_string($info);

            $data['rewardRatio'] = end($o);

             //风总手续费
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/fee.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\\fee.txt");   
            }
            file_put_contents($path."\\fee.txt",$info);
            $p = parse_ini_string($info);

            $data['fee'] = end($p);

             //风总手续费
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/equity.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\\equity.txt");    
            }
            file_put_contents($path."\\equity.txt",$info);
            $q = parse_ini_string($info);

            $data['equity'] = end($q);

             //最大盈利次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/maxSucWinDeals.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\maxSucWinDeals.txt"); 
            }
            file_put_contents($path."\maxSucWinDeals.txt",$info);
            $r = parse_ini_string($info);

            $data['maxSucWinDeals'] = end($r);


            //最大亏损次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/maxSucLossDeals.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\maxSucLossDeals.txt");    
            }
            file_put_contents($path."\maxSucLossDeals.txt",$info);
            $s = parse_ini_string($info);

            $data['maxSucLossDeals'] = end($s);

            //【每天仓位】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/riskRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\\riskRatio.txt"); 
            }
            file_put_contents($path."\\riskRatio.txt",$info);
            $t = parse_ini_string($info);

            $data['riskRatio'] = end($t);

            // 【品种净利】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_netProfit.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\prdID_netProfit.txt");    
            }
            file_put_contents($path."\prdID_netProfit.txt",$info);
            $aa= parse_ini_string($info,true);

            $data['prdID_netProfit'] = json_encode(end($aa));

            // 【品种胜率】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_winRate.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\prdID_winRate.txt");  
            }
            file_put_contents($path."\prdID_winRate.txt",$info);
            $bb= parse_ini_string($info,true);

            $data['prdID_winRate'] = json_encode(end($bb));

            //【品种盈亏比】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_winLossRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\prdID_winLossRatio.txt"); 
            }
            file_put_contents($path."\prdID_winLossRatio.txt",$info);
            $cc= parse_ini_string($info,true);

            $data['prdID_winLossRatio'] = json_encode(end($cc));

            //【品种手续费】
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_fee.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\prdID_fee.txt");  
            }
            file_put_contents($path."\prdID_fee.txt",$info);
            $dd= parse_ini_string($info,true);

            $data['prdID_fee'] = json_encode(end($dd));

            //每日日内交易天数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/dayinDealDays.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\dayinDealDays.txt");  
            }
            file_put_contents($path."\dayinDealDays.txt",$info);
            $ee= parse_ini_string($info);

            $data['dayinDealDays'] = json_encode(end($ee));

            //截止每日各品种交易次数
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/prdID_deals.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\prdID_deals.txt");    
            }
            file_put_contents($path."\prdID_deals.txt",$info);
            $gg= parse_ini_string($info,true);

            $data['prdID_deals'] = json_encode(end($gg));


            //每日初始资金、每日出入金、每日净利润、每日净利率、每日手续费、每日交易次数、每日成交额
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/day.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\day.txt");    
            }
            file_put_contents($path."\day.txt",$info);
            $h= parse_ini_string($info,true);

            if($h){
                // 净利润
                $data['netProfit'] = array_sum($h['netProfit']);
                // 出入金
                $data['deposit'] = round(array_sum($h['deposit']),2);
                // 资金规模（每日初始资金）
                $data['initialFund'] = end($h['initialFund']);
            }

            //leiji收益率（string）
            $url = "http://49.235.36.29/accountPerformance/".$futures_company."_" .$uid."/mulProfitRatio.txt";
            $enurl = iconv('utf-8','gbk',$url);
            $info = @file_get_contents($enurl);
            if(!$info){
                $info = @file_get_contents($path."\mulProfitRatio.txt"); 
            }
            file_put_contents($path."\mulProfitRatio.txt",$info);
            $l= parse_ini_string($info);


            $data['mulProfitRatio'] = end($l);
            $data['last_time'] = time();
            $data['uid'] = $uid;
            $data['studio_id'] = $studio_id;
            if($sinfo){
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