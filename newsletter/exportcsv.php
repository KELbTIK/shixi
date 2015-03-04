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
?>

<style type="text/css">
<!--
.style1 {
	color: #999999
}
-->
</style>

<?php

if(isset($_POST['Emailnolist']) || isset($_POST['Allemails']))
{
	if($script_mode=="demo")
	{?>
<span class="info">You cannot export all emails/emails which are not in
a list in demo. </span>
<br>
<br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
	}
}

$rems_batch_count=1;
$rems_curr_batch=1;
$batch_size=1000;
if(isset($_POST['curr_batch']))
$rems_curr_batch=$_POST['curr_batch']+1;
?>

<table width="779" border="0" align="center" cellpadding="0"
	cellspacing="0">
	<tr align="center">
		<td height="30" colspan="2" scope="row"><a href="export_emails.php">Export
		as IEF</a> | <a href="export_emails_csv.php">Export as CSV</a></td>
	</tr>
	<tr align="center">
		<td colspan="2" scope="row"></td>
	</tr>
	<tr align="left">
		<td width="18" height="41" scope="row"><span class="style1"> </span> <br>
		<br>
		</td>
		<td width="761" height="41" scope="row"><?php		
		$show="";
		$invalid="";
		$valid="";
		$day=date("l_dS_F_Y_h_i_A");
		if(isset($_POST['dayname']) && $_POST['dayname']!="")
			$day=$_POST['dayname'];
		//echo $day;
		$i=0;
		$completedcnt=0;
		$selectedcnt=0;
		if(trim($_POST['hf3'])=="")
		$extraparam=array();
		else
		$extraparam=explode(',',trim($_POST['hf3']));

		$resultstring="";

		$getListSql="select * from ".$table_prefix."email_advt_category order by name";
		if(isset($_COOKIE['inout_sub_admin']))
		{
			$subAdminId=getAdminId($mysql);
			$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.name";
		}

		$result=mysql_query($getListSql);
		while($row=mysql_fetch_row($result))
		{

			if(isset($_POST[ "List".$i]) )
			{	
				$selectedcnt++;
				$lid=$_POST[ "List".$i];
				$name=$_REQUEST['ListName'.$i];
				if(!isValidAccess($lid,$CST_MLM_LIST,$table_prefix,$mysql))
				{
					$n=$_REQUEST['ListName'.$i];
					if($invalid=="")
					$invalid.="[".$n;
					else
					$invalid.=", ".$n;
				}
	
				$aid=0;
				if(isset($_COOKIE['inout_sub_admin']))
				{
					$aid=getAdminId($mysql);
				}
				if($log_enabled==1)
				{
					if($valid=="")
					$valid.="[".$name;
					else
					$valid.=", ".$name;
				}
					$str="";
					$status=$_POST['status'.$i];
					if($status==2)
					$str="and b.unsubstatus=0";
					if($status==3)
					$str="and b.unsubstatus=1";
					$rems_count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where a.id=b.eid and b.cid=$lid $str ");
				
				//if($mysql->total($table_prefix."ea_em_n_cat","cid=$lid")==0)
				if($rems_count==0)
				{
							
					if($show=="")
					$show.="[".$name;
					else
					$show.=", ".$name;
					
				}
	
				$completed=$_POST[ "completed".$i];
	
				if($completed==1 && $rems_count>0)
				{
					$listname=str_replace(" ","_",$name);
					$resultstring=$resultstring."<a href=\"csv/$day/$listname.csv\">$name.csv</a><br><br>";
				}
				if($completed==1 || $rems_count==0 ||!isValidAccess($lid,$CST_MLM_LIST,$table_prefix,$mysql))
				{
					//$listname=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$row[0]");
					$completedcnt++;
				}
				else
				{
					break;
				}

			}
			$i++;

		}
		$selectedlistcount=$selectedcnt;
		$completedlistcount=$completedcnt;
		if(isset($_POST['Emailnolist']))
		{
			$selectedcnt++;
			if(isset($_COOKIE['inout_sub_admin']))
			{
				if($invalid=="")
				$invalid.="[emails not in any list";
				else
				$invalid.=", emails not in any list";


				//$aid=getAdminId($mysql);
				//$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				//$entityname="emails not in any list";
				//mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to export $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

			}
			else
			{ 
					if($log_enabled==1)
					{
						if($valid=="")
						$valid.="[emails not in any list";
						else
						$valid.=", emails not in any list";
					}
			}
			
			$status=$_POST['status'];

			$str1="";
			if($status==2)
			$str1="and unsubstatus=0";
			if($status==3)
			$str1="and unsubstatus=1";

			$str="";
			$result4=mysql_query("select distinct(eid) from ".$table_prefix."ea_em_n_cat ");
			while($row4=mysql_fetch_row($result4))
			{
				$str=$str."'".$row4[0]."',";
			}
			$str=$str."'0'";
			$result5=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt where id NOT IN(".$str.") $str1");
							
			if($result5==0)
			{
					if($show=="")
					$show.="[emails not in any list";
					else
					$show.=", emails not in any list";
				
			}			
			$completed=$_POST[ "completed"];
			
				if($completed==1&& $result5>0)
				{
					$resultstring=$resultstring."<a href=\"csv/$day/Emails_Not_in_List.csv\">Emails_Not_in_List.csv</a><br><br>";
				}
			if($completed==1 || $result5==0 ||isset($_COOKIE['inout_sub_admin']))
				$completedcnt++;
		}
		
		if(isset($_POST['Allemails']))
		{
			$selectedcnt++;
			if(isset($_COOKIE['inout_sub_admin']))
			{
				if($invalid=="")
				$invalid.="[all emails in system";
				else
				$invalid.=", all emails in system";


				//$aid=getAdminId($mysql);
				//$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				//$entityname="emails not in any list";
				//mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to export $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

			}
			else
			{ 
					if($log_enabled==1)
					{
						if($valid=="")
						$valid.="[all emails in system";
						else
						$valid.=", all emails in system";
					}
			}
			
			$status=$_POST['allstatus'];

			$str1="";
			if($status==2)
			$str1="where unsubstatus=0";
			if($status==3)
			$str1="where unsubstatus=1";


			$result5=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt $str1 ");
			
			if($result5==0)
			{
					if($show=="")
					$show.="[all emails in system";
					else
					$show.=", all emails in system";
				
			}
			$completed=$_POST[ "allcompleted"];
						if($completed==1 && $result5>0)
				{
					$resultstring=$resultstring."<a href=\"csv/$day/All_Emails_in_System.csv\">All_Emails_in_System.csv</a><br><br>";
				}
			if($completed==1 ||  $result5==0 || isset($_COOKIE['inout_sub_admin']))
				$completedcnt++;
		}
		
		if($completedcnt<$selectedcnt)
		{
			mysql_data_seek($result,0);
			$i=0;
			while($row=mysql_fetch_row($result))
			{
				$completed=$_POST[ "completed".$i];
				if(isset($_POST[ "List".$i]) && $completed!=1)
				{
					$lid=$_POST[ "List".$i];
					if(isValidAccess($lid,$CST_MLM_LIST,$table_prefix,$mysql))
					{
						$status=$_POST["status".$i];
						$str="";
						if($status==2)
						$str="and b.unsubstatus=0";
						if($status==3)
						$str="and b.unsubstatus=1";
						$rems_count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where a.id=b.eid and b.cid=$row[0] $str ");
						//if($mysql->total($table_prefix."ea_em_n_cat","cid=$lid")>0)
						if($rems_count>0)
						{
							$name=$_REQUEST[ 'ListName'.$i];
							$name=str_replace(" ","_",$name);
							if($rems_curr_batch==1)
							{
								if(!is_dir("csv/$day/"))
									mkdir("csv/$day/",0777);
								$handle = fopen ("csv/$day/$name.csv", "wb");
								$dayname=$day;
							}
							else
							{
								
								$dayname=$day;
								$handle = fopen ("csv/$day/$name.csv", "ab");
							}
							$listname=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$row[0]");
							if(isset($_POST[ "Email".$i]))
							{
								//$rems_count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where a.id=b.eid and b.cid=$row[0] $str ");
								if($rems_count>$batch_size)
								{
									$rems_batch_count=ceil($rems_count/$batch_size);
									$startlimit=($rems_curr_batch-1)*$batch_size;
									//$extension.=" (batch $rems_curr_batch of $rems_batch_count) ";
									$temp=$startlimit+$batch_size;
									if($rems_curr_batch == $rems_batch_count)
									$temp=$rems_count;
									echo "<br><b>Exporting $listname : $startlimit to $temp of $rems_count.<blink> <span class=\"already\">Please wait...</span> </blink></b><br><br>";
								}
								else
								{
									$startlimit=0;
									echo "<br><b>Exporting $listname : 0 to $rems_count of $rems_count.<blink> <span class=\"already\">Please wait...</span> </blink></b><br><br>";
								}
								$endlimit=$batch_size;
								$result1=mysql_query("select a.email,b.eid from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where a.id=b.eid and b.cid=$row[0] $str order by a.id desc limit $startlimit,$endlimit");
								writeEmailsToFile($result1,$handle,$table_prefix,$extraparam,$mysql,$rems_curr_batch);
	
								if($rems_curr_batch == $rems_batch_count)
								{
									$current_completed_id=$row[0];
									$rems_curr_batch=0;
	
								}
							}
							fclose($handle);
							break;
						}
						else
						{
									$current_completed_id=$lid;
									$rems_curr_batch=0;

						}
					}
				}
				$i+=1;
			}
	
			if($selectedlistcount==$completedlistcount)
			{
				if(isset($_POST['Emailnolist']) && $_POST[ 'completed']==0 )
				{
					$status=$_POST['status'];
		
					$str1="";
					if($status==2)
					$str1="and unsubstatus=0";
					if($status==3)
					$str1="and unsubstatus=1";
		
					$str="";
					$result4=mysql_query("select distinct(eid) from ".$table_prefix."ea_em_n_cat ");
					while($row4=mysql_fetch_row($result4))
					{
						$str=$str."'".$row4[0]."',";
					}
					$str=$str."'0'";
					if(!isset($_COOKIE['inout_sub_admin']))
					{
						$rems_count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt where id NOT IN(".$str.") $str1 ");
						if($rems_count>0)
						{
						
								if($rems_curr_batch==1)
								{
									if(!is_dir("csv/$day/"))
										mkdir("csv/$day/",0777);
									$handle = fopen ("csv/$day/Emails_Not_in_List.csv", "wb");
									$dayname=$day;
								}
								else
								{
									
									$dayname=$day;
									
									$handle = fopen ("csv/$day/Emails_Not_in_List.csv", "ab");
								}
					
								if($rems_count>$batch_size)
											{
												$rems_batch_count=ceil($rems_count/$batch_size);
												$startlimit=($rems_curr_batch-1)*$batch_size;
												//$extension.=" (batch $rems_curr_batch of $rems_batch_count) ";
												$temp=$startlimit+$batch_size;
												if($rems_curr_batch == $rems_batch_count)
												$temp=$rems_count;
												echo "<br><b>Exporting 'emails not in any list' : $startlimit to $temp of $rems_count.<blink> <span class=\"already\">Please wait...</span> </blink></b><br><br>";
											}
											else
											{
												$startlimit=0;
												echo "<br><b>Exporting 'emails not in any list' : 0 to $rems_count of $rems_count.<blink> <span class=\"already\">Please wait...</span> </blink></b><br><br>";
											}
											$endlimit=$batch_size;
								$result5=mysql_query("select email,id from ".$table_prefix."email_advt where id NOT IN(".$str.") $str1 limit $startlimit,$endlimit");
								
									// fwrite($handle1, "EmailsNotInAnyList");
									//fwrite($handle1, "\r\n");
									writeEmailsToFile($result5,$handle,$table_prefix,$extraparam,$mysql,$rems_curr_batch);
									if($rems_curr_batch == $rems_batch_count)
											{
												$current_completed_id="not";
												$rems_curr_batch=0;
				
											}
									fclose($handle);
									
						}
						else
						{
									$current_completed_id="not";
									$rems_curr_batch=0;

						}

					}
				}

				if(isset($_POST['Allemails']) && ( !isset($_POST['Emailnolist']) || ( isset($_POST['Emailnolist']) && $_POST[ 'completed']==1 ) ) )
				{
					$status=$_POST['allstatus'];
		
					$str1="";
					if($status==2)
					$str1="where unsubstatus=0";
					if($status==3)
					$str1="where unsubstatus=1";
		
					if(!isset($_COOKIE['inout_sub_admin']))
					{
						$rems_count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt  $str1 ");
						if($rems_count>0)
						{
							if($rems_curr_batch==1)
								{
									if(!is_dir("csv/$day/"))
										mkdir("csv/$day/",0777);
									$handle = fopen ("csv/$day/All_Emails_in_System.csv", "wb");
									$dayname=$day;
								}
								else
								{
									
									$dayname=$day;
									
									$handle = fopen ("csv/$day/All_Emails_in_System.csv", "ab");
								}
		
							
			
							//echo "select email,id from ".$table_prefix."email_advt where id NOT IN(".$str.") $str1";
							if($rems_count>$batch_size)
							{
								$rems_batch_count=ceil($rems_count/$batch_size);
								$startlimit=($rems_curr_batch-1)*$batch_size;
								//$extension.=" (batch $rems_curr_batch of $rems_batch_count) ";
								$temp=$startlimit+$batch_size;
								if($rems_curr_batch == $rems_batch_count)
								$temp=$rems_count;
								echo "<br><b>Exporting 'all emails  in system' : $startlimit to $temp of $rems_count.<blink> <span class=\"already\">Please wait...</span> </blink></b><br><br>";
							}
							else
							{
								$startlimit=0;
								echo "<br><b>Exporting 'all emails  in system' : 0 to $rems_count of $rems_count.<blink> <span class=\"already\">Please wait...</span> </blink></b><br><br>";
							}
							$endlimit=$batch_size;

								
								$result5=mysql_query("select email,id from ".$table_prefix."email_advt $str1 order by id desc limit $startlimit,$endlimit");
								// fwrite($handle1, "EmailsNotInAnyList");
								//fwrite($handle1, "\r\n");
								writeEmailsToFile($result5,$handle,$table_prefix,$extraparam,$mysql,$rems_curr_batch);
										if($rems_curr_batch == $rems_batch_count)
											{
												$current_completed_id="all";
												$rems_curr_batch=0;
				
											}
								fclose($handle);
							
						}	
						else
						{
									$current_completed_id="all";
									$rems_curr_batch=0;

						}
						
					}
				}
			}
		}


		if($selectedcnt==$completedcnt)
		{
			
			if($resultstring=="" && $show=="" && $invalid=="")
			{
				echo "Please go back and select a list. ";
				echo"<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
			}
		
			if($resultstring!="")
			{
				echo "You have successfully exported the selected data. You may find the exported files in the following directory. <br><br>
	 	<span class=\"inserted\">csv/$day/</span>";
				echo "<br><br>If you want to download all the exported files right now, please click on the corresponding links below.";
				echo "<br><br>".$resultstring;
			}


			if($show!="")
			{
				$show.="]";
				echo "<br><span class=\"already\">You cannot export list(s) $show which have no emails.</span><br><br>";
			}

			if($invalid!="")
			{
				$invalid.="]";
				echo "<br><span class=\"already\">You dont have access to export the list(s) $invalid .</span><br><br>";
				if($log_enabled==1 )
				{
					$aid=getAdminId($mysql);
					$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
					mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to export the list(s) $invalid','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
	
				}
	
			}

			if($valid!="" )
			{
				$valid.="]";
				$aid=getAdminId($mysql);
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','List(s) exported as CSV:$valid','".time()."','$CST_MLM_LIST')");
			}

		}


		function escape($str)
		{
			$str = preg_replace("/\r\n$/", "", $str);
			$str = preg_replace("/\n$/", "", $str);
			$str = preg_replace("/\r$/", "", $str);

			if(substr_count($str,"\"")==0)
			return $str;
			$i=0;
			$j=0;
			while(isset($str{$i}))
			{

				if($str{$i}=='"')
				{
					if($j%2==0)
					$str{$i}='“';
					else
					$str{$i}='”';
					$j++;
				}
				//echo $str{$i};
				$i++;
			}
			return $str;
		}

		function writeEmailsToFile($result1,$handle,$table_prefix,$extraparam,$mysql,$rems_curr_batch)
		{
			$i=count($extraparam);
			
			if($i==0)
			{
			
				if($rems_curr_batch==1)
				{
				fwrite($handle, "Email");
				fwrite($handle, ",");
				fwrite($handle, "Name");
				fwrite($handle, ",");
				$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
				$loopvar=1;
				$loopcnt=mysql_num_rows($extrafields);
				while($fielddetails=mysql_fetch_row($extrafields))
					{
						fwrite($handle, $fielddetails[1]);
						if($loopvar<$loopcnt)
						{
							fwrite($handle, ",");
						}
						$loopvar++;
					}
				fwrite($handle, "\r\n");
				}
				//$nameflag=false;
				
				while($row1=mysql_fetch_row($result1))
				{
					fwrite($handle,'"'.escape($row1[0]).'"');
					fwrite($handle, ",");
					$result3=mysql_query("select value from ".$table_prefix."ea_extraparam where name='name' and eid=$row1[1]");
					if(mysql_num_rows($result3)!=0)
					{
						$row2=mysql_fetch_row($result3);
						fwrite($handle,'"'.escape($row2[0]).'"');
						fwrite($handle, ",");
					}
					else
					{
						fwrite($handle, "");
						fwrite($handle, ",");
					}
					$result3=mysql_query("select name,value from ".$table_prefix."ea_extraparam where name <> 'name' and eid=$row1[1]");
					$num=mysql_num_rows($result3);
					$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
					$loopvar=1;
					$loopcnt=mysql_num_rows($extrafields);
					while($fielddetails=mysql_fetch_row($extrafields))
					{
						for($j=0;$j<$num;$j++)
						{
							$row3=mysql_fetch_row($result3);
							if($row3[0]==$fielddetails[1])
							{
								fwrite($handle,'"'.escape($row3[1]).'"');
								break;
							}
						}
						if($loopvar<$loopcnt)
						{
							fwrite($handle, ",");
						}
						mysql_data_seek($result3,0);
						$loopvar++;
					}
					//fseek($handle,-1,SEEK_CUR);

					fwrite($handle, "\r\n");
				}
			}
			else
			{
				if($rems_curr_batch==1)
				{
					
						foreach($extraparam as $key=>$value)
						{
									
									if($value!="Email" && $value!="name")
									{
										$extraparam[$key]=$mysql->echo_one("select fieldname from ".$table_prefix."extra_personal_info where id='$value'");
									}
						
						}			
						
						$loopvar=1;
						$loopcnt=count($extraparam);
						foreach($extraparam as $value)
						{
									fwrite($handle, $value);
									if($loopvar<($loopcnt-1))
									{
										fwrite($handle, ",");
									}
						$loopvar++;
						}			
								
						fwrite($handle, "\r\n");
				}	
				
				while($row1=mysql_fetch_row($result1))
				{
					$loopvar=1;
					$loopcnt=count($extraparam);
					foreach($extraparam as $value)
					{
						if($value=="Email")
							fwrite($handle,'"'.escape($row1[0]).'"');
						else
							if($value!="")
							{
								$result3=$mysql->echo_one("select value from ".$table_prefix."ea_extraparam where name='$value' and eid=$row1[1]");
								//echo "select value from ".$table_prefix."ea_extraparam where name='$value' and eid=$row1[1]";
								fwrite($handle,'"'.escape($result3).'"');
							}
						if($loopvar<($loopcnt-1))
							fwrite($handle, ",");
						$loopvar++;
					}
					//fseek($handle,-1,SEEK_CUR);
					fwrite($handle, "\r\n");

				}
			}
		}

		?></td>
	</tr>
