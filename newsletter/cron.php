<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/

?><?php
//error_reporting(0);
$dirpath="";
include("config.inc.php");
//include("sel_camp_cron.php");

?>
<?php 

set_time_limit(0);
$invalid="";
$valid="";
$flag_var=0;
include("admin.header.inc.php");
  include("class.Email.php");
//Check if using the smtp mailing, if so include the class
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

if(isset($_REQUEST['id']))
{
	$idi=$_REQUEST['id'];
	if($idi=="")
		$idi=-1;
	if(!isValidAccess($idi,$CST_MLM_CAMPAIGN,$table_prefix,$mysql))
	{
		if($log_enabled==1)
		{
			$aid=getAdminId($mysql);
			$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
			$entityname=$mysql->echo_one("select cname from  ".$table_prefix."email_advt_curr_run where id=$idi");
			if($entityname!="")
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to fire the campaign $entityname(id:".$idi.")','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
	
		}
		//include_once("admin.header.inc.php");
		?>
		<br><span class="already">&nbsp;&nbsp;You dont have access to this campaign.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
		<?php
		include_once("admin.footer.inc.php");
		exit(0);
	}
		$sql="select * from ".$table_prefix."email_advt_curr_run where id=$_REQUEST[id] and  status=1";

}
else
{
	if(isset($_COOKIE['inout_sub_admin']))
	{
		$subAdminId=getAdminId($mysql);

		$sql="SELECT a.* FROM ".$table_prefix."email_advt_curr_run a inner join 
		( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
		on a.id=b.cid and  a.status=1 group by a.id  order by a.id desc ";
		//echo $sql;
	}
	else
	{
		$sql="select * from ".$table_prefix."email_advt_curr_run where status=1";
	}
}
$result=mysql_query($sql);




?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="batchinfo.php"> Manage Email Queue</a> | <a href="sendmails.php"> Select Campaign and Send</a> | <a href="cron.php">Execute all Campaigns</a></td>
  </tr>
</table><br />
<?php 

function replaceExtraParams($mysql,$table_prefix,$defaultname,$input,$emailid)
{
	//echo "select value from ".$table_prefix."ea_extraparam where  eid=$emailid and name='name'";
//	if($mysql->total("".$table_prefix."ea_extraparam","name='name' and eid=$emailid")!=0)
		$name=  $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where  eid=$emailid and name='name'");
	if($name=="")
		$name=$defaultname;
	$input=str_replace("{NAME}",$name,$input);
    $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		//echo "select value from ".$table_prefix."ea_extraparam where eid=$emailid  and name='$fielddetails[1]'";
		//if($mysql->total("".$table_prefix."ea_extraparam","name='$fielddetails[1]' and eid=$emailid")!=0)
		$val=  $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid=$emailid  and name='$fielddetails[1]'");
		if($val=="")
			$val=$fielddetails[2];
		$input=str_replace($fielddetails[3],$val,$input);
	}	
	return $input;
}

