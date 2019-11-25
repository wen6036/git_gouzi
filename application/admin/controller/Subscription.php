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
        // $pageParam = ['query' => []];
        $keywords = isset($this->param['keywords'])?$this->param['keywords']:'';

        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['订阅日期', '用户名称', '用户UID', '订阅工作室名称','工作室UID','订阅期限', '订阅状态', '订阅费','到期时间', '支付方式','支付时间','支付金额','支付订单号'];
            $body   = [];


            $data = Db::table('tz_suborder')->alias('a')->field('a.*,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time,FROM_UNIXTIME(a.end_time, "%Y-%m-%d") AS end_time,b.id as uid,b.username,c.studioname,c.price')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->join(['tz_studio'=>'c'],'a.studio_id = c.id','left')->whereLike('c.studioname', "%" . $keywords . "%")->select();
            foreach ($data as $item) {
                $record                    = [];

                if($item['status']==1){
                    $text= '有效';
                }else{
                    $text= '已取消';
                }

                if($item['paytype']==1){
                    $text1= '微信';
                }elseif ($item['paytype']==2) {
                    $text1= '支付宝';
                }else{
                    $text1= '体验券';
                }
                $record['create_time']  = $item['create_time'];
                $record['username']  = $item['username'];
                $record['uid']  = $item['uid'];
                $record['studioname']  = $item['studioname'];
                $record['studio_id']  = $item['studio_id'];
                $record['order_time']  = $item['order_time'];
                $record['status']  = $text;
                $record['price']  = $item['price'];
                $record['end_time']  = $item['end_time'];
                $record['paytype']  = $text1;
                $record['create_time1']  = $item['create_time'];
                $record['pay_money']  = $item['pay_money'];
                $record['ordernum']  = '';
                $body[]                    = $record;
            }
            return $this->export($header, $body, "订阅管理-" . date('Y-m-d-H-i-s'), '2007');
        }
        $list = Db::table('tz_suborder')->alias('a')->field('a.*,FROM_UNIXTIME(a.create_time, "%Y-%m-%d %H:%i:%s") AS create_time,FROM_UNIXTIME(a.end_time, "%Y-%m-%d") AS end_time,b.id as uid,b.username,c.studioname,c.price')->join(['tz_userinfo'=>'b'],'a.uid=b.id','left')->join(['tz_studio'=>'c'],'a.studio_id = c.id','left')->where("c.studioname like '%$keywords%' or c.id like '%$keywords%' or b.id like '%$keywords%' or b.username like '%$keywords%'")->where("a.status > 0")->paginate($this->webData['list_rows']);
        // echo  Db::table('tz_suborder')->alias('a')->getLastSql();
        $this->assign('keywords',$keywords);
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

             $header = ['订阅日期', '用户名称', '用户UID', '订阅工作室名称','工作室UID','订阅期限', '订阅状态', '订阅费','到期时间', '支付方式','支付时间','支付金额','支付订单号'];
            $body   = [];
                if($info['status']==1){
                    $text= '有效';
                }else{
                    $text= '已取消';
                }

                if($info['paytype']==1){
                    $text1= '微信';
                }elseif ($info['paytype']==2) {
                    $text1= '支付宝';
                }else{
                    $text1= '体验券';
                }

                 $record['create_time']  = $info['create_time'];
                 $record['username']  = $info['username'];
                 $record['uid']  = $info['uid'];
                 $record['studioname']  = $info['studioname'];
                 $record['studio_id']  = $info['studio_id'];
                 $record['order_time']  = $info['order_time'];
                 $record['status']  = $text;
                 $record['price']  = $info['price'];
                 $record['end_time']  = $info['end_time'];
                 $record['paytype']  = $text1;
                 $record['create_time1']  = $info['create_time'];
                 $record['pay_money']  = $info['pay_money'];
                 $record['ordernum']  = '';

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

    public function cancel(){
        $id = $this->id;
        Db::table('tz_suborder')->startTrans();
        $info = Db::table('tz_suborder')->where("id=$id")->find();
        if(time() > $info['end_time']){
            return $this->error('已过期 无法取消');
        }

        $status = Db::table('tz_suborder')->where("id=$id")->update(['status'=>2]);
        $con['status'] = 0;
        $con['studio_id'] = $info['studio_id'];
        $res = Db::table('tz_subtotle_status')->where($con)->setDec('order_totle',$info['pay_money']);
        if($status && $res){
            Db::commit();
            return $this->success();
        }else{
            Db::rollback();;
            return $this->error('操作失败');
        }

        // dump($id);
        // $con['status'] = -1;
        // $status = Db::table('tz_suborder')->where("id=$id")->update($con);
        // if ($status) {
        //     return $this->deleteSuccess();
        // }
        // return $this->error('删除失败');
    }


}