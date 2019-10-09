<?php
/**
 * 
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;
//品种管理
use think\Db;
class Varieties extends Base
{
    public function index()
    {
        $pageParam = ['query' => []];
        $list = Db::table('tz_varieties')->select();
        $arr = $this->arrtree($list);
        $this->assign([
            'arr'      => $arr,
            'list'      => $list,
        ]);
        return $this->fetch();
    }


    public function add(){
         $list = Db::table('tz_varieties')->select();
         $arr = $this->arrtree($list);

        if ($this->request->isPost()) {
            $param = $this->param;
            if(!$param['v_name'] || !$param['code']){
                return $this->error('输入完整信息');
            }
            $param['create_time'] = date('Y-m-d H:i:s');

            $status = Db::table('tz_varieties')->insert($param);
            if($status){
                return $this->success();
            }else{
                return $this->error();
            }
        }
        $this->assign('arr',$arr);
        return $this->fetch();
    }


        //树状图
        public function arrtree($list, $id = 'id', $pid = 'pid', $son = 'sub')
        {
            $tree = $map = array();
            foreach ($list as $item) {
                $map[$item[$id]] = $item;
            }
            foreach ($list as $item) {
                if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                    $map[$item[$pid]][$son][] = &$map[$item[$id]];
                } else {
                    $tree[] = &$map[$item[$id]];
                }
            }
            unset($map);
            return $tree;
        }

    //修改
    public function edit()
    {
         $list = Db::table('tz_varieties')->select();
         $arr = $this->arrtree($list);

        $info = Db::table('tz_varieties')->where("id=".$this->id)->find();
        if (!$info) {
            return $this->error('用户不存在');
        }

        if ($this->request->isPost()) {
            $param = $this->param;
            if(!$param['v_name'] || !$param['code']){
                return $this->error('输入完整信息');
            }
            $param['create_time'] = date('Y-m-d H:i:s');

            $status = Db::table('tz_varieties')->where("id=".$this->id)->update($param);
            if($status){
                return $this->success();
            }else{
                return $this->error();
            }
        }

       $info = Db::table('tz_varieties')->where("id=".$this->id)->find();
        $this->assign([
            'info'  => $info
        ]);
        $this->assign('arr',$arr);
        return $this->fetch('add');
    }


    public function del()
    {

        $id = $this->id;

        if(!$id){
            return $this->error('不存在');
        }
        $result = Db::table('tz_ticket')->where('id='.$id)->delete();

        if ($result) {
            return $this->deleteSuccess();
        }
        return $this->error('删除失败');
    }

}