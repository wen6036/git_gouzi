
<?php
//1.定义一个MySQL类
class Mysql{
    //2.定义了4个空的变量（主机名，账号，密码，库名）
    public $host;
    public $login;
    public $password;
    public $dbname;
    public $link;//定义一个连接符
    //3.利用构造方法完成赋值操作
    function __construct($host,$login,$password,$dbname){
        $this->host=$host;//localhost
        $this->login=$login;
        $this->password=$password;
        $this->dbname=$dbname;
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
		   	$time = time();
	   	    $sql = "insert into tz_test (content,time) values ('{$content}','{$time}')";
	        // $info=mysqli_query($this->link,$sql);
	        $info=mysqli_query($this->link,$sql);
	        return $info;	
	   }
}
// //6.实例化 调取查询的方法
// $mysql=new Mysql('localhost','root','root','库名');
// $data=$mysql->querySelect();
