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
error_reporting(0);
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
?><?php
$per=trim($_POST['per']);
$total=0;
$subject=trim($_POST['subject']);

$body=trim($_POST['body']);
$alt_body=trim($_POST['alt_body']);
$name=trim($_POST['name']);
$email=trim($_POST['email']);

//added on 20-03-2010
$emailtemplate=trim($_POST['emailtemplate']);
//added on 20-03-2010

phpsafe($name);
phpsafe($email);
phpsafe($per);
$cid=$_POST['category'];
$cname="";
if(isset($_POST['cname']))
{
$cname=$_POST['cname'];
phpsafe($cname);
}

	if($_POST['ex_field']!="0")
	{
		$ex_field=trim($_POST['ex_field']);
		phpsafe($ex_field);
		$ext_condition=trim($_POST['ext_condition']);
		//phpsafe($ext_condition);
		$ext_text=trim($_POST['ext_text']);
		phpsafe($ext_text);
	}
	else
		{
		$ex_field="";
		$ext_condition="";
		$ext_text="";
		}
if($per=="" || $subject=="" || $body=="" || $name=="" || $email=="" || $cid==""){
header("Location:goback.php?action=goback");
exit(0);
}


if(isset($_POST['unsub'])) 
{
			 if(substr_count($body,"{UNSUBSCRIBE-LINK}")==0)
			 {
 
 				if($_POST['html']==1)
					 $body.="<br><a href=\"{UNSUBSCRIBE-LINK}\">Click Here</a>  to unsubscribe from our mailing list.<br><br><br>";
 				else
 					$body.="
Click the link below to unsubscribe from our mailing list. 
{UNSUBSCRIBE-LINK}";
 
			 }
 
}
if((int)$per<=0)
{
 include("admin.header.inc.php");
echo "<br>You are allowed to enter only +ve integer values for number field.<br><br><a href=\"javascript:history.back(-1);\">Go Back</a> ";
include("admin.footer.inc.php");
exit(0);
}

if(!is_valid_email($email))
{
 include("admin.header.inc.php");
echo "<br>The email you have entered is not valid.<br><br><a href=\"javascript:history.back(-1);\">Go Back</a> ";
include("admin.footer.inc.php");
exit(0);
}


			//phpSafe($subject);
if(!get_magic_quotes_gpc())
		{
			$subject=mysql_real_escape_string($subject);
		}
if(!get_magic_quotes_gpc())
		{
			$body=mysql_real_escape_string($body);
			$alt_body=mysql_real_escape_string($alt_body);
		}
		

if(mysql_query("INSERT INTO `".$table_prefix."email_advt_curr_run` ( `id` , `total` , `emailsperrun` , `lastid` , `subject` , `body` , `sendername` , `senderemail` , `html` , `status` , `time` ,`cname`, `sent`,`extra_field`,`ex_condition`,`ex_value`,`email_template`,`alt_body`)
VALUES (
'', '$total', '$per', '0', '$subject', '$body', '$name', '$email', '$_POST[html]', '-1', '".time()."', '$cname',0,'$ex_field','$ext_condition','$ext_text','$emailtemplate','$alt_body');")){
$row=$mysql->select_last_row("".$table_prefix."email_advt_curr_run","id");
	$id=$row[0];
if($cid!=0){
	
	/*echo "hai/................";
	exit(0);*/
	mysql_query("insert into ".$table_prefix."ea_cnc values('','$id','$cid')");
	}
	if($log_enabled==1)
		{
			$aid=0;
			if(isset($_COOKIE['inout_sub_admin']))
				{
						$aid=getAdminId($mysql);
				}
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','Campaign created:".$cname."(id:".$id.")','".time()."','$CST_MLM_CAMPAIGN')");
		}
		if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				  mysql_query("insert into ".$table_prefix."campaign_access_control values('','$aid','$id','1')");
				}
		

}
else{
	//echo $mysql_error();
header("Location:goback.php?action=interror");
exit(0);
}
?><?php $row=$mysql->select_last_row("".$table_prefix."email_advt_curr_run","id");
	$id=$row[0];
	header("Location:campaign_created.php?id=$id");
	exit(0);
		?>