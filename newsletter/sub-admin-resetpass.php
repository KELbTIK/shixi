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
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to reset administrator password','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
	
	 ?>
	 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="create_new_sub_admin.php" >Create New  Administrator</a>&nbsp;|&nbsp; <a href="manage_sub_admins.php" >Manage  Administrators</a></td>
  </tr>
</table>
	<?php

	$uid=$_GET['id'];
	//$name=$mysql->echo_one();
		$result=mysql_query("select * from ".$table_prefix."subadmin_details where id='$uid'");
		if(mysql_num_rows($result)==1) 
		{
		
		$row=mysql_fetch_row($result);
		$username=$row[1];
		$email=$row[3];
		$oldpass=$row[2];
//		echo $oldpass;
		$newpass=substr($oldpass,0,7);
		//$newpass1=md5($newpass);
		$subject="Your Login Info.";
		//echo $newpass;
		mysql_query("update ".$table_prefix."subadmin_details set password='".md5($newpass)."' where id=$uid;");
				$from="Inout Mailing List Manager";
		$emailstring = <<<EOD

Hello,

Your login information is given below.
Username : $username
New Password : $newpass

Please login and change the the temporary password.

Thanks.
		
EOD;




			
			if($script_mode!="demo")
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
												$mail->AddAddress($email);
												$mail->AddReplyTo($mail->From, $mail->FromName);
												
												//$mail->WordWrap = 50; //optional, you can delete this line
												
												//$mail->IsHTML(true); //set email format to HTML
												
												$mail->Subject = $subject;
												$mail->Body = $emailstring;  //html body
												//$mail->AltBody = $content;  //plain text body
										
											$send_res=$mail->Send();
										}




		
									else {
								$send_res=mail("$email", "$subject", $emailstring,
								 "From: $from\r\n"
								."Reply-To: $from\r\n"
								."X-Mailer: PHP/" . phpversion());
								}
							if($send_res)
							{
								if($log_enabled==1)
									{
										mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Administrator password reset:".$username."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
									}
									echo "<br>Administrator password has been changed successfully.New password has been sent to his/her email.<br><br>";
							}
							else
							{
								echo "<span class=\"already\"><br>Can't send password now.Check the mail server configuration.</span><br><br>";
							}

			}
			else
			{
			echo "<span class=\"already\"><br>Can't reset password in demo.</span><br><br>";
			}

}
//echo $uid;

?>
<br>
<a href="manage_sub_admins.php">View All Admins</a> <br>
<br>
<?php include_once("admin.footer.inc.php"); ?>