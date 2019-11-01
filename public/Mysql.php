
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
            $b = explode('#', $content);
            // $type = $b[1];
            $type = $b[2];

            if(strpos($type,'newTrade') !== false){ 
                $type = 'newTrade';
                $time =time();
                $info = substr($b[3],0,strlen($b[3])-1);
                $arr = json_decode($info,true);

                $futures_company = $arr['brokerName'];
                $userID = $arr['userID'];

                $sql = "insert into tz_time_info (futures_company,userID,info,type,create_time) values ('{$futures_company}','{$userID}','{$info}','{$type}','{$time}')";
                $status=mysqli_query($this->link,$sql);

            }else if(strpos($type,'positions') !== false){ 
                $type = 'positions';
                $time =time();
                $jsonstr = substr($b[3],0,strlen($b[3])-1);
                $arr = json_decode($jsonstr,true);
                if(is_array($arr)){
                    foreach ($arr as $key => $value) {
                        $futures_company = $value['brokerName'];
                        $userID = $value['userID'];
                        $info = json_encode($value,JSON_UNESCAPED_UNICODE);
                        $sql = "insert into tz_time_info (futures_company,userID,info,type,create_time) values ('{$futures_company}','{$userID}','{$info}','{$type}','{$time}')";
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
