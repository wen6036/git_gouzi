<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;
use app\admin\model\Studios;
use think\Db;
//订阅管理
class Advance extends Base
{
    public function index()
    {
        $model = new Studios();
        $this->showDataHeaderAddButton = false;
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];
        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('studioname', "%" . $this->param['keywords'] . "%");
            $this->assign('keywords', $this->param['keywords']);
        }


        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['ID', '工作室名称', '综合积分', '净值','其他'];
            $body   = [];
            $data   = $model->select();
            foreach ($data as $item) {
                $record                    = [];
                $record['id']              = $item->id;
                $record['studioname']            = $item->studioname;
                $body[]                    = $record;
            }
            return $this->export($header, $body, "User-" . date('Y-m-d-H-i-s'), '2007');
        }

        $list = $model->paginate($this->webData['list_rows'], false, $pageParam);
        // dump($list->toArray());
        $this->assign([
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }


}