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

?><?php include("admin.header.inc.php"); ?>
<?php
$param=$_REQUEST['param'];

if($_REQUEST['Submit']=="Delete !")
{
 $catid=$_POST['category'];
 $str="The selected email(s) are successfully deleted!"; 
 $cls="inserted";
 $cnt=0;
 for($i=0;$i<=100;$i+=1)
 {
	$inc="C".$i;
    if(isset($_REQUEST[$inc]))
	{
		 $id=$_REQUEST[$inc];
		$id=$id+0;
		$cnt=$cnt+1;
		$sql="delete from ".$table_prefix."email_advt where id=$id";
		mysql_query($sql);

		$sql="delete from ".$table_prefix."ea_em_n_cat where eid=$id";
		mysql_query($sql);

		$sql="delete from ".$table_prefix."ea_extraparam where eid=$id";
		mysql_query($sql);
		
		
		$sql="delete from ".$table_prefix."bounce_mail_details where eid=$id";
		mysql_query($sql);
		
	}
  }
  if($cnt==0)
  {
  				$cls="already";
				$str="Please select atleast one email id!!";
  }
  
  if($cnt>0)
  {
    if($log_enabled==1)
	{
  			$aid=0;//subadmin does not have delete access
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$cnt email(s) deleted from database','".time()."','$CST_MLM_EMAIL')");
    } 
 }
}
 
if($_REQUEST['Submit']=="Add to List !")
{

 $catid=$_POST['category'];
 if($catid=="")
 {
  echo "<br><br><span class=\"already\"> Please go back and select a mailing list!!&nbsp;&nbsp;</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>"; 
  include_once("admin.footer.inc.php");
  exit(0); 
 }
 

 if(!isValidAccess($catid,$CST_MLM_LIST,$table_prefix,$mysql))
 {
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id=$catid");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to add emails to the list $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this list.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
 }
 
$count=0;			  
 $cnt=0;
 for($i=0;$i<=100;$i+=1)
 {
    $inc="C".$i;
    if(isset($_REQUEST[$inc]))
	{
	 $id=$_REQUEST[$inc];
	 $cnt=$cnt+1;
	 $id=$id+0;
	 if($mysql->total("".$table_prefix."ea_em_n_cat","cid=$catid AND eid=$id")==0)
	 {
	 $t=time();
      if($catid!="")
       mysql_query("insert into ".$table_prefix."ea_em_n_cat values('','$id','$catid',0,'$t')");
    $count=$count+1;
	}
	//echo $id;
	}
  }
    if($cnt==0)
  {
  				$cls="already";
				$str="Please select atleast one email id !!";
  }
   if($count==0)
  {
  				$cls="already";
				$str="Selected emails are already in list!!";
  }
  if($cnt>0 && $count>0)
  {
   $str=$count. "email(s) are successfully added to the selected email list!"; 
        if($log_enabled==1)
		{
              $aid=0;
		      if(isset($_COOKIE['inout_sub_admin']))
			  {
				$aid=getAdminId($mysql);
			  }	
			  $catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category]");
			  mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$count email(s) added to list(s) $catlist','".time()."','$CST_MLM_LIST')");
       }
 }
}
 
 
if($_REQUEST['Submit']=="Unsubscribe !")
{
 $str="The selected email(s) are successfully unsubscribed!"; 
 $cnt=0;
 for($i=0;$i<=100;$i+=1)
 { 
   $inc="C".$i;
   if(isset($_REQUEST[$inc]))
   {
  	 $cnt=$cnt+1;
	 $id=$_REQUEST[$inc];
	 $id=$id+0;
	 $sql="update ".$table_prefix."email_advt set unsubstatus=1 where id=$id";
	 mysql_query($sql);
	 	 $sql="update ".$table_prefix."ea_em_n_cat set unsubstatus=1 where eid=$id";
	 mysql_query($sql);
	 
	 
	 $sql="delete from ".$table_prefix."bounce_mail_details where eid=$id";
		mysql_query($sql);
	 
   }
 }
   if($cnt==0)
  {
  				$cls="already";
				$str="Please select atleast one email id !!";
  }
 if($cnt>0)
 {
    if($log_enabled==1)
	{
  			$aid=0;//subadmin does not have unsubscribe acsess
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$cnt email(s) unsubscribed from system','".time()."','$CST_MLM_EMAIL')");
    } 
 }	
}
 
?>
<?php echo "<br><span class=\"$cls\">$str</span>"; ?> 
<a href="<?php echo "http://".$_SERVER['HTTP_HOST'].$param;?>">View All Emails</a><br><br>
<?php include("admin.footer.inc.php"); ?>
