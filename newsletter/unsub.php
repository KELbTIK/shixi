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
$flag_var=0;
$emailflag=0;
if($smtpmailer == 1)
		{
		if(phpversion()>=5)
			{
			$flag_var=1;
					 if (!class_exists('phpmailer')) {
							require("PHPMailer/class.phpmailer.php");
							}
			}

}
$already_added_list="";
$newly_added_list="";
$listname="";
$catlist="";
$id=$_REQUEST['id'];
$time=$_REQUEST['t'];
$cid=0;
if(isset($_REQUEST['cid']))
$cid=$_REQUEST['cid'];

if($cid==0)
		{
			$emails=$mysql->echo_one("select email from ".$table_prefix."email_advt where id=$id");
			mysql_query("update ".$table_prefix."email_advt set unsubstatus=1 where id=$id AND time=$time");
		   if($mysql->total($table_prefix."email_advt","id=$id AND time=$time")>0)
				{   
				$sub_list=mysql_query("select * from ".$table_prefix."ea_em_n_cat where eid=$id");
				  while($sub_row=mysql_fetch_row($sub_list))
				  {
							if($mysql->total($table_prefix."ea_em_n_cat ","eid=$id AND cid=$sub_row[2] AND unsubstatus=0")>0)
								$catlist.=" ".$mysql->echo_one("select name from`".$table_prefix."email_advt_category` where id='$sub_row[2]'").",";
							else
								$already_added_list.=" ".$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$sub_row[2]'").",";
					}
			  }
			mysql_query("update ".$table_prefix."ea_em_n_cat set unsubstatus=1 where eid=$id");
			 if($already_added_list!="")
				 $already_added_list=trim(substr($already_added_list,0,strrpos($already_added_list,",")));
			 if($catlist!="")
				  $newly_added_list=$catlist=trim(substr($catlist,0,strrpos($catlist,",")));
			if($log_enabled==1 && $catlist!="")
				{
				  $str="[".$catlist."]";
					mysql_query("insert into ".$table_prefix."admin_log_info values('','-1','$emails unsubscribed(email link) from $str','".time()."',$CST_MLM_SUBSCRIPTION)");
				}
		  
}
else if(substr_count($cid,",")>0)  /// this will hapeen only subadmin all email campaign which is disabled now :)
{
							$emails=$mysql->echo_one("select email from ".$table_prefix."email_advt where id=$id");
          $catid=explode(",",$cid);
		  $str="";
		  $count=count($catid);
		  $i=0;
		  while($i<$count)
		  {
		       if($mysql->total($table_prefix."email_advt","id=$id AND time=$time")>0)
					{   
							mysql_query("update  ".$table_prefix."ea_em_n_cat set unsubstatus=1 where eid=$id AND cid=$catid[$i]");
							if(mysql_affected_rows()>0)	
								$catlist.=" ".$mysql->echo_one("select name from`".$table_prefix."email_advt_category` where id='$catid[$i]'").",";
							else
								$already_added_list.=" ".$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$catid[$i]'").",";
					}
		  
		  $i++;
		  
		  }
		 if($already_added_list!="")
			 $already_added_list=trim(substr($already_added_list,0,strrpos($already_added_list,",")));
		 if($catlist!="")
			  $newly_added_list=$catlist=trim(substr($catlist,0,strrpos($catlist,",")));
		if($log_enabled==1 && $catlist!="")
			{
			  $str="[".$catlist."]";
				mysql_query("insert into ".$table_prefix."admin_log_info values('','-1','$emails unsubscribed(email link) from $str','".time()."',$CST_MLM_SUBSCRIPTION)");
			}
		  
}		
else
{
	if($mysql->total($table_prefix."email_advt","id=$id AND time=$time")>0)
	{   
		$emails=$mysql->echo_one("select email from ".$table_prefix."email_advt where id=$id");
		//$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$cid");
		mysql_query("update ".$table_prefix."ea_em_n_cat set unsubstatus=1 where eid=$id AND cid=$cid");
							if(mysql_affected_rows()>0)	
								$catlist=$newly_added_list=$mysql->echo_one("select name from`".$table_prefix."email_advt_category` where id='$cid'");
							else
								$already_added_list=$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$cid'");
		if($log_enabled==1  && $catlist!="")
		{
		mysql_query("insert into ".$table_prefix."admin_log_info values('','-1','$emails unsubscribed(email link) from $catlist','".time()."',$CST_MLM_SUBSCRIPTION)");
		}
	}
}


$email=$emails;

$msg = <<< EOB

Hello,

The following user has unsubscribed from $catlist via email unsubscribe link.

Email		 	: $email

Regards,
Inout Mailing List Manager

EOB;

$headers  = "";
$headers .= "From: {$admin_general_notification_email}\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
$headers .= "Content-Transfer-Encoding: 8bit\n";

if(trim($admin_general_notification_email)!="" && $catlist!="")
		{
		 if(($smtpmailer == 1) && ($flag_var==1))
										{
										$mail = new PHPMailer(true);
												//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
												$mail->IsSMTP();                                        // set mailer to use SMTP
												$mail->Host = $smtp_host; // specify SMTP mail server
												$mail->Port = $smtp_port; // specify SMTP Port
												$mail->SMTPAuth = $smtp_auth; // turn on SMTP authentication
												$mail->Username = $smtp_user; //Full SMTP username
												$mail->Password =$smtp_pass; //SMTP password
												
												//if($smtp_secure )
												  $mail->SMTPSecure =$smtp_secure;                 // sets the prefix to the servier
								
												
												$mail->From = "$smtp_user";
												$mail->FromName = "";
												//$mail->Sender =$error_ret_mail;
												$mail->AddAddress($admin_general_notification_email);
												$mail->AddReplyTo($mail->From, $mail->FromName);
												
												//$mail->WordWrap = 50; //optional, you can delete this line
												
												//$mail->IsHTML(true); //set email format to HTML
												
												$mail->Subject = "Unsubscription notification";
												$mail->Body = $msg;  //html body
												//$mail->AltBody = $content;  //plain text body
										
											$send_res=$mail->Send();
										}
		else
			{
				mail($admin_general_notification_email, "Unsubscription notification", $msg,$headers);
			}

		}
	


header("Location:".$unsubokpath.getUrlAppendChar($unsubokpath)."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
function getUrlAppendChar($url)
{
if(strpos($url,"?")>0)
return "&";
else
return "?";
}		
?>
