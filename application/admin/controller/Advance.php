<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;
use think\Db;
//订阅管理
class Advance extends Base
{
    public function index()
    {
        $this->showDataHeaderAddButton = false;
        $this->showDataHeaderDeleteButton = false;
        // $pageParam = ['query' => []];
        // if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
        //     $pageParam['query']['keywords'] = $this->param['keywords'];
        //     $model->whereLike('studioname', "%" . $this->param['keywords'] . "%");
        //     $this->assign('keywords', $this->param['keywords']);
        // }
        $info = Db::table('tz_user_bank')->field('distinct(uid),username,banknum,bankname')->select();
        $temp_key = array_column($info,'uid');  //键值
        $Newarr= array_combine($temp_key,$info);

        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['工作室名称','工作室id','用户名称', '用户UID', '手机号','银行开户人','银行卡卡号','开户行','订阅次数','订阅费','状态','最后操作日期'];
            $body   = [];
            $data   = Db::table('tz_subtotle_status')->alias('a')->distinct(true)->field('a.*,FROM_UNIXTIME(a.last_time, "%Y-%m-%d") AS last_time,b.id studio_id,b.price,b.studioname,c.id uid,c.username,c.usertel')->join(['tz_studio'=>'b'],'a.studio_id = b.id','left')->join(['tz_userinfo'=>'c'],'b.uid = c.id','left')->select();
            foreach ($data as $item) {
                if($item['status'] == 0){
                    $a = '不可提现';
                }elseif ($item['status'] == 1) {
                    $a = '可提现';
                }elseif ($item['status'] == 2) {
                    $a = '可提现';
                }
                $record                    = [];
                $record['id']              = $item['studioname'];
                $record['studio_id']              = $item['studio_id'];
                $record['username']              = $item['username'];
                $record['uid']              = $item['uid'];
                $record['usertel']              = $item['usertel'];
                $record['username2']              = $Newarr[$item['uid']]['username'];
                $record['banknum']              = $Newarr[$item['uid']]['banknum'];
                $record['bankname']              = $Newarr[$item['uid']]['bankname'];
                $record['order_num']              = $item['order_num'];
                $record['price']              = $item['price'];
                $record['status']              = $a;
                $record['last_time']              = $item['last_time'];
                $body[]                    = $record;
            }
            return $this->export($header, $body, "工作室提现管理-" . date('Y-m-d-H-i-s'), '2007');
        }

        // $list = $model->paginate($this->webData['list_rows'], false, $pageParam);
        // dump($list->toArray());
        $list = Db::table('tz_subtotle_status')->alias('a')->distinct(true)->field('a.*,FROM_UNIXTIME(a.last_time, "%Y-%m-%d") AS last_time,b.id studio_id,b.price,b.studioname,c.id uid,c.username,c.usertel')->join(['tz_studio'=>'b'],'a.studio_id = b.id','left')->join(['tz_userinfo'=>'c'],'b.uid = c.id','left')->paginate($this->webData['list_rows']);

        $this->assign('newarr',$Newarr);

        $this->assign([
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }

    public function detail_exl()
    {
            $info = Db::table('tz_user_bank')->field('distinct(uid),username,banknum,bankname')->select();
            $temp_key = array_column($info,'uid');  //键值
            $Newarr= array_combine($temp_key,$info);

            $id = $this->id;
            $info = Db::table('tz_subtotle_status')->alias('a')->distinct(true)->field('a.*,FROM_UNIXTIME(a.last_time, "%Y-%m-%d") AS last_time,b.id studio_id,b.price,b.studioname,c.id uid,c.username,c.usertel')->join(['tz_studio'=>'b'],'a.studio_id = b.id','left')->join(['tz_userinfo'=>'c'],'b.uid = c.id','left')->where("a.id = $id")->find(); 
            $header = ['工作室名称','工作室id','用户名称', '用户UID', '手机号','银行开户人','银行卡卡号','开户行','订阅次数','订阅费','状态','最后操作日期'];
            $body   = [];


                if($info['status'] == 0){
                    $a = '不可提现';
                }elseif ($info['status'] == 1) {
                    $a = '可提现';
                }elseif ($info['status'] == 2) {
                    $a = '可提现';
                }
                $record                    = [];
                $record['id']              = $info['studioname'];
                $record['studio_id']              = $info['studio_id'];
                $record['username']              = $info['username'];
                $record['uid']              = $info['uid'];
                $record['usertel']              = $info['usertel'];
                $record['username2']              = $Newarr[$info['uid']]['username'];
                $record['banknum']              = $Newarr[$info['uid']]['banknum'];
                $record['bankname']              = $Newarr[$info['uid']]['bankname'];
                $record['order_num']              = $info['order_num'];
                $record['price']              = $info['price'];
                $record['status']              = $a;
                $record['last_time']              = $info['last_time'];
                $body[]                    = $record;
            return $this->export($header, $body, $info['studioname']."工作室-" . date('Y-m-d-H-i-s'), '2007');
    }
}