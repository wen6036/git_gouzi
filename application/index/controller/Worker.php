<?php

namespace app\index\controller;
use Workerman\Lib\Timer;
use think\worker\Server;

class Worker extends Server
{
    protected $socket = 'websocket://www.51dewen.com:2345';
// 192.168.8.66
    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $connection->send('我收到你的信息了1');
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
        Timer::add(10, function()use($worker)
        {
            // 遍历当前进程所有的客户端连接，发送当前服务器的时间
            foreach($worker->connections as $connection)
            {
                $connection->send(time());
            }
        });

    }
}