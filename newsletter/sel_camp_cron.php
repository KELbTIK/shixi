<?php 


set_time_limit(0);
include("config.inc.php");
include("admin.header.inc.php");
  include("class.Email.php");
error_reporting(1);

if(isset($_REQUEST['id']))
$addon="id=$_REQUEST[id] and ";
else
$addon="";

$result=mysql_query("select * from ".$table_prefix."email_advt_curr_run where $addon status=1");

 include_once("class.Email.php");


?>