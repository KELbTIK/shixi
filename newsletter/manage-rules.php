<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php
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
		header("Location:index.php");exit(0);
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
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to view email templates','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}



$getListSql="select * from ".$table_prefix."bmh_rules order by id desc";

?>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><br />
    <a href="create-rule.php"> Add New Rule</a> | <a href="manage-rules.php">Manage Rules </a> </td>
  </tr>
</table>

<?php
$result=mysql_query($getListSql);
$n=mysql_num_rows($result);
if($n==0)
{
	echo "<br>-No rules found-<br><br>";
}
else
{
?> 

<br>
<span class="inserted">&nbsp;&nbsp; &nbsp;&nbsp;All bmh rules are shown below</span> <br>
<br>
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr bgcolor="#CCCCCC">
<td width="12%" height="30"><strong>Type</strong></td>
<td width="66%"><strong>Pattern</strong></td>
<td width="7%"><strong>Email Index</strong> </td>
<td width="15%"><strong>Action </strong></td>
</tr>
<?php
$i=0;
while($row=mysql_fetch_row($result))
{
	 $i=$i+1;
	echo "<tr height=\"25\" ";
	if($i%2==1)
	{
		echo ' bgcolor="#EFEFEF"';
	}

	echo "><td style=\"border-bottom:1px solid #CCCCCC; \"><strong>";
	 if($row[1]==1)
	 	{
			echo "DSN";
		}
	else
		{
			echo "Body";
		}
	echo " </strong> &nbsp;&nbsp;&nbsp;</td>
	<td style=\"border-bottom:1px solid #CCCCCC; \"><input type=\"text\" value=\"$row[2]\" size=\"75\" width=\"20\" readonly>&nbsp;</td>
	<td style=\"border-bottom:1px solid #CCCCCC; \"> <strong>&nbsp;&nbsp; $row[3] </strong>&nbsp;&nbsp;&nbsp;</td>
	<td style=\"border-bottom:1px solid #CCCCCC; \"><a href=\"rule-edit.php?id=$row[0]\">Edit</a>  | <a href=\"rule-delete.php?id=$row[0]\">Delete</a></td>";
	
	echo "</tr>";
}?>
</table>
<?php
}
include_once("admin.footer.inc.php");
?>
