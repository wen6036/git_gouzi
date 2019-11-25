<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;

// use app\common\model\Attachments;
use think\Db;
class Studio extends Base
{
    public function index()
    {
        $this->showDataHeaderAddButton = false;
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];

        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            // $pageParam['query']['keywords'] = $this->param['keywords'];
            $keywords = $this->param['keywords'];
            $this->assign('keywords', $this->param['keywords']);
        }else{
            $keywords ='';

        }

        $list = Db::table('tz_studio')->alias('a')->field('a.*,b.score,b.netValue,b.netProfit,b.efficiency,b.deals,b.winRate,b.dealDays,b.maxReduceRatio,b.kamaRatio,b.sharpRatio,b.equity,b.mulProfitRatioPerYear,b.winLossRatio,b.mulProfitRatio,b.initialFund')->join(['tz_futures_info'=>'b'],'a.id=b.studio_id','left')->where("a.studioname like '%$keywords%' or a.id like '%$keywords%'")->where('a.status=1 and a.studiotype=1')->paginate($this->webData['list_rows']);


        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {


             $list = Db::table('tz_studio')->alias('a')->field('a.*,b.score,b.netValue,b.netProfit,b.efficiency,b.deals,b.winRate,b.dealDays,b.maxReduceRatio,b.kamaRatio,b.sharpRatio,b.equity,b.mulProfitRatioPerYear,b.winLossRatio,b.mulProfitRatio,b.initialFund')->join(['tz_futures_info'=>'b'],'a.id=b.studio_id','left')->where("a.studioname like '%$keywords%' or a.id like '%$keywords%'")->where('a.status=1 and a.studiotype=1')->select();

            $header = ['ID', '工作室名称', '综合积分', '净值','年化收益率','胜率','最大回撤率','盈亏比','夏普比率','盈亏效率','累计收益率','净利润','资金规模'];
            // dump($header);exit;
            $body   = [];
            $data   = $list;
            foreach ($data as $item) {
                $record                    = [];
                $record['id']              = $item['id'];
                $record['studioname']            = $item['studioname'];

                $record['score']            = $item['score'];
                $record['netValue']            = $item['netValue'];
                $record['mulProfitRatioPerYear']            = $item['mulProfitRatioPerYear'];
                $record['winRate']            = $item['winRate'];
                $record['maxReduceRatio']            = $item['maxReduceRatio'];
                $record['winLossRatio']            = $item['winLossRatio'];
                $record['sharpRatio']            = $item['sharpRatio'];
                $record['efficiency']            = $item['efficiency'];
                $record['mulProfitRatio']            = $item['mulProfitRatio'];
                $record['netProfit']            = $item['netProfit'];
                $record['initialFund']            = $item['initialFund'];
                // $record['deals']            = $item['deals'];
                $body[]                    = $record;

            }
            return $this->export($header, $body, "订阅区-" . date('Y-m-d-H-i-s'), '2007');
        }
        // $list = $model->paginate($this->webData['list_rows'], false, $pageParam);
        $pagenum = isset($this->param['page'])?$this->param['page']:1;
        $startnumber = $this->webData['list_rows'] * ($pagenum-1);

        $this->assign([
            'startnumber'   => $startnumber,
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }

    public function rangking()
    {
        $con['ranking'] = 0;
        $status = Db::table('tz_studio')->where('id='.$this->id)->update($con);
        // dump($this->id);
        if($status){
            return $this->success();
        }
        return $this->error();
    }

    public function rangk()
    {
        $con['ranking'] = 1;
        $status = Db::table('tz_studio')->where('id='.$this->id)->update($con);
        // dump($this->id);
        if($status){
            return $this->success();
        }
        return $this->error();
    }

}