</table>
		<?php
		include_once("admin.footer.inc.php");
		?>



		<?php
	//	echo $current_completed_id;
		if($completedcnt<$selectedcnt)
		{
			mysql_data_seek($result,0);
			?>
<form name="form1" method="post" action="exportcsv.php"><?php
$i=0;
while($row=mysql_fetch_row($result))
{
	?> <input name="<?php echo "List".$i; ?>" type="checkbox"	id="List<?php echo $i; ?>" value="<?php echo $row[0]; ?>"	<?php if(isset($_POST[ "List".$i])) echo "checked"; ?>	style="display: none;">
	 <input name="<?php echo "Email".$i; ?>"	type="hidden" id="Email<?php echo $i; ?>"	value="<?php echo $row[0]; ?>"> 
	 <input name="status<?php echo $i; ?>"	type="hidden" value="<?php echo $_POST["status".$i];?>"> 
	 <input name="ListName<?php echo $i; ?>"	type="hidden" value="<?php echo $row[1]; ?>"> 
	 <input	name="<?php echo "completed".$i; ?>" type="hidden"	id="completed<?php echo $i; ?>"	value="<?php if($current_completed_id==$row[0]) echo "1"; else echo $_POST["completed".$i]; ?>">
	<?php
	$i++;

}?> 
<input name="Emailnolist" type="checkbox" id="Emailnolist" value="1" <?php if(isset($_POST[ "Emailnolist"])) echo "checked"; ?>	style="display: none;"> 
<input name="status" type="hidden" value="<?php echo $_POST['status'];?>"> 
<input	name="completed" type="hidden" value="<?php if($current_completed_id=="not") echo "1"; else echo $_POST["completed"]; ?>"> 
<input name="Allemails"	type="checkbox" id="Allemails" value="1"	<?php if(isset($_POST[ "Allemails"])) echo "checked"; ?>	style="display: none;"> 
<input name="allstatus" type="hidden" value="<?php echo $_POST['allstatus'];?>">
<input name="allcompleted" type="hidden" value="<?php if($current_completed_id=="all") echo "1"; else echo $_POST["allcompleted"]; ?>">

  <select	name="fields1" size="20" multiple id="fields1" style="display: none;">
	<option value="Email">Email</option>
	<option value="name">Name</option>

	<?php
	$result3=mysql_query("select fieldname from ".$table_prefix."extra_personal_info order by 'id' ");
	// $str="";
	while($row1=mysql_fetch_row($result3))
	{
		?>
	<option value="<?php echo $row1[0];?>"><?php echo $row1[0];?></option>

	<?php
	}
	?>

</select> 
<input type="hidden" name="hf3" id="hf3"	value="<?php echo $_POST['hf3']; ?>"> 
<input type="hidden"	name="dayname" id="dayname" value="<?php echo $dayname; ?>">
 <input type="hidden"	name="curr_batch" value="<?php echo  $rems_curr_batch;?>">
</form>

<script language="javascript" type="text/javascript">
	document.form1.submit();
	</script>
	<?php
		}

		?>

