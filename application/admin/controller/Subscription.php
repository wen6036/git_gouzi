<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;

// use app\common\model\Attachments;
use app\admin\model\Studios;
use think\Db;
//订阅管理
class Subscription extends Base
{
    public function index()
    {
        $this->showDataHeaderAddButton = false;
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];
        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];

            $this->assign('keywords', $this->param['keywords']);
            $list = Db::table('tz_suborder')->alias('a')->field('a.*,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time,b.id as uid,b.username,c.studioname,c.price')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->join(['tz_studio'=>'c'],'a.studio_id = c.id','left')->whereLike('c.studioname', "%" . $this->param['keywords'] . "%")->where("a.status=1")->paginate($this->webData['list_rows']);
        }else{
           $list = Db::table('tz_suborder')->alias('a')->field('a.*,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time,b.id as uid,b.username,c.studioname,c.price')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->join(['tz_studio'=>'c'],'a.studio_id = c.id','left')->where("a.status=1")->paginate($this->webData['list_rows']); 
        }


        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['订阅日期', '用户名称', '用户UID', '订阅工作室名称','工作室UID','订阅期限', '订阅状态', '订阅费', '支付方式','支付时间','支付金额'];
            $body   = [];
            $data   = $list;
            foreach ($data as $item) {
                $record                    = [];
                 $record['create_time']  = $item['create_time'];
                 $record['username']  = $item['username'];
                 $record['uid']  = $item['uid'];
                 $record['studioname']  = $item['studioname'];
                 $record['studio_id']  = $item['studio_id'];
                 $record['order_time']  = $item['order_time'];
                 $record['price']  = $item['price'];
                 $record['status']  = '有效';
                 $record['paytype']  = $item['paytype'];
                 $record['create_time1']  = $item['create_time'];
                 $record['pay_money']  = $item['pay_money'];
                $body[]                    = $record;
            }
            return $this->export($header, $body, "订阅管理-" . date('Y-m-d-H-i-s'), '2007');
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

    public function detail_exl()
    {
        $id = $this->id;


        $info = Db::table('tz_suborder')->alias('a')->field('a.*,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time,b.id as uid,b.username,c.studioname,c.price')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->join(['tz_studio'=>'c'],'a.studio_id = c.id','left')->where("a.id=$id")->find(); 

             $header = ['订阅日期', '用户名称', '用户UID', '订阅工作室名称','工作室UID','订阅期限', '订阅状态', '订阅费', '支付方式','支付时间','支付金额'];
            $body   = [];
                 $record['create_time']  = $info['create_time'];
                 $record['username']  = $info['username'];
                 $record['uid']  = $info['uid'];
                 $record['studioname']  = $info['studioname'];
                 $record['studio_id']  = $info['studio_id'];
                 $record['order_time']  = $info['order_time'];
                 $record['price']  = $info['price'];
                 $record['status']  = '有效';
                 $record['paytype']  = $info['paytype'];
                 $record['create_time1']  = $info['create_time'];
                 $record['pay_money']  = $info['pay_money'];

                $body[]                    = $record;
            return $this->export($header, $body, $info['studioname']."工作室-" . date('Y-m-d-H-i-s'), '2007');
    }

    public function del(){
        $id = $this->id;
        $con['status'] = -1;
        $status = Db::table('tz_suborder')->where("id=$id")->update($con);
        if ($status) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

}