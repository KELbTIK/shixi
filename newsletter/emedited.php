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

include_once("admin.header.inc.php");
$id=$_POST['id'];
$param=$_POST['param'];
$categId=-1;
if(isset($_REQUEST['cid']))
	$categId=$_REQUEST['cid'];
$email=trim($_POST['email']);
if($email=="") 
{
echo "Email address cannot be blank. &nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a>";
include_once("admin.footer.inc.php"); 
exit(0);
}
$id=$_POST['id'];
$verify=is_valid_email($email);
if($verify==false)
{
echo "Please enter a valid email address."."&nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a>"; 
include_once("admin.footer.inc.php"); 
exit(0); 
}
if(!isValidEmailAccess($id,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select email from  ".$table_prefix."email_advt where id=$id");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit the email $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this email.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}

if(!(mysql_query("update ".$table_prefix."email_advt set email='$email' where id=$id")))
{
echo "Email address already exists in database."."&nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a>"; 
include_once("admin.footer.inc.php"); 
exit(0); 
}
$aid=0;
		//echo "hii";
		if(isset($_COOKIE['inout_sub_admin']))
			{
				$aid=getAdminId($mysql);
			}	
			if($log_enabled==1)
		    {
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','Email edited : $email','".time()."','$CST_MLM_EMAIL')");
			}
$fieldval =$_POST['name'];
updateField($mysql,$id,'name',$fieldval,$table_prefix);	 
$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
while($fielddetails=mysql_fetch_row($extrafields))
{
	$fieldval =$_POST["extra_personal_info".$fielddetails[0]];
	updateField($mysql,$id,$fielddetails[1],$fieldval,$table_prefix);
}

function updateField($mysql,$id,$fieldname,$fieldval,$table_prefix)
{
	if(str_replace(" ","",$fieldval)=="")
	{
		mysql_query("delete from ".$table_prefix."ea_extraparam where eid='$id' and name='$fieldname'");
	}
 	else
	{
		if($mysql->total("".$table_prefix."ea_extraparam","eid='$id' and name='$fieldname'")==0)
		{
			mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fieldname','$fieldval')");
		}
		else
		{
			mysql_query("update ".$table_prefix."ea_extraparam set value ='$fieldval' where eid='$id' and name='$fieldname' ");
		}
	}
}
?>
<br><span class="inserted">Email details edited successfully.</span><br><br>

<a href="<?php echo "http://".$_SERVER['HTTP_HOST'].$param;?>">View All Emails</a><br><br>
<?php
include_once("admin.footer.inc.php"); ?>