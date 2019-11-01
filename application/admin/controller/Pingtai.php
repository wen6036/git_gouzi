<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;
use app\common\model\Attachments;
use think\Db;
//订阅管理
class Pingtai extends Base
{
    public function menu()
    {
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];
        $list = Db::table('tz_menu')->paginate($this->webData['list_rows'], false, $pageParam); 
        // dump($list->toArray());
        // dump($list);
        $this->assign([
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();
    }


    public function add(){
        if ($this->request->isPost()) {
            $param = $this->param;

            $attachment              = new Attachments();
            $file                    = $attachment->upload('headimg');
            if ($file) {
                $this->param['headimg'] = $file->url;
            }else{
                return $this->error($attachment->getError());
            }

            $status = Db::table('tz_menu')->insert($param);
            if($status){
                return $this->success();
            }else{
                return $this->error();
            }
        }
        return $this->fetch();
    }
    public function edit(){
        if ($this->request->isPost()) {
            $param = $this->param;

            if ($this->request->file('headimg')) {
                $attachment = new Attachments();
                $file       = $attachment->upload('headimg');
                if ($file) {
                    $param['headimg'] = $file->url;
                } else {
                    return $this->error($attachment->getError());
                }
            }


            $status = Db::table('tz_menu')->where("id=".$this->id)->update($param);
            if($status){
                return $this->success('操作成功','admin/pingtai/menu');
            }else{
                return $this->error();
            }
        }


        $info = Db::table('tz_menu')->where("id=".$this->id)->find();
        $this->assign('info',$info);
        return $this->fetch('add');
    }


    public function disable()
    {


        $id = $this->id;

        if(!$id){
            return $this->error('不存在');
        }
        $result = Db::table('tz_menu')->where('id ='.$id)->update(['status'=>-1]);


        if ($result) {
            return $this->success();
        }else{
            $result = Db::table('tz_menu')->where('id ='.$id)->update(['status'=>1]);
            return $this->success();

        }
    }

    public function link(){
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];
        $list = Db::table('tz_link')->paginate(30, false, $pageParam); 
        // dump($list->toArray());
        // dump($list);
        $this->assign([
            'list'      => $list,
            'total'     => $list->total(),
            'page'      => $list->render()
        ]);
        return $this->fetch();

        $this->fetch();
    }

    public function add_link(){
        if ($this->request->isPost()) {
            $param = $this->param;

            $status = Db::table('tz_link')->insert($param);
            if($status){
                return $this->success('操作成功','admin/pingtai/link');
            }else{
                return $this->error();
            }
        }
        return $this->fetch();
    }


    public function edit_link(){
        if ($this->request->isPost()) {
            $param = $this->param;

            $status = Db::table('tz_link')->where("id=".$this->id)->update($param);
            if($status){
                return $this->success('操作成功','admin/pingtai/link');
            }else{
                return $this->error();
            }
        }


        $info = Db::table('tz_link')->where("id=".$this->id)->find();
        $this->assign('info',$info);
        return $this->fetch('add_link');
    }


    public function disable_link()
    {
        $id = $this->id;
        if(!$id){
            return $this->error('不存在');
        }
        $result = Db::table('tz_link')->where('id ='.$id)->update(['status'=>-1]);


        if ($result) {
            return $this->success();
        }else{
            $result = Db::table('tz_link')->where('id ='.$id)->update(['status'=>1]);
            return $this->success();

        }
    }

    public function webinfo(){
        if ($this->request->isPost()) {

            $param = $this->request->param();
            $image_url = $param['logo'];

            if(strlen($image_url)<200){
                $data['logo'] = $image_url;
            }else{
                $data['logo'] = basepic($image_url);
            }
            $data['copyright'] = $param['copyright'];
            $data['sub_title'] = $param['sub_title'];

            $con['id'] = $param['id'];
            if($con['id']){
                $status = Db::table('tz_company')->where($con)->update($data);
            }else{
                $status = Db::table('tz_company')->insert($data);
            }
            if($status){
                return json(['code'=>1,'msg'=>'修改成功']);
            }else{
                return json(['code'=>0,'msg'=>'修改失败']);
            }
        }
        $info = Db::table('tz_company')->where("id=1")->find();
        if($info){
            $this->assign('info',$info);
        }
        return $this->fetch();
    }

}