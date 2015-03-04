<?php
$table_prefix=$tableprefix;
$script_version="4.3";
$unsub_email_path=$dirpath."unsub.php"; 
$web_prev_path=$dirpath."viewbody.php";
$sub_path=$dirpath."subscribe.php"; 

error_reporting(0);

$script_mode="live";


include("mysql.cls.php");
$mysql=new mysql($mysql_server,$mysql_username,$mysql_password,$mysql_dbname);
include("paging.cls.php"); 
$paging=new paging();
if($table_prefix!="")
$table_prefix.="_";
//else
//$table_prefix="inoutmlm_";

?>