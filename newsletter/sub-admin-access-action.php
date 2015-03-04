<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php
$file="export_data";

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

include("admin.header.inc.php");
if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to update access permission','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="create_new_sub_admin.php" >Create New  Administrator</a>&nbsp;|&nbsp; <a href="manage_sub_admins.php" >Manage  Administrators</a></td>
  </tr>
</table>
<?php
$uid=$_POST['uid'];

$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$uid'");

	 if(""== $name)
	  {
	  echo "<br><span class=\"already\">Please select an administrator.<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
	  include("admin.footer.inc.php");
	  exit(0);
	  }
$i=0;
$str="";
$sqlstr="";
$tot=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt_category");
while($i<$tot)
{
  if(isset($_POST[ "List".$i]))
  {
   $lis=$_POST[ "List".$i];
    $str.=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id='$lis'").", "; 
  	$id=$_POST["List".$i];
	if($sqlstr=="")
		$sqlstr.="$id";
	else
		$sqlstr.=",$id";	

	if($mysql->total($table_prefix."admin_access_control","aid='$uid' and eid='$id'")==0)
	{
	  	mysql_query("insert into ".$table_prefix."admin_access_control values('','$uid','$id')");
	}
  }
  $i+=1; 
} 

$accesslist=trim($_POST['accesslist']);
$str=trim($str);
if($accesslist=="")
	$accesslist="No List";
if($str=="")
	$str="No List";	
else
 	$str=substr($str,0,strrpos($str,",")); 


if($log_enabled==1)
{
	mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Access modified for ".$name.": [".$accesslist."] -> [".$str."]','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
}

if($sqlstr!="")//access is given for some lists
{
	//$access_removed=mysql_query("select eid from ".$table_prefix."admin_access_control where aid='$uid' and eid not in (".$sqlstr.")");
	mysql_query("delete from ".$table_prefix."admin_access_control where aid='$uid' and eid not in (".$sqlstr.")");
	//echo $sqlstr;
	
	
	/* Giving access back to campaigns which was created by this admin*/
	$result=mysql_query("select distinct(a.id) from ".$table_prefix."campaign_access_control a inner join (select * from ".$table_prefix."ea_cnc where catid in (".$sqlstr.") ) b on a.cid=b.campid  where a.aid='$uid' "); 
	while($row=mysql_fetch_row($result))
	{
	//echo $row[0];
		mysql_query("update ".$table_prefix."campaign_access_control set access_status=1 where id=$row[0]");
	}
	$result=mysql_query("select distinct(a.id) from ".$table_prefix."campaign_access_control a where a.aid='$uid' and  a.cid not in (select distinct(campid) from ".$table_prefix."ea_cnc)"); 
	while($row=mysql_fetch_row($result))
	{
	//echo $row[0];
		mysql_query("update ".$table_prefix."campaign_access_control set access_status=1 where id=$row[0]");
	}
	
	
	/* Removing access from campaigns which were created by this admin on the lists which he lost access now */
	$result=mysql_query("select distinct(a.id) from ".$table_prefix."campaign_access_control a inner join (select * from ".$table_prefix."ea_cnc where catid not in (".$sqlstr.") ) b on a.cid=b.campid  where a.aid='$uid' "); 
	while($row=mysql_fetch_row($result))
	{
		mysql_query("update ".$table_prefix."campaign_access_control set access_status=0 where id=$row[0]");
	}
	
}
else//access is given for no lists
{
	mysql_query("delete from ".$table_prefix."admin_access_control where aid='$uid' ");
	mysql_query("update ".$table_prefix."campaign_access_control set access_status=0 where aid='$uid'");
}


?>
<span class="inserted">
<?php
	echo "<br>Access control updated sucessfully.<br><br>";
?>
</span>
<?php include_once("admin.footer.inc.php"); ?>