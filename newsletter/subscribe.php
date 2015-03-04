<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



$already_added_list="";
$newly_added_list="";
$cat_name_arr=array();
ob_start();
include_once("config.inc.php"); 
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
error_reporting(1);
$emails=trim($_REQUEST['email']);
phpSafe($emails);

if(isset($_REQUEST['cid']))
{
$cid=$_REQUEST['cid'];
}
else
{
$cid="";
$i=0;
//print_r($_POST);
$ct=$mysql->echo_one("select count(*) from `".$table_prefix."email_advt_category` ");

while($i<$ct)
{
	if(isset($_POST['chb'.$i]))
	{
	//echo $_POST['chb'.$i];
	 $cid.="".$_POST['chb'.$i].",";
	}

	$i++;
}
if($cid!="")
	$cid = substr($cid, 0, -1);
}
//echo $cid;	
//exit(0);    
if(isset($_POST['radiobutton'])) 
{
	if($_POST['radiobutton']=="1")
	{  
   			$id=$mysql->echo_one("select id from`".$table_prefix."email_advt` where email='$emails'");
		 	//$catId  = $_REQUEST['cid'];
			$catId  = $cid;
		 	$catlist="";
			$catIdArr = explode(",",$catId);
			$cnt=count($catIdArr);
			for($i=0;$i<$cnt;$i++)
			{
					$update_res=mysql_query("update ".$table_prefix."ea_em_n_cat set unsubstatus=1 where eid='$id' AND cid='$catIdArr[$i]'");
					if(mysql_affected_rows()>0)	
						$catlist.=" ".$mysql->echo_one("select name from`".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").",";
					else
						$already_added_list.=" ".$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").",";
					
			 }
			 if($already_added_list!="")
				 $already_added_list=trim(substr($already_added_list,0,strrpos($already_added_list,",")));
			 if($catlist!="")
			 {
				  $newly_added_list=$catlist=trim(substr($catlist,0,strrpos($catlist,",")));
				  if($log_enabled==1)
				  {
					 mysql_query("insert into ".$table_prefix."admin_log_info values('','-1','$emails unsubscribed(html) from $catlist','".time()."',$CST_MLM_SUBSCRIPTION)");
					 echo mysql_error();


					$msg ="
Hello,

The following user has unsubscribed from the list(s) \"$catlist\" via HTML subscribe/unsubscribe code.

Email		 	: $emails

Regards,
Inout Mailing List Manager

";

					$headers  = "";
					$headers .= "From: {$admin_general_notification_email}\n";
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
					$headers .= "Content-Transfer-Encoding: 8bit\n";
					if($mysql->total("".$table_prefix."email_advt","email='$emails' and unsubstatus=0")>0)
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

				  }
			 }
	}
	else
	{
		subscribeToList($emails,$mysql,$table_prefix,$CST_MLM_SUBSCRIPTION,$log_enabled,$defaultname,$confirm_subscription,$dirpath,$cid);
 	}
}
else
{
		subscribeToList($emails,$mysql,$table_prefix,$CST_MLM_SUBSCRIPTION,$log_enabled,$defaultname,$confirm_subscription,$dirpath,$cid);
}

function subscribeToList($emails,$mysql,$table_prefix,$CST_MLM_SUBSCRIPTION,$log_enabled,$defaultname,$confirm_subscription,$dirpath,$cid)
{
global $admin_general_notification_email,$already_added_list,$newly_added_list;
  $catlist="";
  $sendmail=0;	
  $confirm=$confirm_subscription; // get value from configuration file.
  if($emails!="" && is_valid_email($emails))
     {
		if($mysql->total("".$table_prefix."email_advt","email='$emails'")==0)
		{
			$ti=time();
			mysql_query("INSERT INTO `".$table_prefix."email_advt` ( `id` , `email` , `unsubstatus` , `time` )VALUES ('', '$emails', '$confirm', '".$ti."');");

			$roww=$mysql->select_last_row("".$table_prefix."email_advt","id");
			$id=$roww[0];
			if($confirm==1)
			{
				 //sendConfirmationMail ($defaultname,$id,$emails,$dirpath,$ti,$cid);
				 $sendmail=1;	
			}
			$roww=$mysql->select_last_row("".$table_prefix."email_advt","id");
			$id=$roww[0];
			if(isset($_POST['name']))
			{
				$var=trim($_POST['name']);
				if($var!="")
					mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$var');");
			}
			$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
			while($fielddetails=mysql_fetch_row($extrafields))
			{
				$reqParamName="extra_personal_info".$fielddetails[0];
				if(isset($_POST[$reqParamName]))
				{
					$var=trim($_POST[$reqParamName]);
					phpSafe($var);
					if($var!="")
					{
						mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$var');");
					}
				}
			}		
		}
		else
		{
			//check whether unsubscribed; if so make status subscribed
			$id=$mysql->echo_one("select id from`".$table_prefix."email_advt` where email='$emails'");
			//$ti=$mysql->echo_one("select time from`".$table_prefix."email_advt` where email='$emails'");
			$ti=time();
			if($mysql->total("".$table_prefix."email_advt","id='$id' and unsubstatus='1'")!=0)
				if($confirm!=1)
					mysql_query("update `".$table_prefix."email_advt` set unsubstatus='0',time='$ti' where id='$id'");
				else
					{
						mysql_query("update `".$table_prefix."email_advt` set  time='$ti' where id='$id'");
				  //sendConfirmationMail ($defaultname,$id,$emails,$dirpath,$ti,$cid);
						  $sendmail=1;	
					}
			if(isset($_POST['name']))
			{
					$var=trim($_POST['name']);
					if(str_replace(" ","",$var)=="")
						{
							mysql_query("delete from ".$table_prefix."ea_extraparam where eid='$id' and name='name'");
						}
					else
						{
									if($mysql->total("".$table_prefix."ea_extraparam","eid='$id' AND name='name'")==0)
										{
											mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$var');");
										 }
									else
										  {
											mysql_query("update `".$table_prefix."ea_extraparam` set value='$var' where eid='$id' AND name='name'");
										  }
						}
			}
				
		   $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
			while($fielddetails=mysql_fetch_row($extrafields))
				{
						$reqParamName="extra_personal_info".$fielddetails[0];
						if(isset($_POST[$reqParamName]))
						{
							$var=trim($_POST[$reqParamName]);	
							phpSafe($var);		
							if(str_replace(" ","",$var)=="")
							{
								mysql_query("delete from ".$table_prefix."ea_extraparam where eid='$id' and name='$fielddetails[1]'");
							}						  
							else
							{  
							   if($mysql->total("".$table_prefix."ea_extraparam","eid='$id' and name='$fielddetails[1]'")==0)
								{
									mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$var')");
								}
								else
								{
									mysql_query("update ".$table_prefix."ea_extraparam set value ='$var' where eid='$id' and name='$fielddetails[1]' ");
								}
							}
						}	
				  }	

		 }
//		$catId  = $_GET['cid'];
		$catId  = $cid;
		$catIdArr = explode(",",$catId);
		$cnt=count($catIdArr);
		for($i=0;$i<$cnt;$i++)
		{
			$curr_stat_res=mysql_query("select id,unsubstatus from ".$table_prefix."ea_em_n_cat where cid='$catIdArr[$i]' and eid='$id'");;
			if(mysql_num_rows($curr_stat_res)>0)
			{
				$curr_stat_row=mysql_fetch_row($curr_stat_res);
			}
			//print_r($curr_stat_row);die;
			if(mysql_num_rows($curr_stat_res)==0)
			{
				//echo "1";die;
				$catlist.=" ".$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").",";
				
				mysql_query("insert into ".$table_prefix."ea_em_n_cat values('','$id','$catIdArr[$i]',$confirm,$ti)");
				if($confirm==1)
					$sendmail=1;	
				
			}
			elseif($curr_stat_row[1]==1)
			{
				//echo "2";die;
				$catlist.=" ".$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").",";
				if($confirm!=1)
					mysql_query("update `".$table_prefix."ea_em_n_cat` set unsubstatus='0',time='$ti' where eid='$id' and cid=$catIdArr[$i]");
				else
				{
					mysql_query("update `".$table_prefix."ea_em_n_cat` set time='$ti' where eid='$id' and cid=$catIdArr[$i]");
					$sendmail=1;	
				}
			}
			else
			{
				$already_added_list.=" ".$mysql->echo_one("select name from `".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").",";

			}
		 }
		 if($already_added_list!="")
				 $already_added_list=trim(substr($already_added_list,0,strrpos($already_added_list,",")));
		 if($catlist!="")
			 {
				 $catlist=trim(substr($catlist,0,strrpos($catlist,",")));
				 $newly_added_list=$catlist;
				 if($log_enabled==1)
				 {
					  mysql_query("insert into ".$table_prefix."admin_log_info values('','-1','$emails subscribed(html) to $catlist','".time()."','$CST_MLM_SUBSCRIPTION')");
				 }		  
				echo mysql_error();

				if($confirm!=1)
				{
				$msg ="
	
Hello,

The following user has subscribed to the list(s) \"$catlist\".

Email		 	: $emails

Login to the admin area to see his details.

Regards,
Inout Mailing List Manager";

	
				$headers  = "";
				$headers .= "From: {$admin_general_notification_email}\n";
				$headers .= "MIME-Version: 1.0\n";
				$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
				$headers .= "Content-Transfer-Encoding: 8bit\n";
				if(trim($admin_general_notification_email)!="" )
					{
					 global $smtpmailer;

					global $smtp_host;
					
					global $smtp_port;
					
					global $smtp_auth;
					
					global $smtp_user;
					
					global $smtp_pass;
					
					global $smtp_secure;
					
					global $flag_var;
					
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
				

			}
		if($sendmail==1)
			sendConfirmationMail ($defaultname,$id,$emails,$dirpath,$ti,$cid);		
	}

}

function sendConfirmationMail ($defaultname,$id,$emails,$dirpath,$ti,$cid)
{
		include_once("confirm.inc.php");
		global $admin_general_notification_email;
		if(isset($_POST['name']))
		{
			$var=trim($_POST['name']);
			if($var!="")
				$text=str_replace("{NAME}","$var",$text);
			else	
				$text=str_replace("{NAME}","$defaultname",$text);
		}
		else
			$text=str_replace("{NAME}","$defaultname",$text);
		
		$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
		while($fielddetails=mysql_fetch_row($extrafields))
		{
			$reqParamName="extra_personal_info".$fielddetails[0];
			if(isset($_POST[$reqParamName]))
			{
				$var=trim($_POST[$reqParamName]);
				if($var!="")
					$text=str_replace($fielddetails[3],"$var",$text);
				else
					$text=str_replace($fielddetails[3],"$fielddetails[2]",$text);
			}
			else
				$text=str_replace($fielddetails[3],"$fielddetails[2]",$text);
		}
$conf_str="";	
if(isset($_POST['confirm']) && trim($_POST['confirm'])!="")
$conf_str="&conf=".$_POST['confirm'];

		$text=str_replace("{CONFIRM-LINK}",$dirpath."confirmnow.php?id=$id"."&t=$ti"."&cid=".$cid.$conf_str,$text);
			//echo $text;	exit(0);				

		$Recipiant = $emails; 
		$Cc = ''; 
		$Bcc = ''; 
	 global $smtpmailer;

					global $smtp_host;
					
					global $smtp_port;
					
					global $smtp_user;
					
					global $smtp_pass;
					
					global $smtp_auth;
					
					global $smtp_secure;
					
					global $flag_var;
					
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
							$mail->AddAddress($Recipiant);
							$mail->AddReplyTo($mail->From, $mail->FromName);
							
							//$mail->WordWrap = 50; //optional, you can delete this line
							
							//$mail->IsHTML(true); //set email format to HTML
							
							$mail->Subject = $Subject;
							$mail->Body = $text;  //html body
							//$mail->AltBody = $content;  //plain text body
					
						$send_res=$mail->Send();
					}
				else
						{
						mail($Recipiant, $Subject, $text,      "From: $Sender\r\n"    ."Reply-To: $Sender\r\n"  );
						
					}
				
}		

ob_clean();

//echo $CST_MLM_SUBSCRIPTION;
if(isset($_POST['radiobutton'])) 
{
	if($_POST['radiobutton']=="1")
	{  
		if(isset($_POST['redirect1']))
		{
			header("Location:".urldecode($_POST['redirect1']).getUrlAppendChar(urldecode($_POST['redirect1']))."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
		else if(isset($_POST['redir1']))
		{
		header("Location:".urldecode($_POST['redir1']).getUrlAppendChar(urldecode($_POST['redir1']))."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
		
		else
		{
			header("Location:".$unsubokpath.getUrlAppendChar($unsubokpath)."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
	}
	else
	{
	if(isset($_POST['redirect']))
		{
			header("Location:".urldecode($_POST['redirect']).getUrlAppendChar(urldecode($_POST['redirect']))."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
		else if(isset($_POST['redir']))
		{
		header("Location:".urldecode($_POST['redir']).getUrlAppendChar(urldecode($_POST['redir']))."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
		
		else
		{
			header("Location:".$subokpath.getUrlAppendChar($subokpath)."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
	}
}
else
{
	if(isset($_POST['redirect']))
		{
			header("Location:".urldecode($_POST['redirect']).getUrlAppendChar(urldecode($_POST['redirect']))."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
		else if(isset($_POST['redir']))
		{
		header("Location:".urldecode($_POST['redir']).getUrlAppendChar(urldecode($_POST['redir']))."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
		
		else
		{
			header("Location:".$subokpath.getUrlAppendChar($subokpath)."already=".urlencode(trim($already_added_list))."&new=".urlencode(trim($newly_added_list)));
		}
}

function getUrlAppendChar($url)
{
if(strpos($url,"?")>0)
return "&";
else
return "?";
}		
ob_flush();
exit(0);
?>