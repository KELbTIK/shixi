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
		header("Location:index.php"); exit(0);
	}
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:index.php"); exit(0);
}

$id=$_REQUEST['id'];
$categId=-1;
if(isset($_REQUEST['cid']))
	$categId=$_REQUEST['cid'];
$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
include_once("admin.header.inc.php");
$param=$_REQUEST['param'];
//echo $param;
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan ="2" align="center"><a href="editem.php?action=edit&id=<?php echo $id; ?><?php if($categId!=-1) echo "&cid=$categId"?>&param=<?php echo urlencode($param);?>">Edit Details</a>  | <a href="<?php echo "http://".$_SERVER['HTTP_HOST'].$param;?>">View All Emails</a>   </td>
  </tr>
    <tr>
      <td colspan ="2"><br></td>
    </tr>
    <tr>
      <td width="23%">&nbsp;Email</td>
      <td width="77%">&nbsp;<?php echo $mysql->echo_one("select email  from ".$table_prefix."email_advt where id='$id'"); ?></td>
    </tr>
    <tr>
      <td colspan ="2"><br></td>
    </tr>
	 <tr>
      <td width="23%">&nbsp;Name</td>
      <td width="77%">&nbsp;<?php echo $mysql->echo_one("select value  from ".$table_prefix."ea_extraparam where eid = '$id' and name='name'"); ?></td>
    </tr>
    <tr>
      <td colspan ="2"><br></td>
    </tr>
	<?php 
	while($fielddetails=mysql_fetch_row($extrafields))
	{?>
    <tr>
      <td>&nbsp;<?php echo $fielddetails[1]; ?></td>
      <td>&nbsp;<?php echo $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid = '$id' and name='$fielddetails[1]'"); ?></td>
    </tr>
    <tr>
      <td colspan ="2"><br></td>
    </tr>
	<?php 
	}
	?>
	<tr>
      <td colspan ="2"><br><a href="<?php echo "http://".$_SERVER['HTTP_HOST'].$param;?>">Goback</a><br></td>
    </tr>
  </table>

<?php include_once("admin.footer.inc.php"); ?>