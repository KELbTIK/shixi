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

include_once("admin.header.inc.php");?>
<link href="style.css" rel="stylesheet" type="text/css">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>
 <?php
$email=trim($_POST['email']);
$name=trim($_POST['name']);
phpSafe($email);	
phpSafe($name);

$resultstring="";
$result=mysql_query("select * from ".$table_prefix."email_advt_category order by name");
	if(isset($_COOKIE['inout_sub_admin']))
	{
		$subAdminId=getAdminId($mysql);
		$result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
		( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
		on a.id=b.eid order by a.name");
	}
$i=0;
while($row=mysql_fetch_row($result))
{
  if(isset($_POST[ "List".$i]))
  {
	  $id=$row[0];
	  $resultstring.=$id.",";
  }
  $i+=1;
}

if($resultstring==""||$email=="")
{
	echo "<br>Please fill all mandatory fields!!&nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	include_once("admin.footer.inc.php");
	exit(0);
}
 	
function getInoutFieldString($inoutVarName,$actualFieldName)
{
	return $inoutVarName."=".'urlencode($_REQUEST['."'".$actualFieldName."']);";
}	
	
if( substr_count($email," ")!=0 || substr_count($name," ")!=0 )//verify all fields for blank space
{
	echo "<br>Please verify the data entered. Remove spaces in between!!&nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	include_once("admin.footer.inc.php");
	exit(0);
}
?> 
<?php

$rest = substr($resultstring, 0, -1);

$nameFieldStr="";
$requestParamStr="&email=".'$email_inout';
$dummy="";
if($name!="")
{
	$nameFieldStr=getInoutFieldString('$name_inout',$name);
	$requestParamStr.="&name=".'$name_inout';
}

?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
<tr align="left">
   	<td colspan="3"><br><span class="inserted">Automatic List Subscription PHP Code<br><br> </span><span class="pagetable_activecell">
		  	Please copy the PHP code displayed in the text area below and paste it in the PHP action page of the form, where user enters their email and other details to subscribe. <br><br>
	     </span>
	</td>
    <td>&nbsp;</td>
</tr>
<tr>
	<td colspan="3" align="center">
       <textarea name="textarea" cols="100" rows="12">
<?php echo '<?php'; echo "\r\n";?>
$email_inout=urlencode($_REQUEST['<?php echo $email;?>']);
<?php 
echo $nameFieldStr;
if($nameFieldStr!="")
	echo "\r\n";
?>
<?php
	$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		$reqParamName="extra_personal_info".$fielddetails[0];
		if(trim($_POST[$reqParamName])!="")
		{
			echo getInoutFieldString("$".$reqParamName."_inout",trim($_POST[$reqParamName]));
			echo "\r\n";
			$requestParamStr.="&extra_personal_info".$fielddetails[0]."=$".$reqParamName."_inout";
		}
	}
?>
$fp=fopen("<?php echo $dirpath."phpsubscribe.php";?>?cid=<?php echo $rest;echo $requestParamStr;?>","r");
fclose($fp);
<?php echo '?>'; ?>
	  </textarea>	 
	</td>

</tr>

<tr>
<td colspan =4>
<?php
echo "<br><a href=\"javascript:history.back(-1);\">Go Back And Modify</a><br><br>";
?>
</td>
</tr>

</table>
<?php
include_once("admin.footer.inc.php");
?>
