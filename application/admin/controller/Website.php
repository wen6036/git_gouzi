<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Banner;
use think\Request;
use think\Db;
use app\admin\model\Webpage;
// 网站用户协议
class Website extends Base
{
    public function index(){
        $Banner           = new Banner;
        $arr = $Banner->where("type=1")->select();
        // dump($arr);
        $this->assign('list',$arr);

        return $this->fetch();
    }
    public function addbanner(){
        $param = $this->request->param();
        $imgurl = $param['imgurl'];
        $Banner           = new Banner;
        $Banner->imgurl     = basepic($imgurl,'Banner');
        $Banner->type     = $param['type'];
        // $Banner->save();
        // 获取自增ID
        // echo $Banner->id;
        if($Banner->save()){
            return $Banner->id;
        }else{  
            return false;
        }
    }

    public function delbanner($id){
        $id     = $this->id;
        $result = Banner::destroy(function ($query) use ($id) {
            $query->whereIn('id', $id);
        });
        if ($result) {
            return json(['code'=>1]);
        }else{
            return json(['code'=>0]);
        }

    }
    // 首页
    public function webindex(){
        $model = new Webpage();
        $page_param = ['query' => []];

        $list = $model
            ->where("type=1")
            ->paginate($this->webData['list_rows'], false, $page_param);

        $this->assign([
            'list' => $list,
            'page'  => $list->render(),
            'total' => $list->total()
        ]);

        return $this->fetch();
    }

    // 数据排名订阅/展示区
    public function datashow(){
        
        $Banner           = new Banner;
        $arr = $Banner->where("type=2")->select();
        // dump($arr);
        $this->assign('list',$arr);
        return $this->fetch();
    }
    // 服务内容
    public function service_content(){
        $model = new Webpage();
        $page_param = ['query' => []];

        $list = $model
            ->where("type=2")
            ->paginate($this->webData['list_rows'], false, $page_param);

        $this->assign([
            'list' => $list,
            'page'  => $list->render(),
            'total' => $list->total()
        ]);

        return $this->fetch();        
    }
    // 学研中心
    public function research_center(){
        $model = new Webpage();
        $page_param = ['query' => []];

        $list = $model
            ->where("type=3")
            ->paginate($this->webData['list_rows'], false, $page_param);

        $this->assign([
            'list' => $list,
            'page'  => $list->render(),
            'total' => $list->total()
        ]);

        return $this->fetch();        
    }
    // 新手指南
    public function guide(){
        $model = new Webpage();
        $page_param = ['query' => []];

        $list = $model
            ->where("type=4")
            ->paginate($this->webData['list_rows'], false, $page_param);

        $this->assign([
            'list' => $list,
            'page'  => $list->render(),
            'total' => $list->total()
        ]);

        return $this->fetch();        
    }

    // 客服中心
    public function customer(){
        $model = new Webpage();
        $page_param = ['query' => []];

        $list = $model
            ->where("type=5")
            ->paginate($this->webData['list_rows'], false, $page_param);

        $this->assign([
            'list' => $list,
            'page'  => $list->render(),
            'total' => $list->total()
        ]);

        return $this->fetch();        
    }
        // 关于
    public function about(){
        $model = new Webpage();
        $page_param = ['query' => []];

        $list = $model
            ->where("type=6")
            ->paginate($this->webData['list_rows'], false, $page_param);

        $this->assign([
            'list' => $list,
            'page'  => $list->render(),
            'total' => $list->total()
        ]);

        return $this->fetch();        
    }
    // 客户服务热线
    public function contact(){
        if ($this->request->isPost()) {

            $param = $this->request->param();
            $image_url = $param['wx_qrcode'];

            if(strlen($image_url)<200){
                $data['wx_qrcode'] = $image_url;
            }else{
                $data['wx_qrcode'] = basepic($image_url);
            }
            $data['tel'] = $param['tel'];
            $data['start_week'] = $param['start_week'];
            $data['end_week'] = $param['end_week'];
            $data['start_time'] = $param['start_time'];
            $data['end_time'] = $param['end_time'];
            $data['wechat'] = $param['wechat'];
            $data['address'] = $param['address'];
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

    public function personnel(){
        // $info = Webpage::get($this->id);
        if ($this->request->isPost()) {
            $this->param['create_time'] = time();
            $this->param['webcontent'] = $_POST['editorValue'];
            if (Db::table('tz_webpage')->where("id=34")->update($this->param)) {
                return $this->success('操作成功','admin/website/personnel');
            }
            return $this->error();
        }

        $info = Db::table('tz_webpage')->where("id=34")->find();
        $this->assign([
            'info'       => $info,
        ]);

        return $this->fetch();
    }
    // 内容編輯頁面
    public  function web_content(){
        
        $info = Webpage::get($this->id);
        if ($this->request->isPost()) {
            $this->param['create_time'] = time();
            $this->param['webcontent'] = $_POST['editorValue'];
            if (false !== $info->save($this->param)) {
                return $this->success();
            }
            return $this->error();
        }
        if(!$info){
            
            return $this->error();
        }
        $this->assign([
            'info'       => $info,
        ]);

        return $this->fetch();
    }
}