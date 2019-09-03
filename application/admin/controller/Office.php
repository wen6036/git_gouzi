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
            $list = Db::table('tz_studio')->alias('a')->field('a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,a.id,a.uid,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time')->join(['tz_userinfo'=>'b'],'a.uid=a.id','left')->whereLike('a.uid', "%" . $this->param['keywords'] . "%")->paginate($this->webData['list_rows'], false, $pageParam);
            $this->assign('keywords', $this->param['keywords']);
        }else{
            $list = Db::table('tz_studio')->alias('a')->field('a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,a.id,a.uid,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->paginate($this->webData['list_rows'], false, $pageParam);
            
        }

        // dump($list);
        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
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

        // $list = $model->paginate($this->webData['list_rows'], false, $pageParam);
        // dump($list->toArray());
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

    public function editstatus(){
        $con['id'] = $this->param['id']; 

        $studioinfo = Db::table('tz_studio')->where($con)->find();
// dump($studioinfo);
        $BrokerId = $studioinfo['BrokerId'];
        $uid = $studioinfo['uid'];
        $uid = '071988';
        $data['studiotype'] = $this->param['type'];
        $data['status'] = $this->param['status'];
        if($this->param['status']==1){
            // qryPerformance brokerID userID 20190101
             
            $url = 'http://49.235.36.29/WebFunctions.asmx/qry';
            $input = "input=qryPerformance $BrokerId $uid 20190101";
            $info = sendCurlPost($url,$input); 
            // dump($info);

            $a = explode('#', $info);
            $b = str_replace('</string>','', $a[2]);

            $arr = json_decode($b,true);
            // $data1['datainfo'] = $b;
            $data1['uid'] = $uid;
            $data1['studio_id'] = $studioinfo['id'];
            $data1['periodType'] = $arr['periodType'];
            $data1['initialFund'] = $arr['initialFund'];
            $data1['netProfit'] = $arr['netProfit'];
            $data1['fee'] = $arr['fee'];
            $data1['efficiency'] = $arr['efficiency'];
            $data1['deals'] = $arr['deals'];
            $data1['winRate'] = $arr['winRate'];
            $data1['maxSucWinDeals'] = $arr['maxSucWinDeals'];
            $data1['maxSucLossDeals'] = $arr['maxSucLossDeals'];
            $data1['dealDays'] = $arr['dealDays'];
            $data1['dealFrequency'] = $arr['dealFrequency'];
            $data1['deposit'] = $arr['deposit'];
            $data1['withdraw'] = $arr['withdraw'];
            $data1['rewardRatio'] = $arr['rewardRatio'];
            $data1['totalWinLossRatio'] = $arr['totalWinLossRatio'];
            $data1['winLossRatio'] = $arr['winLossRatio'];
            $data1['mulNetValue'] = $arr['mulNetValue'];
            $data1['mulProfitRatio'] = $arr['mulProfitRatio'];
            $data1['mulProfitRatioPerYear'] = $arr['mulProfitRatioPerYear'];
            $data1['maxReduceRatio'] = $arr['maxReduceRatio'];
            $data1['score'] = $arr['score'];
            $data1['kamaRatio'] = $arr['kamaRatio'];
            $data1['sharpRatio'] = $arr['sharpRatio'];

            $data1['equitySeries'] = json_encode($arr['equitySeries']);
            $data1['netProfitSeries'] = json_encode($arr['netProfitSeries']);
            $data1['monthProfitSeries'] = json_encode($arr['monthProfitSeries']);
            $data1['riskRatioSeries'] = json_encode($arr['riskRatioSeries']);
            $data1['prdID_netProfit'] = json_encode($arr['prdID_netProfit']);
            $data1['prdID_totalWinLossRatio'] = json_encode($arr['prdID_totalWinLossRatio']);
            $data1['prdID_winLossRatio'] = json_encode($arr['prdID_winLossRatio']);
            $data1['prdID_fee'] = json_encode($arr['prdID_fee']);
            $data1['prdID_deals'] = json_encode($arr['prdID_deals']);
            $data1['prdID_winRate'] = json_encode($arr['prdID_winRate']);
            $data1['prdID_trdRatio'] = json_encode($arr['prdID_trdRatio']);
            $data1['last_time'] = time();

            $s = Db::table('tz_futures_info')->where('uid='.$uid)->find();
            if($s){
                if(time() - $s['last_time'] >300){
                    dump(1);
                    $status = Db::table('tz_futures_info')->where('uid='.$uid)->update($data1); 
                }
            }else{
                dump(2);
                $status = Db::table('tz_futures_info')->insert($data1); 
            }
            // dump($data1);
        }
        $status = Db::table('tz_studio')->where($con)->update($data); 
        // dump($con);
        // dump($data);
    }

    public function save_service(){
        $con['id'] = $this->param['id'];
        $data['start_time'] = strtotime($this->param['start_time']);
        $status = Db::table('tz_studio')->where($con)->update($data);
    }


    public function detail_exl()
    {
        $id = $this->id;
        // $info = Db::table('tz_userinfo')->field("*,FROM_UNIXTIME(create_time, '%Y-%m-%d') as create_time")->where("id=$id")->find();

        $info = Db::table('tz_studio')->alias('a')->field('a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,a.id,a.uid,FROM_UNIXTIME(a.create_time, "%Y-%m-%d") AS create_time')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->where("a.id=$id")->find();



            $header = ['创建日期', '工作室名称', '工作室UID', '用户名称', '收费价格','期货账户', '开户供货公司', '类型','状态','是否禁止订阅'];
            $body   = [];
                $record['create_time']              = $info['create_time'];
                $record['studioname']            = $info['studioname'];
                $record['id']            = $info['id'];
                $record['username']            = $info['username'];
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