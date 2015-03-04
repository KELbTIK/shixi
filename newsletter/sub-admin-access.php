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
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to update  access permission','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
	$uid=$_GET['id'];
	  
	 if(""== $mysql->echo_one("select username from $table_prefix"."subadmin_details where id ='$uid'"))
	  {
	  echo "<br><span class=\"already\">Please select an administrator.<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
	  include("admin.footer.inc.php");
	  exit(0);
	  }
$i=0;
$result=mysql_query("select * from ".$table_prefix."email_advt_category");
$resultlist=mysql_query("select eid from ".$table_prefix."admin_access_control where aid =$uid");
$existingarray = array();
while($row=mysql_fetch_row($resultlist))
{
	$existingarray[$row[0]]=true;
}
?>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="create_new_sub_admin.php" >Create New  Administrator</a>&nbsp;|&nbsp; <a href="manage_sub_admins.php" >Manage  Administrators</a></td>
  </tr>
</table>

<form name="form1" method="post" action="sub-admin-access-action.php">
<input type="hidden" name="uid" id="uid" value="<?php echo $uid;?>">
  <table width="99%" cellpadding="0" cellspacing="0">
    <tr>
      <td  colspan="2" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td height="35" colspan="2" valign="top"><span class="inserted">Please check or uncheck email lists for which you want to permit access to <?php echo $mysql->echo_one("select username from $table_prefix"."subadmin_details where id =$uid");?>.</span></td>
    </tr>
    <tr bgcolor="#CCCCCC">
      <td width="441" height="30" valign="top" bgcolor="#CCCCCC"><strong>Email Lists</strong> 
	  <?php if(mysql_num_rows($result)>0) {?>
	  (<a href="javascript:checkAll('document.form1.List',<?php echo mysql_num_rows($result);?>)">All</a> , <a href="javascript:uncheckAll('document.form1.List',<?php echo mysql_num_rows($result);?>)">None</a>)
	  <?php } ?></td>
      <td valign="top" bgcolor="#CCCCCC">&nbsp;</td>
    </tr>
    <?php

$str="";
while($row=mysql_fetch_row($result))
{

?>
    <tr <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>>
      <td height="25" valign="top" style="border-bottom:1px solid #CCCCCC; "><input name="<?php echo "List".$i; ?>" type="checkbox"  id="List<?php echo $i; ?>" value="<?php echo $row[0]; ?>" 
	  <?php 
	  if($existingarray[$row[0]]==true)
	  {
	 	 echo "checked"; 
		 if($str=="")
		 	$str.=$row[1];
		 else
		 	$str.=", ".$row[1];
	  }
	  ?> >
       <?php echo $row[1]; ?>
          
      <td width="695" height="25" style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
    </tr>
    <?php $i+=1; 
	
	}

?>
<input type="hidden" name="accesslist" id="accesslist" value=" <?php echo $str; ?>">
    <tr>
      <td colspan="2"   valign="top"> <br>
          <br>
          <input type="submit" name="Submit" value="Update Access Control!">
          <p></p></td>
    </tr>
  </table>
</form>
<?php include_once("admin.footer.inc.php"); ?>