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
class Test extends Controller
{
	public function index(){
			$this->assign('title','test');

		$info['uid'] = 81331531;
		//综合积分
		$url = "http://49.235.36.29/accountPerformance/6050_81331531/stmt.txt";

	
	    $str = file_get_contents('http://49.235.36.29/accountPerformance/6050_81331531/dayinDealDays.txt');//将整个文件内容读入到一个字符串中
	 //    $str_encoding = mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//转换字符集（编码）
	 //    $arr = explode("\r\n", $str_encoding);//转换成数组

	 //    //去除值中的空格
	 //    foreach ($arr as &$row) {
	 //        $row = trim($row);
	 //    }
		// foreach( $arr as $k=>$v){   
		//     if( !$v ){
		//     	unset( $arr[$k] );
		//     }else{
		//     	$array[$k][] = explode(',',$v);
		//     }
		// }   


	    // dump($arr);
	    // dump($array);

        // $score = file_get_contents($url);
        // dump($str);
        $a = parse_ini_string($str);
        $b = end($a);
        dump($b);
        // $c = end($b['dayinDealDays']);
        // dump($c);

		return $this->fetch();
	}


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