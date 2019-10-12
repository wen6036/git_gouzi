<?php
/**
 * @author yupoxiong<i@yufuping.com>
 * 前台基础控制器
 */
namespace app\index\controller;
// use Workerman\Worker;
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;
use \Workerman\Autoloader;
use think\Db;
class Test extends Controller
{
	public function index(){
		$this->assign('title','test');
		dump(123);

		$list = Db::table('tz_varieties')->select();
		foreach ($list as $key => $value) {
			$arr[$value['code']] = $value['v_name'];
		}
		dump($arr);
		return $this->fetch();
	}

// $aa='0#tradeCenter#newTrade#{
//   "name": "6050_81331531",
//   "brokerID": "6050",
//   "userID": "81331531",
//   "tradeID": "[     1690862]",
//   "date": "20191010",
//   "time": "10:51:45",
//   "insID": "ni1911",
//   "BS": "S",
//   "OC": "C",
//   "lot": 1,
//   "price": 137020.0,
//   "profit": 28.0,
//   "fee": 12.0,
//   "note": ""
// }~';
	public function task($task){
	    // 不支持直接指定http，但是可以用tcp模拟http协议发送数据
	    // $connection_to_baidu = new AsyncTcpConnection('tcp://49.235.36.29:6871');
	    $connection_to_baidu = new AsyncTcpConnection('tcp://www.baidu.com:80');
	    // 当连接建立成功时，发送http请求数据
	    $connection_to_baidu->onConnect = function($connection_to_baidu)
	    {
	        echo "connect success\n";
	        $connection_to_baidu->send("GET / HTTP/1.1\r\nHost: www.baidu.com\r\nConnection: keep-alive\r\n\r\n");
	    };
	    $connection_to_baidu->onMessage = function($connection_to_baidu, $http_buffer)
	    {
	        echo $http_buffer;
	    };
	    $connection_to_baidu->onClose = function($connection_to_baidu)
	    {
	        echo "connection closed\n";
	    };
	    $connection_to_baidu->onError = function($connection_to_baidu, $code, $msg)
	    {
			        echo "Error code:$code msg:$msg\n";
			    };
	    $connection_to_baidu->connect();
		
	}
		// public function test(){
		// 	$task = new Worker();
		// 	// 进程启动时异步建立一个到www.baidu.com连接对象，并发送数据获取数据
		// 	$task->onWorkerStart = $this->task($task);

		// 	// 运行worker
		// 	Worker::runAll();
		// }
}