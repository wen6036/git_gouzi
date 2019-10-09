<?php
include "Db.class.php";
include "Mysql.php";

$C = new Mysql('111.230.11.122','root','wang6036','www.51dewen.wang');
$time = time();

$data = $C->insert();
echo "<pre>";
var_dump($data);