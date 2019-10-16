
<?php
//1.定义一个MySQL类
class Mysql{
    // $C = new Mysql('111.230.11.122','root','wang6036','www.51dewen.wang');
    //2.定义了4个空的变量（主机名，账号，密码，库名）
    public $host = '111.230.11.122';
    public $login = 'root';
    public $password ='wang6036';
    public $dbname = 'www.51dewen.wang';
    public $link;//定义一个连接符
    //3.利用构造方法完成赋值操作
    function __construct(){
        // $this->host=$host;//localhost
        // $this->login=$login;
        // $this->password=$password;
        // $this->dbname=$dbname;
        //4.在赋值的基础上做了：连接数据库  设置字符集
        $this->link=mysqli_connect($this->host,$this->login,$this->password,$this->dbname);
		mysqli_set_charset($this->link,'utf8');
    }
   //5.完成了一个查询的方法：书写SQL语句   执行SQL语句    return
	   public function querySelect(){
	        $sql="select * from tz_userinfo";
	        $info=mysqli_query($this->link,$sql);
	        $data=mysqli_fetch_all($info,MYSQLI_ASSOC);
	        return $data;
	   }

	   public function insert($content){
            // $time = time();
            // $sql = "insert into tz_time_info (info,create_time) values ('{$content}','{$time}')";
            // $info=mysqli_query($this->link,$sql);
            // 
        //             $content = '0#tradeCenter#newTrade#{
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
            $b = explode('#', $content);
            $type = $b[2];

            if(strpos($type,'newTrade') !== false){ 
                $type = 'newTrade';
                $time =time();
                $d = substr($b[3],0,strlen($b[3])-1);
                $c = json_decode($d,true);

                $brokerID = $c['brokerID'];
                $userID = $c['userID'];
                $info = json_encode($c);

                $sql = "insert into tz_time_info (brokerID,userID,info,type,create_time) values ($brokerID,$userID,'{$info}','{$type}','{$time}')";
                $status=mysqli_query($this->link,$sql);

            }else if(strpos($type,'positions') !== false){ 
                $type = 'positions';
                $d = substr($b[3],0,strlen($b[3])-1);
                $c = json_decode($d,true);
                $time =time();
                if(is_array($c)){
                    foreach ($c as $key => $value) {
                        $brokerID = $value['brokerID'];
                        $userID = $value['userID'];
                        $info = json_encode($value);
                        $sql = "insert into tz_time_info (brokerID,userID,info,type,create_time) values ($brokerID,$userID,'{$info}','{$type}','{$time}')";
                        $status=mysqli_query($this->link,$sql);
                    }
                }
            }else{
                $time =time();
                $info = $content;
                $sql = "insert into tz_time_info (info,type,create_time) values ('{$info}','{$type}','{$time}')";
                $status=mysqli_query($this->link,$sql);
            }

            $status = isset($status)?$status:0;
	        return $status;	
	   }
}
// //6.实例化 调取查询的方法
// $mysql=new Mysql('localhost','root','root','库名');
// $data=$mysql->querySelect();
