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
            $list = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d %H:%i:%s') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->whereLike('a.uid', "%" . $this->param['keywords'] . "%")->where("a.status!=-1")->paginate($this->webData['list_rows'], false, $pageParam);
            $this->assign('keywords', $this->param['keywords']);
        }else{
            $list = Db::table('tz_studio')->alias('a')->field("a.futures_account,a.futures_company,a.studioname,a.studiotype,a.is_sub,a.status,a.price,b.username,LPAD(a.id,6,'0') as id,LPAD(a.uid,6,'0') as uid,FROM_UNIXTIME(a.create_time, '%Y-%m-%d %H:%i:%s') AS create_time")->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->where("a.status!=-1")->paginate($this->webData['list_rows'], false, $pageParam);
            
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
        $con['id'] = $this->param['id'];
        $this->save_info($con['id']);
        $data['start_time'] = strtotime($this->param['start_time']);
        $status = Db::table('tz_studio')->where($con)->update($data);
    }


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

            //综合积分
            $score = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/score.txt");
            $a = parse_ini_string($score);

            $data['score_json'] = json_encode($a);
            $data['score'] = end($a);

            //每日净值
            $netValue = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/netValue.txt");
            $b = parse_ini_string($netValue);
            $data['netValue'] = end($b);

            //年化收益率（string）
            $mulProfitRatioPerYear = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/mulProfitRatioPerYear.txt");
            $c = parse_ini_string($mulProfitRatioPerYear);
            $data['mulProfitRatioPerYear'] = end($c);

            //胜率
            $winRate = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winRate.txt");
            $d = parse_ini_string($winRate);
            $data['winRate'] = end($d);

            //最大回撤率
            $maxReduceRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/maxReduceRatio.txt");
            $e = parse_ini_string($maxReduceRatio);
            $data['maxReduceRatio'] = end($e);

            //截止每日盈亏比
            $winLossRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/winLossRatio.txt");
            $f = parse_ini_string($winLossRatio);
            $data['winLossRatio'] = end($f);

            //截止每日夏普比率
            $sharpRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/sharpRatio.txt");
            $g = parse_ini_string($sharpRatio);
            $data['sharpRatio'] = end($g);

            //截止每日夏普比率
            $sharpRatio = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/sharpRatio.txt");
            $g = parse_ini_string($sharpRatio);
            $data['sharpRatio'] = end($g);

            //每日初始资金、每日出入金、每日净利润、每日净利率、每日手续费、每日交易次数、每日成交额
            $day = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/day.txt");
            // dump($day);
            $h = parse_ini_string($day,true);
            // 净利润
            $data['netProfit'] = end($h['netProfit']);
            // 资金规模（每日初始资金）
            $data['initialFund'] = end($h['initialFund']);

           // //截止每日各品种胜率
           //  $prdID_winRate = file_get_contents("http://49.235.36.29/accountPerformance/".$BrokerId."_" .$uid."/prdID_winRate.txt");
           //  $i = parse_ini_string($prdID_winRate);
           //  $data['prdID_winRate'] = end($i);
           //  
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