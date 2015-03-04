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


 if($script_mode=="demo") 
 {
 include_once("admin.header.inc.php");
 ?>
	<br><span class="info">You cannot do this in online demo. </a></span><br><br>
 <?php 
  include_once("admin.footer.inc.php");
  exit(0);
 }


if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to unsubscribe  bulk emails','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
				
	$emails=" ".trim($_POST['emails'])." ";
	
	$i=0;
	$new=0;
	$old=0;
	$already=0;
	$str="";
	$str1="";
	$str2="";
	
		while(isset($emails{$i}))
			{ 
			
			
				$email=" ";
				$j=0;
				if($emails{$i}==" " || $emails{$i}=="(" || $emails{$i}==")" || $emails{$i}=="]" || $emails{$i}=="[" || $emails{$i}=="\r\n" || $emails{$i}=="\n" || $emails{$i}=="," || $emails{$i}=="<" || $emails{$i}==">"|| $emails{$i}==":" || $emails{$i}=="'" || $emails{$i}=="\"")
				{ 	
				
					$i+=1;
					while(1) 
					{
						if(!isset($emails{$i}))
							break;
							
						$email{$j}=$emails{$i};
						$j+=1;
						$i+=1;
						
						if(!isset($emails{$i}))
							break;
						if($emails{$i}==" " || $emails{$i}=="(" || $emails{$i}==")" || $emails{$i}=="]" || $emails{$i}=="[" || $emails{$i}=="\r\n" || $emails{$i}=="\n" || $emails{$i}=="," || $emails{$i}==">"|| $emails{$i}=="<" || $emails{$i}==":" || $emails{$i}=="'" || $emails{$i}=="\"" || $emails{$i}=="\\" || $emails{$i}=="/"|| $emails{$i}=="&"|| $emails{$i}==";")
						{		
						
								$email=trim($email);
								$email=replaceAllSubStr($email," ","");
								$email=replaceAllSubStr($email,"(","");
								$email=replaceAllSubStr($email,")","");
								$email=replaceAllSubStr($email,"[","");
								$email=replaceAllSubStr($email,"]","");
								$email=replaceAllSubStr($email,"\r\n","");
								$email=replaceAllSubStr($email,"\n","");
								$email=replaceAllSubStr($email,",","");
								$email=replaceAllSubStr($email,"<","");
								$email=replaceAllSubStr($email,">","");
								$email=replaceAllSubStr($email,"\'","");
								$email=replaceAllSubStr($email,"\"","");																
								$email=replaceAllSubStr($email,"\\","");																
								$email=replaceAllSubStr($email,"/","");																
								$email=replaceAllSubStr($email,";","");																
								$email=replaceAllSubStr($email,"&","");																
								$email=replaceAllSubStr($email,"|","");																
								//if((substr_count($email,"@")==1)&&(substr_count($email,".")>=1))
								
								if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
								{ 
								
									//echo "valid email ".$email."<br>";
									if($mysql->total("".$table_prefix."email_advt","email='$email'")==0)
									{
									//echo "hiiii";
										//mysql_query("INSERT INTO `".$table_prefix."email_advt` ( `id` , `email` , `unsubstatus` , `time` )VALUES ('', '$email', '0', '".time()."');");
										//echo "<span class=\"inserted\">".$email." - > Inserted Into Database. <br></span>"; 
										
										//$roww=$mysql->select_last_row("".$table_prefix."email_advt","id");
										//$id=$roww[0]; //echo $name;
										//$arraycount=addEmailToList($mysql,$id,$table_prefix,$arraycount);
										if(substr_count($str1,$email)==0)
											{
											$new+=1;
											$str1.=$email."<br>";
											}
									}
									else
									{
										//echo "<span class=\"already\">".$email." - > Already In Database <br</span>"; 
										
										$id=$mysql->echo_one("select id from `".$table_prefix."email_advt` where email='$email'");
										//$rems_count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt  $str1 ");
										if($mysql->total("".$table_prefix."email_advt","id='$id' and unsubstatus='1'")==0)
										{
										mysql_query("update `".$table_prefix."email_advt` set unsubstatus='1' where id='$id' ");
										mysql_query("update `".$table_prefix."ea_em_n_cat` set unsubstatus='1' where eid='$id' ");
										//$arraycount=addEmailToList($mysql,$id,$table_prefix,$arraycount);
										if(substr_count($str,$email)==0)
											{
											$old+=1;
											$str.=$email."<br>";
											}
										}
										else
										{
										if(substr_count($str2,$email)==0)
											{
											$already+=1;
											
											$str2.=$email."<br>";
											}
										}

									}
								}
								
								$i-=1;
								break;
						}//end of if 
					}// while 1
				}//end of if 
				$i+=1;
				
				
			}//end of while
			

			

			
		

include("admin.header.inc.php");?>
 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="viewems.php">All Emails</a> | <a href="viewems.php?action=active">Active Emails </a> | <a href="viewems.php?action=unsub">Unsubscribed Emails</a> | <a href="category_viewall.php">Emails in Mailing Lists </a> | <a href="searchem.php">Search Emails</a> </td>
  </tr>
</table>

  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Total number of emails unsubscribed from the database  :<?php echo $old."<br>";?>
	   Total number of emails which are already unsubscribed in the database  :<?php echo $already."<br>";?>
	   Total number of emails which are not in the database  :<?php echo $new."<br>";?></strong>
	   <?php if($str!="")
	  
	   {
	    mysql_query("insert into ".$table_prefix."admin_log_info values('','0','$old email(s) unsubscribed from database manually(bulk unsubscription)','".time()."','$CST_MLM_EMAIL')");
		?>
	   <br><span class="inserted">Emails which are unsubscribed from database are listed below</span><br><?php echo $str;
	   }
	   ?>
	   <?php if($str2!="")
	  
	   {
	   // mysql_query("insert into ".$table_prefix."admin_log_info values('','0','$old email(s) unsubscribed from database manually(bulk unsubscription)','".time()."','$CST_MLM_EMAIL')");
		?>
	   <br><span class="inserted">Emails which are already unsubscribed in database are listed below</span><br><?php echo $str2;
	   }
	   ?>
	   <?php if($str1!="")
	  
	   {
	   // mysql_query("insert into ".$table_prefix."admin_log_info values('','0','$old email(s) unsubscribed from database manually(bulk unsubscription)','".time()."','$CST_MLM_EMAIL')");
		?>
	   <br><span class="inserted">Emails which are not in database are listed below</span><br><?php echo $str1;
	   }
	   ?></td>
      <td>&nbsp;</td>
    </tr>
   
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
   
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <?php  include("admin.footer.inc.php"); ?>