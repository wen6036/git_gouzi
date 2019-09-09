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
            $pageParam['query']['keywords'] = $this->param['keywords'];
            // dump($this->param['keywords']);
            $this->assign('keywords', $this->param['keywords']);
            $list = Db::table('tz_studio')->alias('a')->field('a.*,b.score,b.mulNetValue,b.netProfit,b.efficiency,b.deals,b.winRate,b.maxReduceRatio')->join(['tz_futures_info'=>'b'],'a.id=b.studio_id','left')->whereLike('a.studioname', "%" . $this->param['keywords'] . "%")->where('a.studiotype=1')->paginate($this->webData['list_rows']);
            // dump( Db::table('tz_studio')->getLastSql());
        }else{

            $list = Db::table('tz_studio')->alias('a')->field('a.*,b.score,b.mulNetValue,b.netProfit,b.efficiency,b.deals,b.winRate,b.maxReduceRatio')->join(['tz_futures_info'=>'b'],'a.id=b.studio_id','left')->where('a.studiotype=1')->paginate($this->webData['list_rows'], false, $pageParam);

        }

        // dump($list);

        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['ID', '工作室名称', '综合积分', '净值','净利润','盈亏效率','交易次数','胜率','最大回撤率'];
            $body   = [];
            $data   = $list;
            foreach ($data as $item) {
                $record                    = [];
                $record['id']              = $item['id'];
                $record['studioname']            = $item['studioname'];
                $record['score']            = $item['score'];
                $record['mulNetValue']            = $item['mulNetValue'];
                $record['netProfit']            = $item['netProfit'];
                $record['efficiency']            = $item['efficiency'];
                $record['deals']            = $item['deals'];
                $record['winRate']            = $item['winRate'];
                $record['maxReduceRatio']            = $item['maxReduceRatio'];
                $body[]                    = $record;
            }
            return $this->export($header, $body, "User-" . date('Y-m-d-H-i-s'), '2007');
        }
        // $list = $model->paginate($this->webData['list_rows'], false, $pageParam);
        $this->assign([
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