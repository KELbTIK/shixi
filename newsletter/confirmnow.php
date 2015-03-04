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
$id=$_REQUEST['id'];
$time=$_REQUEST['t'];
$cid=$_REQUEST['cid'];
$t=time();
$catlist="";
$catIdArr = explode(",",$cid);
$cnt=count($catIdArr);
for($i=0;$i<$cnt;$i++)
{
	if($mysql->total("".$table_prefix."ea_em_n_cat","cid='$catIdArr[$i]' and eid='$id' and unsubstatus=1")==1)
	{
		$catlist.=" ".$mysql->echo_one("select name from`".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").",";
	}
}	
mysql_query("update ".$table_prefix."email_advt set unsubstatus=0 where id=$id AND time=$time");
mysql_query("update ".$table_prefix."ea_em_n_cat set unsubstatus=0,time='$t' where eid=$id AND cid in(".$cid.")");
if($catlist!="")
{
	$catlist=trim(substr($catlist,0,strrpos($catlist,",")));
	$email=$mysql->echo_one("select email from ".$table_prefix."email_advt where id= $id");
	$msg ="
	
Hello,

The following user has confirmed his subscription to the list(s) \"$catlist\".

Email		 	: $email

Login to the admin area to see his details.

Regards,
Inout Mailing List Manager";

	
	$headers  = "";
	$headers .= "From: {$admin_general_notification_email}\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
	if(trim($admin_general_notification_email)!="")
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
							
							$mail->Subject = "New subscription";
							$mail->Body = $msg;  //html body
							//$mail->AltBody = $content;  //plain text body
					
						$send_res=$mail->Send();
					}
		else
			{
			mail($admin_general_notification_email, "New subscription", $msg,$headers);
			}
		
		}
		 
}		 

if(isset($_REQUEST['conf']) && trim($_REQUEST['conf'])!="")
header("Location:".urldecode($_REQUEST['conf']));
else
header("Location:".$dirpath."confirm.html");
?>
