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
		header("Location:index.php");
	}
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:index.php"); exit(0);
}

include_once("admin.header.inc.php");

$getListSql="select * from ".$table_prefix."email_advt_category order by time desc";
if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.time desc";
}

?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>

<?php
$result=mysql_query($getListSql);
$n=mysql_num_rows($result);
if($n==0)
{
	echo "<br>-No Email Lists Found-<br><br>";
}
else
{
?> 

<br>
<span class="inserted">&nbsp;&nbsp; &nbsp;&nbsp;All email lists are shown below</span> <br>
<br>
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr bgcolor="#CCCCCC">
<td height="30">&nbsp;&nbsp;<strong>List Name</strong></td>
<td><strong>Action</strong></td>
<td></td>
<td> </td>
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
	echo "><td style=\"border-bottom:1px solid #CCCCCC; \"><strong>&nbsp;&nbsp; $row[1] </strong> &nbsp;&nbsp;&nbsp;</td><td style=\"border-bottom:1px solid #CCCCCC; \"><a href=\"category_editname.php?id=$row[0]\">Edit</a> &nbsp;&nbsp;&nbsp;</td><td style=\"border-bottom:1px solid #CCCCCC; \"><a href=\"category_delete.php?id=$row[0]\">Delete</a> &nbsp;&nbsp;&nbsp;</td><td style=\"border-bottom:1px solid #CCCCCC; \">  <a href=\"viewems.php?cid=$row[0]\">View Emails</a> </td></tr>";
}?>
</table>
<?php
}
include_once("admin.footer.inc.php");
?>