$activeCampaignCount = $mysql->total("".$table_prefix."email_advt_curr_run","status=1"); 
if($activeCampaignCount>0)
{

	if($script_mode=="demo")
	{
	?>
	<span class="error"><br>Email sending is disabled in demo version due to security problems. Only statistics are updated. <br><br></span><br>
	<?php
	}
	while($row=mysql_fetch_row($result))   //fetching campaigns
	{ 
		$i=0;
		$lst=$row[3];
	 	$subject=$row[4];
		//echo "$lst";
		$idtop=$row[0];
		/*if($idtop=="")
			$idtop=-1;
		if(!isValidAccess($idtop,$CST_MLM_CAMPAIGN,$table_prefix,$mysql))
			{
					$entityname=$mysql->echo_one("select cname from  ".$table_prefix."email_advt_curr_run where id=$idtop");

				//include_once("admin.header.inc.php");
				//$n=$_REQUEST['ListName'.$i];
				 if($invalid=="")
					$invalid.="[".$entityname."(id:".$idtop.")";
					 else	
					$invalid.=", ".$entityname."(id:".$idtop.")";
					
			}
		else
		{*/
?>
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0" class="box">
        <tr>
          <td>&nbsp;</td>
          <td>Campaign - <?php echo $subject; ?> ,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<<< Fired List : <?php 
		$ctid=""; 
		if($mysql->total("".$table_prefix."ea_cnc","campid=$row[0]")==0) 
		{
			echo "All Emails";
			$subAdminId=$mysql->echo_one("select aid from ".$table_prefix."campaign_access_control where cid=$row[0] and access_status=1");
			if($subAdminId!="")//isset($_COOKIE['inout_sub_admin']))
			{		
				$res=mysql_query("SELECT  a.*,b.* FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
				( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
				 where a.unsubstatus=0 and b.unsubstatus=0 and  a.id= b.eid and b.id>$lst group by a.id order by b.id asc");
				 
				 $rlt=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
				while($ro=mysql_fetch_row($rlt))
				{
					if($ctid=="")
						$ctid.=$ro[0];
					else
						$ctid.=",".$ro[0];
				}
			}
			else
			{	
			  $res=mysql_query("select * from ".$table_prefix."email_advt where id>$lst and unsubstatus=0 order by id asc;");
			  $ctid=0;
			} 
		}
		else
		{
			 $cid=$mysql->echo_one("select catid from ".$table_prefix."ea_cnc where campid=$idtop"); //echo $cid;
			 $res=mysql_query("select * from ".$table_prefix."email_advt,".$table_prefix."ea_em_n_cat where ".$table_prefix."email_advt.id=".$table_prefix."ea_em_n_cat.eid and ".$table_prefix."ea_em_n_cat.id>$lst and ".$table_prefix."ea_em_n_cat.cid=$cid and ".$table_prefix."email_advt.unsubstatus=0 and ".$table_prefix."ea_em_n_cat.unsubstatus=0 order by ".$table_prefix."ea_em_n_cat.id asc;");
			 $ctid=$cid;
			echo $mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$cid");

		}// echo mysql_num_rows($res);
	    //$i=0;
		  
		  ?> >>></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        
	   <?php
	   while($r=mysql_fetch_row($res))  //fetching emails  for a campaign
	   {	
	   
	   		$fire_campaign=1;
	   		if($row[13]!="")
			{
				//echo "select value from  ".$table_prefix."ea_extraparam  where name='$row[13]' and eid=$r[0] and  value $row[14] '$row[15]'";
				$rule_name1=$mysql->echo_one("select value from  ".$table_prefix."ea_extraparam  where name='$row[13]' and eid=$r[0] and  value $row[14] '$row[15]'");
				if($rule_name1=="")
				{
				$fire_campaign=0;
				}
				
			}	
		    if(1==$fire_campaign)
			{
				
						//$web-send-path="test";
						
						
						$disp_str="";
						$final_str="";
						$lnk=$web_prev_path."?id=$row[0]"."&e_id=".$r[0];
						if($enable_web_page_preview==1)
							{
							 $disp_str.="Email not displaying correctly ? <a href=\"$lnk\">View it in your browser</a><br>";
							}
							$content=$row[5];
						
						$lnk=$unsub_email_path."?id=$r[0]"."&t=".$r[3]."&cid=$ctid";

						if($row[16]!=0)
							{
								$final_str=$mysql->echo_one("select content from ".$table_prefix."email_template where id='$row[16]'");
								$final_str=str_replace("{CONTENT}",$content,$final_str);
							}
						else
							{
								$final_str=$content;
							}

	
							$final_str=str_replace("{UNSUBSCRIBE-LINK}",$lnk,$final_str);
						$final_str=str_replace("{EMAIL}",$r[1],$final_str);
						$final_str=replaceExtraParams($mysql,$table_prefix,"$defaultname",$final_str,$r[0]);


		
						 $Sender = $row[6]." <".$row[7].">";
						 $Recipiant = $r[1]; 
						 $Cc = ''; 
						 $Bcc = ''; 
			
						$Subject=replaceExtraParams($mysql,$table_prefix,"$defaultname",$row[4],$r[0]);
			
						$CustomHeaders='' ;
			
						//** create the new email message. All constructor parameters are optional and
						//** can be set later using the appropriate property.
			
						  $message = new Email($Recipiant, $Sender, $Subject, $CustomHeaders);
						  $message->Cc = $Cc; 
						  $message->Bcc = $Bcc; 

						 if($row[8]=="1")  //html mail
						 {
						 	   $final_str= $disp_str.$final_str;
							  
							  $alt_content=$row[17];
							  $alt_content=str_replace("{UNSUBSCRIBE-LINK}",$lnk,$alt_content);
							  $alt_content=str_replace("{EMAIL}",$toemail,$alt_content);
 		        			  $alt_content=replaceExtraParams($mysql,$table_prefix,"$defaultname",$alt_content,$r[0]);
							  $message->SetTextContent($alt_content);
								$message->SetHtmlContent($final_str);		
						 }
						 else
						 {
							$message->SetTextContent($final_str);
						 }

			if($smtpmailer == 1 && $flag_var==1)
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
							$alt_content=$row[17];
							$alt_content=str_replace("{UNSUBSCRIBE-LINK}",$lnk,$alt_content);
							$alt_content=str_replace("{EMAIL}",$toemail,$alt_content);
 		        			$alt_content=replaceExtraParams($mysql,$table_prefix,"$defaultname",$alt_content,$r[0]);
							$mail->AltBody=$alt_content;
							$mail->Body = $final_str;  //html body
							//$mail->AltBody = $content;  //plain text body
						}
						
						$attres=mysql_query("select * from ".$table_prefix."ea_attachments where cid=$row[0]");
						while($at=mysql_fetch_row($attres))
						{
							//$filename = "attachments/$at[1]/$at[2]";
							 $pathToServerFile ="attachments/$at[1]/$at[2]";        //** attach this very PHP script.
							$serverFileMimeType = 'application/octet-stream'; //** this PHP file is plain text.
				
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
						
						
						if($script_mode!="demo")
						{
						
				
				 //	echo "live";
							$send_res=0;
							if(($smtpmailer == 1) && ($flag_var==1))
								{
								try
								{
								$send_res=$mail->Send();
								}
								catch(Exception $e)
								{
								 //let loop continue;
								}
								}
							else
								{$send_res=$message->Send();}
							
								?>
								<tr>
							  <td>&nbsp;</td>
							 <td><?php echo ($i+1)."&nbsp;&nbsp;  $r[1] "; 
							 if($send_res ==0 ) 
							{
								echo "=> Failed";
							}?></td>
							  <td>&nbsp;</td>
							   </tr><?php 
							
				
						}
						else
						{ 
						  ?>
							<tr>
						 <td>&nbsp;</td>
						 <td><?php echo ($i+1)."&nbsp;&nbsp;  $r[1] "; ?></td>
						  <td>&nbsp;</td>
						   </tr><?php
						}
						sleep($time_interval);
						mysql_query("update ".$table_prefix."email_advt_curr_run set sent=sent+1 where id=$row[0]");
			
						 $i+=1;
			 
					if($mysql->total("".$table_prefix."ea_cnc","campid=$row[0]")==0) 
					{
						$subAdminId=$mysql->echo_one("select aid from ".$table_prefix."campaign_access_control where cid=$row[0] and access_status=1");
						if($subAdminId!="")//isset($_COOKIE['inout_sub_admin']))
							$lst=$r[4];
						else
							$lst=$r[0];
					}		
					else
							$lst=$r[4];
					 if($i==$row[2])
					 {
						 mysql_query("update ".$table_prefix."email_advt_curr_run set lastid='$lst' where id=$idtop");
						 break; 
						 
					 } 
			 }
	  
		}//end of  fetching emails for a campaign
	
		if($log_enabled==1)
		{
			$entityname=$mysql->echo_one("select cname from  ".$table_prefix."email_advt_curr_run where id=$idtop");
			if($valid=="")
				$valid.="[".$entityname."(id:".$idtop.")";
			 else	
				$valid.=", ".$entityname."(id:".$idtop.")";
		}
			
		if($i<$row[2])
		{ 
			mysql_query("update ".$table_prefix."email_advt_curr_run set lastid='$lst' where id=$idtop");
		}
		if($i==0)
		{
			?>
				<tr>
			  <td>&nbsp;</td>
			  <td><?php 		 echo "<br>All emails have been sent! You can inactivate this campaign if you need";
			 ?></td>
			  <td>&nbsp;</td>
			</tr>
			<?php
		 }

		 ?>
		</table> <br>
		<?php
		//}
	}//end of fetching campaigns
	/*if($invalid!="")
	{
		$invalid.="]";
		if($log_enabled==1)
		{
			$aid=getAdminId($mysql);
			$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to fire campaign(s) $invalid ','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
	
		}
		echo "<br><span class=\"already\">You dont have access to fire  campaign(s) $invalid.</span><br><br>";
	}*/
	if($valid!="")
	{		
		$valid.="]";
		$aid=0;
		if(isset($_COOKIE['inout_sub_admin']))
		{
			$aid=getAdminId($mysql);
		}
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','Campaign(s) fired:".$valid."','".time()."','$CST_MLM_CAMPAIGN')");
	}
}
else
{
	echo "<br>-No Active Campaigns-<br><br>";
}
include("admin.footer.inc.php");

?>
