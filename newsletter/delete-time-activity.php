<?php
include("config.inc.php");
if(!isset($_COOKIE['admin']))
{
header("Location:index.php"); exit(0);
}
$inout_username=$_COOKIE['admin'];
$inout_password=$_COOKIE['inout_pass'];
if(isset($_COOKIE['inout_sub_admin']))
{
	$usercount=$mysql->total($table_prefix."subadmin_details","username='$inout_username' and password='$inout_password' and status=1");
	if(0==$usercount)
	{
		header("Location:index.php"); exit(0);
	}
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:index.php"); exit(0);
}

include_once("admin.header.inc.php");
if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to delete  activity logs','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}

?>
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2"  align="center" scope="row"><a href="view-time-activity.php">Time Based Logs</a> | <a href="view-admin-activity.php">Administrator Logs</a> | <a href="view-category-activity.php">Categorized Logs</a></td>
    </tr>
	</table>
	<?php 
	$time=$_GET['time'];
	//echo $time;
$result=mysql_query("select * from ".$table_prefix."admin_log_info where time<$time");
$n=mysql_num_rows($result);
if($n>0)
{
if($script_mode=="demo")
{
?>
<span class="error"><br>
	This feature is disabled in demo version. <br><br>
</span>
<?php include("admin.footer.inc.php"); exit(0); 
} ?>
<br>
<?php
mysql_query("delete from ".$table_prefix."admin_log_info where time<$time");
 echo "<br><br><span class=\"inserted\">Selected logs have been deleted successfully.</span><br><br>";
 }
 else
 {
  echo "<br><br>- No items found -<br><br>";
  }
 include_once("admin.footer.inc.php"); 
?>
