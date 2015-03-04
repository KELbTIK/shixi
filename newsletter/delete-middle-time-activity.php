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
$show="";
//$showmessage="";
$time=0;

if(!isset($_GET['statistic']))
{	$show="week";	}
else
{ $show=$_GET['statistic']; }

if($show=="week")
{
//$showmessage="Last 7 Days";
$time=time()-(86400*7);
}
else if($show=="month")
{
//$showmessage="This Month";
$time=mktime(0,0,0,date("m",time()),1,date("y",time()));
}
else if($show=="year")
{

//$showmessage="This Year";
$time=mktime(0,0,0,1,1,date("y",time()));
}
else if($show=="all")
{
//$showmessage="All Time";
$time=time();

}
?>
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2"  align="center" scope="row"><a href="view-time-activity.php">Time Based Logs</a> | <a href="view-admin-activity.php">Administrator Logs</a> | <a href="view-category-activity.php">Categorized Logs</a></td>
    </tr>
</table>
<?php
$result=mysql_query("select * from ".$table_prefix."admin_log_info where time<$time");
$n=mysql_num_rows($result);
if($n>0)
{
echo "<br>Are you sure to delete logs? If so please press delete button<br><br>";
?>
<form name="form1" method="post" action="delete-time-activity.php?time=<?php echo $time;?>">
  <input type="submit" name="Submit" value="Delete !">
  </form>
<?php
}
else
 {
  echo "<br><br>- No items found -<br><br>";
  }
 include_once("admin.footer.inc.php"); 
?>
