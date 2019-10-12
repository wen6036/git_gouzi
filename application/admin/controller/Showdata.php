<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;

// use app\common\model\Attachments;
use app\admin\model\Studios;
use think\Db;
class Showdata extends Base
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
            $list = Db::table('tz_studio')->alias('a')->field('a.*,b.score,b.netValue,b.netProfit,b.efficiency,b.lot,b.winRate,b.dealDays,b.maxReduceRatio,b.kamaRatio,b.sharpRatio,b.equity')->join(['tz_futures_info'=>'b'],'a.id=b.studio_id','left')->whereLike('a.studioname', "%" . $this->param['keywords'] . "%")->where('a.studiotype=2')->paginate($this->webData['list_rows']);
            // dump( Db::table('tz_studio')->getLastSql());
        }else{
            $list = Db::table('tz_studio')->alias('a')->field('a.*,b.score,b.netValue,b.netProfit,b.efficiency,b.lot,b.winRate,b.dealDays,b.maxReduceRatio,b.kamaRatio,b.sharpRatio,b.equity')->join(['tz_futures_info'=>'b'],'a.id=b.studio_id','left')->where('a.studiotype=2')->paginate($this->webData['list_rows'], false, $pageParam);
        }

        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['ID', '工作室名称', '综合积分', '净值','其他'];
            $body   = [];
            $data   = $list;
            foreach ($data as $item) {
                $record                    = [];
                $record['id']              = $item['id'];
                $record['studioname']            = $item['studioname'];
                $record['score']            = $item['score'];
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