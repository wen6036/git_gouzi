<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;

// use app\common\model\Attachments;
use app\admin\model\UserInfo;
use think\Db;
class User extends Base
{
    public function index()
    {
        $model = new UserInfo();

        $pagenum = isset($this->param['page'])?$this->param['page']:1;
        $this->showDataHeaderAddButton = false;
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];
        if (isset($this->param['keywords']) && !empty($this->param['keywords'])) {
            $pageParam['query']['keywords'] = $this->param['keywords'];
            $model->whereLike('username|id|email|usertel', "%" . $this->param['keywords'] . "%");
            $this->assign('keywords', $this->param['keywords']);
        }
        if (isset($this->param['_order_']) && !empty($this->param['_order_'])) {
            $pageParam['query']['_order_'] = $this->param['_order_'];
            $order                         = $this->param['_order_'];
            switch ($order) {
                case 'id':
                    $order = 'id';
                    break;
                case 'username':
                    $order = 'username';
                    break;
                case 'create_time':
                    $order = 'create_time';
                    break;
                case 'last_login_time':
                    $order = 'last_login_time';
                    break;
                default:
                    $order = 'id';
            }
            $by = isset($this->param['_by_']) && !empty($this->param['_by_']) ? $this->param['_by_'] : 'desc';
            $model->order($order, $by);
            $this->assign('_order_', $this->param['_order_']);
            $this->assign('_by_', $this->param['_by_']);
        } else {
            $model->order('id', 'desc');
        }

        if (isset($this->param['export_data']) && $this->param['export_data'] == 1) {
            $header = ['用户名称','用户UID', '手机号', '邮箱', '昵称', '微信号','姓名', '身份证号', '注册时间','最后登录时间','性别','其他电话','地址','开户人1','银行卡号','开户银行','开户人2','银行卡号','开户银行'];
            $body   = [];
            $data   = $model->select();
            foreach ($data as $item) {
                $record                    = [];
                $record['username']        = $item->username;
                $record['id']              = $item->id;
                $record['usertel']         = $item->usertel;
                $record['email']           = $item->email;
                $record['nickname']           = $item->nickname;
                $record['wx_num']          = $item->wx_num;
                $record['truename']          = $item->truename;
                $record['shenfenzheng']          = $item->shenfenzheng;
                $record['create_time']        = $item->create_time;
                $record['last_login_time'] = $item->last_login_time;
                $record['sex']          = $item->sex==1?'男':'女';
                $record['other_phone']          = $item->other_phone;
                $record['address_detail']          = $item->address_detail;
                if($item->bankinfo){
                    foreach (json_decode($item->bankinfo) as $k => $v) {
                        $record['kaihuren'.$k]          = $v[0];
                        $record['yinhangkai'.$k]          = $v[1];
                        $record['kaihubank'.$k]          = $v[2];
                    }
                }

                $body[]                    = $record;
            }
            return $this->export($header, $body, "用户-" . date('Y-m-d-H-i-s'), '2007');
        }
        // $this->webData['list_rows'] = 2;
        $list = $model->where("status != -1")->paginate($this->webData['list_rows'], false, $pageParam);

        $startnumber = $this->webData['list_rows'] * ($pagenum-1);
        $this->assign([
            'startnumber'   => $startnumber,
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }


    // public function add()
    // {
    //     if ($this->request->isPost()) {
    //         $resultValidate = $this->validate($this->param, 'User.admin_add');
    //         if (true !== $resultValidate) {
    //             return $this->error($resultValidate);
    //         }
    //         $this->param['password'] = md5(md5($this->param['password']));
    //         $attachment              = new Attachments();
    //         $file                    = $attachment->upload('headimg');
    //         if ($file) {
    //             $this->param['headimg'] = $file->url;
    //         }else{
    //             return $this->error($attachment->getError());
    //         }

    //         $result = UserInfo::create($this->param);
    //         if ($result) {
    //             return $this->success();
    //         }
    //         return $this->error();
    //     }
    //     $this->assign([
    //         'user_level' => UserLevels::all(),
    //     ]);
    //     return $this->fetch();
    // }


    // public function edit()
    // {
    //     $info = UserInfo::get($this->id);
    //     if ($this->request->isPost()) {
    //         $resultValidate = $this->validate($this->param, 'User.admin_edit');
    //         if (true !== $resultValidate) {
    //             return $this->error($resultValidate);
    //         }

    //         if ($this->request->file('headimg')) {
    //             $attachment = new Attachments();
    //             $file       = $attachment->upload('headimg');
    //             if ($file) {
    //                 $this->param['headimg'] = $file->url;
    //             } else {
    //                 return $this->error($attachment->getError());
    //             }
    //         }

    //         if (isset($this->param['password'])) {
    //             if(!empty($this->param['password'])){
    //                 $this->param['password'] = md5(md5($this->param['password']));
    //             }else{
    //                 unset($this->param['password']);
    //             }

    //         }

    //         if (false !== $info->save($this->param)) {
    //             return $this->success();
    //         }
    //         return $this->error();
    //     }

    //     $this->assign([
    //         'info'       => $info,
    //         'user_level' => UserLevels::all(),
    //     ]);
    //     return $this->fetch('add');
    // }


    public function detail()
    {
        $id = $this->id;
        $info = Db::table('tz_userinfo')->field("*,FROM_UNIXTIME(create_time, '%Y-%m-%d') as create_time,FROM_UNIXTIME(last_login_time, '%Y-%m-%d') as last_login_time")->where("id=$id")->find();
        $info['bankinfo'] = json_decode($info['bankinfo']);
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function detail_exl()
    {
        // $this->showLeftMenu = false;
        $id = $this->id;
        $info = Db::table('tz_userinfo')->field("*,FROM_UNIXTIME(create_time, '%Y-%m-%d') as create_time,FROM_UNIXTIME(last_login_time, '%Y-%m-%d') as last_login_time")->where("id=$id")->find();


        $bankinfo = json_decode($info['bankinfo']);

            $header = ['用户名称', '用户UID','手机', '昵称',  '邮箱','注册时间', '最后登录时间', '其他电话','地址','真实姓名','身份证','性别','微信','银行卡姓名1','银行卡1','所属银行1','银行卡姓名2','银行卡2','所属银行2'];
            $body   = [];

                $record                    = [];
                $record['username']            = $info['username'];
                $record['id']              = $info['id'];
                $record['usertel']          = $info['usertel'];
                $record['nickname']        = $info['nickname'];
                $record['email']           = $info['email'];
                $record['create_time']        = $info['create_time'];
                $record['last_login_time'] = $info['last_login_time'];
                $record['other_phone']          = $info['other_phone'];
                $record['address_detail']          = $info['address_detail'];
                $record['truename']          = $info['truename'];
                $record['shenfenzheng']          = $info['shenfenzheng'];
                $record['sex']          = $info['sex']==1?'男':'女';
                $record['wx_num']          = $info['wx_num'];
                if($bankinfo){
                    foreach ($bankinfo as $key => $value) {
                        $record['bankusername'.$key]          = $value[0];
                        $record['banknum'.$key]          = $value[1];
                        $record['bankname'.$key]          = $value[2];
                    }                   
                }
                $body[]                    = $record;
            return $this->export($header, $body, $info['username']. date('Y-m-d-H-i-s'), '2007');
    }



    public function del()
    {

        $id     = $this->id;
        $con['status']= -1;
        $con['delete_time'] = time();
        $result = Db::table('tz_userinfo')->whereIn('id', $id)->update($con);
        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
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

}