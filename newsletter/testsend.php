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

$camp_id=$_POST['id'];
$toemail=$_POST['toemail'];
?>
<?php
//error_reporting(0);
$dirpath="";
//include("config.inc.php");
//include("sel_camp_cron.php");

?>
<?php 

set_time_limit(0);
$invalid="";
$valid="";
include("admin.header.inc.php");
  include("class.Email.php");
error_reporting(1);
?>
<?php
$sql="select * from ".$table_prefix."email_advt_curr_run where id='$camp_id'";
$result=mysql_query($sql);
$row=mysql_fetch_row($result);
$lst=$row[3];
	 	$subject=$row[4];
		//echo "$lst";
						$disp_str="";
						$lnk=$web_prev_path."?id=$row[0]&e_id=0";
						if($enable_web_page_preview==1)
							{
							 $disp_str.="Email not displaying correctly ? <a href=\"$lnk\">View it in your browser</a><br>";
							}
							
						$content=$row[5];
						//$lnk=$unsub_email_path."?id=$row[0]"."&t=show&sendemail=$toemail";
						//echo $link."asasasd";
						//$content=str_replace("{WEBPREV-LINK}","#",$content);
						
						if($row[16]!=0)
							{
								$final_str=$mysql->echo_one("select content from ".$table_prefix."email_template where id='$row[16]'");
								$final_str=str_replace("{CONTENT}",$content,$final_str);
							}
						else
							{
								$final_str=$content;
							}
			
						$final_str=str_replace("{UNSUBSCRIBE-LINK}","#",$final_str);
						$final_str=str_replace("{EMAIL}",$toemail,$final_str);
						$final_str=replaceExtraParams($mysql,$table_prefix,"$defaultname",$final_str);

						 $Sender = $row[6]." <".$row[7].">";
						 $Recipiant =$toemail;
						 $Cc = ''; 
						 $Bcc = ''; 
			
						// $Subject = str_replace("{NAME}",$name,$row[4]);
						$Subject=replaceExtraParams($mysql,$table_prefix,"$defaultname",$row[4]);
			
						//** you can still specify custom headers as long as you use the constant
						//** 'EmailNewLine' to separate multiple headers.
			
						$CustomHeaders=  '';
					 //echo $CustomHeaders;
					// exit(0);
						//** create the new email message. All constructor parameters are optional and
						//** can be set later using the appropriate property.
			
						  $message = new Email($Recipiant, $Sender, $Subject, $CustomHeaders);
						  $message->Cc = $Cc; 
						  $message->Bcc = $Bcc; 
						// $text=$row[5];
						// $html=$row[5];
						//$content=$content;
						 if($row[8]=="1")  //html mail
						 {
						// echo $final_str;
						// exit(0);
							  $final_str= $disp_str.$final_str;
							  $html = $final_str;
							  
							  $alt_content=$row[17];
							  $alt_content=str_replace("{UNSUBSCRIBE-LINK}","#",$alt_content);
						$alt_content=str_replace("{EMAIL}",$toemail,$alt_content);
						$alt_content=replaceExtraParams($mysql,$table_prefix,"$defaultname",$alt_content);
							   $message->SetTextContent($alt_content);
							   $message->SetHtmlContent($html);
						 }
						 else
						 {
							$text = $final_str;
							$message->SetTextContent($text);
						 }
					//echo "hai";
	if($smtpmailer == 1)
			{
				if($flag_var==1)
						{
							$mail = new PHPMailer(true);
							//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
							$mail->IsSMTP();                                        // set mailer to use SMTP
							$mail->Host = $smtp_host; // specify SMTP mail server
							$mail->Port = $smtp_port; // specify SMTP Port
							$mail->SMTPAuth = $smtp_auth; // turn on SMTP authentication
							$mail->Username = $smtp_user; //Full SMTP username
							$mail->Password =$smtp_pass; //SMTP password
							$mail->CharSet = $charset_encoding;
							//if($smtp_secure )
							  $mail->SMTPSecure =$smtp_secure;                 // sets the prefix to the servier
			
							
							$mail->From = $row[7];
							$mail->FromName = $row[6];
							//$mail->Sender =$error_ret_mail;
							$mail->AddAddress($Recipiant);
							$mail->AddReplyTo($mail->From, $mail->FromName);
							
							//$mail->WordWrap = 50; //optional, you can delete this line
							
							if($row[8]=="1") 
								$mail->IsHTML(true); //set email format to HTML
							
							$mail->Subject = $Subject;
							$mail->Body = $final_str;  //html body
							 $alt_content=$row[17];
							  $alt_content=str_replace("{UNSUBSCRIBE-LINK}","#",$alt_content);
						$alt_content=str_replace("{EMAIL}",$toemail,$alt_content);
						$alt_content=replaceExtraParams($mysql,$table_prefix,"$defaultname",$alt_content);
							$mail->AltBody=$alt_content;
							//$mail->AltBody = $content;  //plain text body
						}
			}			
			
			
						$attres=mysql_query("select * from ".$table_prefix."ea_attachments where cid=$row[0]");
						while($at=mysql_fetch_row($attres))
						{
							//$filename = "attachments/$at[1]/$at[2]";
							 $pathToServerFile ="attachments/$at[1]/$at[2]";        //** attach this very PHP script.
							 $serverFileMimeType = 'application/octet-stream';  //** this PHP file is plain text.
				
							//** attach the given file to be sent with this message. ANy number of
							//** attachments can be associated with an email message. 
				
							//** NOTE: If the file path does not exist or cannot be read by PHP the file
							//** will not be sent with the email message.
				
							  $message->Attach($pathToServerFile, $serverFileMimeType);
							    if(($smtpmailer == 1) && ($flag_var==1))
							  {
								$mail->Addattachment($pathToServerFile);
							  }
						}
						
				//echo "hai";
		
						if($script_mode!="demo")
						{
						 	//echo "live";
							 $send_res=0;
				  if(($smtpmailer == 1) && ($flag_var==1))
					{$send_res=$mail->Send();}
				else
					{$send_res=$message->Send();}
				if($send_res ) 
							{
							echo "<br>Your mail was sent successfully. <a href=\"javascript:history.back(-1);\">Go Back</a><br>";
							}
							else
							echo "<br>Sending failed. <a href=\"javascript:history.back(-1);\">Go Back</a><br>";;
				
						}
						else
						{ 
								echo "<br>Cannot send test mail in demo. <a href=\"javascript:history.back(-1);\">Go Back</a><br>";;
						}
			
						


function replaceExtraParams($mysql,$table_prefix,$defaultname,$input)
{
	
		$name=$defaultname;
	$input=str_replace("{NAME}",$name,$input);
	
    $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		
			$val=$fielddetails[2];
		$input=str_replace($fielddetails[3],$val,$input);
	}	
	return $input;
}?>
<?php include("admin.footer.inc.php"); ?>