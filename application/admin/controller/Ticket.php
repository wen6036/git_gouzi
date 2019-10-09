<?php
/**
 * 用户管理
 * @author yupoxiong<i@yufuping.com>
 */

namespace app\admin\controller;
use app\admin\model\Studios;
use think\Db;
//订阅管理
class Ticket extends Base
{
    public function index()
    {
        $model = new Studios();
        // $this->showDataHeaderAddButton = true;
        $this->showDataHeaderDeleteButton = false;
        $pageParam = ['query' => []];
        $list = Db::table('tz_ticket')->field("*,FROM_UNIXTIME(datetime,'%Y年%m月%d') datetime,FROM_UNIXTIME(create_time,'%Y年%m月%d') create_time")->paginate($this->webData['list_rows'], false, $pageParam); 
        // dump($list->toArray());
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
            if(!$param['number'] || !$param['datetime']){
                return $this->error('输入完整信息');
            }
            $param['datetime'] = strtotime($param['datetime']);
            $param['create_time'] = time();

            $status = Db::table('tz_ticket')->insert($param);
            if($status){
                return $this->success();
            }else{
                return $this->error();
            }
        }
        return $this->fetch();
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