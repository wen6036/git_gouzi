<?php

namespace app\index\controller;
use Workerman\Lib\Timer;
use think\worker\Server;
use think\Db;

//实时记录
class Workertwo extends Server
{
    protected $socket = 'websocket://www.51dewen.com:2366';
// 192.168.8.66
    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $info['msg'] = '我收到你的信息了22';
        $info['name'] = $data;
        $connection->send(json_encode($info));
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {

    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        // 定时，每10秒一次
        Timer::add(2, function()use($worker)
        {
                $time = date('Ymd');
                $list = Db::table('tz_time_info')->where("type='positions' and FROM_UNIXTIME(create_time,  '%Y%m%d' ) = $time and status=0")->select();

                $s = Db::table('tz_time_info')->where("type='positions' and FROM_UNIXTIME(create_time,  '%Y%m%d' ) = $time and status=0")->update(['status'=>1]);
                foreach ($list as $key => $value) {
                    $data[$key] = json_decode($value['info'],true);
                }

                if(count($list)>0){
                    // 遍历当前进程所有的客户端连接，发送当前服务器的时间
                    foreach($worker->connections as $connection)
                    {
                        $connection->send(json_encode($data));
                    }
                }

        });

    }
}