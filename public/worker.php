<?php
// include "Db.class.php";
include "Mysql.php";
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;
require_once __DIR__ . '/../vendor/Workerman/Workerman/Autoloader.php';
// require __DIR__ . '/../thinkphp/start.php';

$task = new Worker();
// 进程启动时异步建立一个到www.baidu.com连接对象，并发送数据获取数据
$task->onWorkerStart = function($task)
{
    // 不支持直接指定http，但是可以用tcp模拟http协议发送数据
    $connection_to_baidu = new AsyncTcpConnection('tcp://49.235.36.29:6871/?a=123');

    // 设置为ssl加密连接
    // $connection_to_baidu->transport = 'ssl';
    // 当连接建立成功时，发送http请求数据
    $connection_to_baidu->onConnect = function($connection_to_baidu)
    {
        // $C = new Mysql();
        // $data = $C->insert("connect success\n");
        // echo "connect success\n";
        $connection_to_baidu->send("login webCenter 123");
    };
    
    $connection_to_baidu->onMessage = function($connection_to_baidu, $http_buffer)
    {
		$C = new Mysql();
		$data = $C->insert($http_buffer);

        echo $http_buffer;
    };
    $connection_to_baidu->onClose = function($connection_to_baidu)
    {
        $C = new Mysql();
        $data = $C->insert("connection closed\n");
        echo "connection closed\n";
    };
    $connection_to_baidu->onError = function($connection_to_baidu, $code, $msg)
    {
        $C = new Mysql();
        $data = $C->insert("Error code:$code msg:$msg\n");

        echo "Error code:$code msg:$msg\n";
    };
    $connection_to_baidu->connect();
};

// 运行worker
Worker::runAll();