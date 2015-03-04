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
include("admin.header.inc.php");
?>
<link href="style.css" rel="stylesheet" type="text/css">

<!--<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="importemails.php?type=single"> Single Email</a> | <a href="importemails.php?type=many"> Multiple Emails</a> | <a href="importemails.php?type=source">Extract from HTML</a> | <a href="emailadvturl.php">Extract from URLs</a> | <a href="importfromdb.php">Import from MySQL</a> | <a href="importfromfile.php">Import from Files </a></td>
  </tr>
</table>
<br>
-->
<?php
set_time_limit(0);
if($_POST['category']=="")
{
	echo "YOU SHOULD SELECT FIRST EMAIL LIST.";?>
	<a href="javascript:history.back(-1);">Go Back</a><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
$extension="";
$allmails="";
		
$rems_batch_count=1;
$rems_curr_batch=1;
$batch_size=1000;

$emails_already=0;
$emails_added=0;
$emails_list1=0;
$emails_list2=0;
$emails_list3=0;
if(isset($_POST['emails_already']))
$emails_already=$_POST['emails_already'];
if(isset($_POST['emails_added']))
$emails_added=$_POST['emails_added'];
if(isset($_POST['emails_list1']))
$emails_list1=$_POST['emails_list1'];
if(isset($_POST['emails_list2']))
$emails_list2=$_POST['emails_list2'];
if(isset($_POST['emails_list3']))
$emails_list3=$_POST['emails_list3'];

//print_r($_POST);
if(isset($_POST['server']))
{
    $extension="from db";
	$server=trim($_POST['server']);
	$user=trim($_POST['user']);
	$pass=trim($_POST['pass']);
	$db=trim($_POST['db']);
	$table=trim($_POST['table']);
	$fldem=trim($_POST['fldem']);
	$fldname="";
	$secname="";
	if(isset($_POST['fldname']))
	{
		$fldname=trim($_POST['fldname']);
		phpSafe($fldname);
		}
	if(isset($_POST['secname']))
	{
		$secname=trim($_POST['secname']);
		phpSafe($secname);
		}
	if($server=="" || $user=="" || $db=="" || $table=="" || $fldem=="")
	{ 
		echo "Please complete the database info.";?>
	<a href="javascript:history.back(-1);">Go Back</a><br><br>
	<?php
		include_once("admin.footer.inc.php");
		exit(0);
	}
	$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	$fieldnames="";
	$arrfielddetails[] = array();
	$loopvar=0;
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		$arrfielddetails[$loopvar]=trim($_POST["extra_personal_info".$fielddetails[0]]);
		if(trim($_POST["extra_personal_info".$fielddetails[0]])!="")
	 		$fieldnames.=",".$_POST["extra_personal_info".$fielddetails[0]] ;
		$loopvar+=1;
	}
	if(FALSE == mysql_connect($server, $user, $pass) )//or
	{
		echo "Could not connect: Might be wrong database info. Please check." . mysql_error();
		?>
		<a href="javascript:history.back(-1);">Go Back</a><br><br>
		<?php
		include_once("admin.footer.inc.php");
		exit(0);
	}
	// mysql_connect($server, $user, $pass) or
	//die("Could not connect: Might be wrong database info. Please go back and check." . mysql_error());
	mysql_select_db($db);
	if($fldname!="" && $secname!="")
	{
		$rems=mysql_query("select count(*) from $table");
		$rems_row= mysql_fetch_row($rems);
		$rems_count=$rems_row[0];
		if(isset($_POST['curr_batch'])) 
			$rems_curr_batch=$_POST['curr_batch']+1;
		if($rems_count>$batch_size)
		{
			$rems_batch_count=ceil($rems_count/$batch_size);
			$startlimit=($rems_curr_batch-1)*$batch_size;
			$extension.=" (batch $rems_curr_batch of $rems_batch_count) ";
			$temp=$startlimit+$batch_size;
			echo "<br><b><blink>Importing $startlimit to $temp of $rems_count....</blink></b><br><br>";
		}
		else
		{
			$startlimit=0;
		}
		$endlimit=$batch_size;
		$rems=mysql_query("select $fldem , $fldname , $secname".$fieldnames." from $table limit $startlimit,$endlimit");
		while($rem=mysql_fetch_row($rems))
		{ 
			$allmails.="\n ".$rem[0].":".$rem[1]." ".$rem[2]; 
			$j=3;
			$allmails=formEmailString($arrfielddetails,$allmails,$rem,3);
		}
	}
	elseif($fldname!="")
	{
		$rems=mysql_query("select count(*) from $table");
		$rems_row= mysql_fetch_row($rems);
		$rems_count=$rems_row[0];
		if(isset($_POST['curr_batch'])) 
			$rems_curr_batch=$_POST['curr_batch']+1;
		if($rems_count>$batch_size)
		{
			$rems_batch_count=ceil($rems_count/$batch_size);
			$startlimit=($rems_curr_batch-1)*$batch_size;
			$extension.=" (batch $rems_curr_batch of $rems_batch_count) ";
			$temp=$startlimit+$batch_size;
			echo "<br><b><blink>Importing $startlimit to $temp of $rems_count....</blink></b><br><br>";
		}
		else
		{
			$startlimit=0;
		}
		$endlimit=$batch_size;
		$rems=mysql_query("select $fldem , $fldname".$fieldnames." from $table limit $startlimit,$endlimit");
		while($rem=mysql_fetch_row($rems))
		{ 
			$allmails.="\n ".$rem[0].":".$rem[1];
			$allmails=formEmailString($arrfielddetails,$allmails,$rem,2);
		}
	}
	else
	{
		$rems=mysql_query("select count(*) from $table");
		$rems_row= mysql_fetch_row($rems);
		$rems_count=$rems_row[0];
		if(isset($_POST['curr_batch'])) 
			$rems_curr_batch=$_POST['curr_batch']+1;
		if($rems_count>$batch_size)
		{
			$rems_batch_count=ceil($rems_count/$batch_size);
			$startlimit=($rems_curr_batch-1)*$batch_size;
			$extension.=" (batch $rems_curr_batch of $rems_batch_count) ";
			$temp=$startlimit+$batch_size;
			echo "<br><b><blink>Importing $startlimit to $temp of $rems_count....</blink></b><br><br>";
		}
		else
		{
			$startlimit=0;
		}
		$endlimit=$batch_size;
		$rems=mysql_query("select $fldem".$fieldnames." from $table limit $startlimit,$endlimit");
		while($rem=mysql_fetch_row($rems))
		{
 			$allmails.="\n ".$rem[0].":"; 
			$allmails=formEmailString($arrfielddetails,$allmails,$rem,1);
		}
	}
	mysql_connect($mysql_server, $mysql_username, $mysql_password) or
 	die("Could not connect: " . mysql_error());
	mysql_select_db($mysql_dbname);
}

/*This method forms the email string while importing from db when there are extraparams*/
function formEmailString($arrfielddetails,$emails,$row,$j)
{
	for($i=0;$i<count($arrfielddetails);$i+=1)
	{
		if($arrfielddetails[$i]=="")
			$emails.=":";
		else
		{
			$emails.=":".$row[$j];
			$j+=1;
		}
	}
	return $emails;
}

$new=0;
$old=0;
$invalid=0;
$arraycount=array();
$arraycount[1]=0;
$arraycount[2]=0;
$arraycount[3]=0;


		
if($log_enabled==1)
{
	$aid=0;
	if(isset($_COOKIE['inout_sub_admin']))
	{
		$aid=getAdminId($mysql);
	}
	
	$entityname="";
	
	$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");

	$id=$_POST['category'];
	if(!isValidAccess($id,$CST_MLM_LIST,$table_prefix,$mysql))
	{
		$entityname=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id=$id");
	}	
	if(($_POST['category2']!=$_POST['category'])&&($_POST['category2']!=""))
	{
		$id=$_POST['category2'];
		if(!isValidAccess($id,$CST_MLM_LIST,$table_prefix,$mysql))
		{
			if($entityname!="")
				$entityname.=", ";
			$entityname.=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id=$id");
		}	
	}	
	if(($_POST['category3']!=$_POST['category2'])&&($_POST['category3']!=$_POST['category'])&&($_POST['category3']!=""))
	{	
		$id=$_POST['category3'];
		if(!isValidAccess($id,$CST_MLM_LIST,$table_prefix,$mysql))
		{
			if($entityname!="")
				$entityname.=", ";
			$entityname.=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id=$id");
		}	
	}
	if($entityname!="")
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to add emails to the list(s) $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
}		
			
//$script_mode="live";
if(isset($_POST['file']))
{ 
    $extension="from file"; 
	$filename=$_FILES['file']['name'];
	if($filename=="")
	{
		echo "Please select a file.";?>
		<a href="javascript:history.back(-1);">Go Back</a><br>
		<?php 
		include_once("admin.footer.inc.php");
		exit(0);
	}

	$exten=strtolower(substr($_FILES['file']['name'],-4));
//echo $_FILES['file']['type'];
	if(!strstr($_FILES['file']['type'],"text") && $exten!=".txt" && $exten!=".csv")
	{
		echo "Please select only csv/text based file.";?>
		<a href="javascript:history.back(-1);">Go Back</a><br>
		<?php 
		include_once("admin.footer.inc.php");
		exit(0);
	}
	copy($_FILES['file']['tmp_name'],"attachments/".$_FILES['file']['name']);
	if($script_mode=="demo")
	{
		if( $exten!=".txt" && $exten!=".csv")
		{
			echo "In demo version you can extract emails only from Text or CSV files.";
			unlink("attachments/".$_FILES['file']['name']);
			include("admin.footer.inc.php");
  			exit(0);
		 }
	}
	$fp=fopen("attachments/".$_FILES['file']['name'],"r");
	$emails=" ";
	while(!feof($fp))
	{
		$emails.=fgetc($fp);
	}
	$emails.=",";
}
else if(isset($_POST['urls']))
{ 
	$extension="from url";
	if($script_mode=="demo")
	{
		echo "This feature is disabled in demo version due to security problems.<br>";
		include("admin.footer.inc.php");
		exit(0); 
	}
	$urls=trim($_POST['urls']);
	if($urls=="") 
	{
		echo "Please fill in all  mandatory fields.";?>
		<a href="javascript:history.back(-1);">Go Back</a><br>
		<?php 
		include_once("admin.footer.inc.php");
		exit(0);
	}
	$urls="\n".$_POST['urls']."\n";
	$k=0;
	$emails=" ";
	while(isset($urls{$k}))
	{
		$url=" ";
		$l=0;
		if( $urls{$k}=="\n" || $urls{$k}=="\r" || $urls{$k}=="\r\n" || $urls{$k}==" " )
		{ 	
			$k+=1;
			while(1) 
			{
				if(!isset($urls{$k}))
					break;
				$url{$l}=$urls{$k};
				$k+=1;
				$l+=1;
				if(!isset($urls{$k}))
					break;
				if($urls{$k}=="\n" || $urls{$k}=="\r" || $urls{$k}=="\r\n" || $urls{$k}==" " )
				{								
					$url=str_replace(" ","",$url);
					$url=str_replace(">","",$url);
					$url=str_replace("\r\n","",$url);
					$url=str_replace("\n","",$url);
					if($fp=fopen($url,"r"))
					{
						while(!feof($fp))
							$emails.=fgetc($fp);
						fclose($fp);
					}
					$k-=1;
					break;
				}
			}
		}
		$k+=1;
	}//end of while
	$emails.=",";
}
else if(isset($_POST['server']))
{ 
	$emails="".$allmails.","; 
} 
else
{ 
   if(isset($_GET['val']))
   {
   	$extension=" from ief file";
   }
   else
   {
    $extension=" manually";
   }	
	$emails=trim($_POST['emails']);
	phpsafe($emails);
	if(($emails=="") && !isset($_POST['add']))
	{
		echo "Please fill in all  mandatory fields.";?>
		<a href="javascript:history.back(-1);">Go Back</a><br>
		<?php 
		include_once("admin.footer.inc.php");
		exit(0);
	}
	$emails=" ".trim($_POST['emails']).",";
}


if(isset($_POST['add']))
{
	$tmp=trim($_POST['email0']);
	if($tmp=="")
	{
			echo "Please fill in all  mandatory fields.";?>
		<a href="javascript:history.back(-1);">Go Back</a><br>
		<?php 
		include_once("admin.footer.inc.php");
		exit(0);
	} 
	$emails=" ";
	for($i=0;$i<5;$i+=1)
	{ 
		$addname="email".$i; //echo $addname;
		$emails.=trim($_POST[$addname]).",,";
	}
	
}


//echo $emails;
if($emails!="")
{ 
//echo htmlentities($emails);exit(0);
	//replacing all ": " and  " :" with ":" 
	
	$emails = replaceAllSubStr($emails,": ",":");
	$emails = replaceAllSubStr($emails," :",":");
	$emails = replaceAllSubStr($emails," ,",",");
	if(isset($_POST['urls'])||isset($_REQUEST['html']))//in these cases extract only emails (no extraparams)
	{
		extractEmailsOnly($emails,$mysql,$table_prefix,$extension,$arraycount,$log_enabled);
	}
	else
	{
	  $i=0;
	  $extraparamcounter = 0; // This field is used for managing extra params when they are inputed in single textarea 
	  //ie, colon separated.
	  $emailFlag=true;
	  while(isset($emails{$i}))
	  { 
		$email="";
		$j=0;
		if($emails{$i}=="\r\n" || $emails{$i}=="\r" || $emails{$i}=="\n" || $emails{$i}=="," || $emails{$i}==":"||$emails{$i}==" ")
		{ 
			$i+=1;
			while(1)
			{
			  
				if(!isset($emails{$i}))
					break;
				if(($emails{$i}==":"||$emails{$i}==","||$emails{$i}=="\n" || $emails{$i}=="\r" ||$emails{$i}=="\r\n") && $j==0)
				{
					$email="";
					$i-=1;
				}
				else
				{
					$email.=$emails{$i};
				}
				//echo $email."<br>";
				$j+=1;
				$i+=1;
				if(!isset($emails{$i}))
					break;
				if($emails{$i}=="\r\n" || $emails{$i}=="\n" || $emails{$i}=="\r" || $emails{$i}==","  || $emails{$i}==":" )
				{	
					$name="";			
					if($emails{$i}==":" || $emails{$i}==","  )
					{ 
						$ink=$i+1;//echo $emails{$ink};
						while((isset($emails{$ink})) && $emails{$ink}!="\r\n" && $emails{$ink}!="\r" && $emails{$ink}!="\n" && $emails{$ink}!="," && $emails{$ink}!=":" )
						{
							$name.=$emails{$ink};
							$ink+=1;
						}

					}
//echo $name;die;
					if ($extraparamcounter>0)//This  block is for insering extra fields coming after name
						// while inputing mutilple emails  through single text area
					{
						$name=trim($name);
						$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id LIMIT ".($extraparamcounter-1).",1");
						$fielddetails=mysql_fetch_row($extrafields);
						if(str_replace(" ","",$name)!="")
						{
							if(isset($_POST['file']) && strpos($name,'"')==0 && strrpos($name,'"')== (strlen($name)-1) ) 
							{
								$name=substr($name,1,strlen($name)-2);
							}	
							mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$name');");
						}
						if($emails{$i}==":" || $emails{$i}==",")
						{
							$extraparamcounter=$extraparamcounter+1;
						}
						if($emails{$i}=="\r\n" || $emails{$i}=="\n"  || $emails{$i}=="\r" )
						{
							$extraparamcounter = 0;
							$emailFlag=true;
							$i-=1;
							break;
						}	
					}//end of extra param logic 
					$email=str_replace(" ","",$email);
					$email=str_replace("<","",$email);
					//$email=str_replace("_user_","",$email);
					$email=str_replace(">","",$email);
					$email=trim($email);
					
										
					if(isset($_POST['file']) && strpos($email,'"')==0 && strrpos($email,'"')== (strlen($email)-1) ) 
					{
						$email=str_replace('"',"",$email);
					}	
					//echo $email.$name."<br>";die;
					if(is_valid_email($email) && $emailFlag==true)
					{ 

						if($mysql->total("".$table_prefix."email_advt","email='$email'")==0)//checking whether email is already in db
						{
							mysql_query("INSERT INTO `".$table_prefix."email_advt` ( `id` , `email` , `unsubstatus` , `time` )VALUES ('', '$email', '0', '".time()."');");
							echo "<span class=\"inserted\">".$email." - > Inserted Into Database. <br></span>"; 
							$emailFlag=false;
							$roww=$mysql->select_last_row("".$table_prefix."email_advt","id");
							$id=$roww[0]; //local variable $id specific to if block
							
							if($extraparamcounter==0 && ($emails{$i}==":" || $emails{$i}==","))
							{
								$extraparamcounter=$extraparamcounter+1;
								if($name!="")
								{
									if(str_replace(" ","",$name)!="")
									{
										if(isset($_POST['file']) && strpos($name,'"')==0 && strrpos($name,'"')== (strlen($name)-1) ) 
										{
											$name=substr($name,1,strlen($name)-2);
										}	
										mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$name');");
									}
								}
							}
							if($extraparamcounter==0)
								$emailFlag=true;
							if(isset($_POST['extra']))
							{
								$emailFlag=true;
								if(str_replace(" ","",$_POST['name'])!="")
								{
									$fieldvalue = trim($_POST['name']);
									 mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$fieldvalue');");
								}	 
								$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
								while($fielddetails=mysql_fetch_row($extrafields))
								{
									$fieldvalue = trim($_POST[ "extra_personal_info".$fielddetails[0]]);
									if(str_replace(" ","",$fieldvalue)!="")
									{
										mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$fieldvalue');");
									}
								}
							}
							if(isset($_POST['add']))
							{
								$emailFlag=true;
								$addname="name".($new+$old+$invalid);
								if(str_replace(" ","",$_POST[$addname])!="")
								{
									$fieldvalue = trim($_POST[$addname]);
									 mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$fieldvalue');");
 								}
								 $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
								 while($fielddetails=mysql_fetch_row($extrafields))
								 {
 										$varname = "extra_personal_info".$fielddetails[0].($new+$old+$invalid);
										$fieldvalue = trim($_POST[ $varname  ]);
										if(str_replace(" ","",$fieldvalue)!="")
										{
											mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$fieldvalue');");
										}
								 }
							}
							$arraycount=addEmailToList($mysql,$id,$table_prefix,$arraycount);
							$new+=1;
						}//End of checking whether email is already in db
						else
						{
							echo "<span class=\"already\">".$email." - > Already in database <br</span>"; 
							$old+=1;
							//local variable $id specific to else block
							$id = $mysql->echo_one("select id from ".$table_prefix."email_advt where email='$email'");
							$arraycount=addEmailToList($mysql,$id,$table_prefix,$arraycount);
						}
					}//end of checking whether email format is valid
					if(!is_valid_email($email) && $emailFlag==true &&$email!="")
					{
						$invalid+=1;
					}
					$i-=1;
					break;
				}
			}//end of while(1)
		 }
		$i+=1;
	  }//End of while(isset($emails{$i}))?>
	 	 <br>
		<strong>
		Total number of new emails added to the database     :<?php echo $new."<br>"; 
					$catlist1=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category]");
			echo "Total number of emails added to the list [$catlist1]     :{$arraycount[1]}<br>";
			
			if(($_POST['category2']!=$_POST['category'])&&($_POST['category2']!=""))
			{
				$catlist2=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category2]");
				echo "Total number of emails added to the list [$catlist2]     :{$arraycount[2]}<br>";
			}
			if(($_POST['category3']!=$_POST['category2'])&&($_POST['category3']!=$_POST['category'])&&($_POST['category3']!=""))
			{
				$catlist3=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category3]");
				echo "Total number of emails added to the list [$catlist3]     :{$arraycount[3]}<br>";
			}

		if($log_enabled==1)
		{
			writeLog($new,$table_prefix,$extension,$arraycount,$mysql);
		}
		?>
		Total number of emails which are already in database :<?php echo $old; ?><br>
		
		<br>
		<?php
		$emails_already+=$old;
		$emails_added+=$new;
		$emails_list1+=$arraycount[1];
		$emails_list2+=$arraycount[2];
		$emails_list3+=$arraycount[3];
		if($rems_batch_count==$rems_curr_batch)
		{
		echo "<br><br>Total Statistics<br>";
		echo "Total number of new emails added to the database     :$emails_added<br>"; 
		echo "Total number of emails added to the list [$catlist1]     :{$emails_list1}<br>";
		if(($_POST['category2']!=$_POST['category'])&&($_POST['category2']!=""))
		echo "Total number of emails added to the list [$catlist2]     :{$emails_list2}<br>";
		if(($_POST['category3']!=$_POST['category2'])&&($_POST['category3']!=$_POST['category'])&&($_POST['category3']!=""))
		echo "Total number of emails added to the list [$catlist3]     :{$emails_list3}<br>";
		echo "Total number of emails which are already in database :$emails_already<br>";
		}
		
		if($entityname!="")
		{
		?>
		<span class="already">Invalid Access!!! Emails not added to the list(s) [<?php echo $entityname; ?>] !!! </span>
		<?php
		}
		?>
		</strong><?php
	}
}
	
function addEmailToList($mysql,$eid,$table_prefix,$arraycount)
{
	include("constants.php");
	$t=time();
	//echo "CST_MLM_LIST".$CST_MLM_LIST;
	if($mysql->total("".$table_prefix."ea_em_n_cat","eid='$eid' and cid='$_POST[category]'")==0  && isValidAccess($_POST['category'],$CST_MLM_LIST,$table_prefix,$mysql) )//check whether eid already mapped to cid
	{
			mysql_query("insert into ".$table_prefix."ea_em_n_cat values('','$eid','$_POST[category]',0,'$t')");
			$arraycount[1]+=1;
	}
	if(($_POST['category2']!=$_POST['category'])&&($_POST['category2']!=""))
		if($mysql->total("".$table_prefix."ea_em_n_cat","eid='$eid' and cid='$_POST[category2]'")==0  && isValidAccess($_POST['category2'],$CST_MLM_LIST,$table_prefix,$mysql) )//check whether eid already mapped to cid
		{
			mysql_query("insert into ".$table_prefix."ea_em_n_cat values('','$eid','$_POST[category2]',0,'$t')");
			$arraycount[2]+=1;
		}	
	if(($_POST['category3']!=$_POST['category2'])&&($_POST['category3']!=$_POST['category'])&&($_POST['category3']!=""))
		if($mysql->total("".$table_prefix."ea_em_n_cat","eid='$eid' and cid='$_POST[category3]'")==0  && isValidAccess($_POST['category3'],$CST_MLM_LIST,$table_prefix,$mysql) )//check whether eid already mapped to cid
		{
			$arraycount[3]+=1;
			//echo $eid."  ".$_POST[category3];
			mysql_query("insert into ".$table_prefix."ea_em_n_cat values('','$eid','$_POST[category3]',0,'$t')");
			//echo mysql_error();
		}	
	return 	$arraycount;
}


function extractEmailsOnly($emails,$mysql,$table_prefix,$extension,$arraycount,$log_enabled)
{
	$i=0;
	$new=0;
	$old=0;
		while(isset($emails{$i}))
			{ 
				$email=" ";
				$j=0;
				if($emails{$i}==" " || $emails{$i}=="(" || $emails{$i}==")" || $emails{$i}=="]" || $emails{$i}=="[" || $emails{$i}=="\r\n" || $emails{$i}=="\r" || $emails{$i}=="\n" || $emails{$i}=="," || $emails{$i}=="<" || $emails{$i}==">"|| $emails{$i}==":" || $emails{$i}=="'" || $emails{$i}=="\"")
				{ 	$i+=1;

					while(1) 
					{
						if(!isset($emails{$i}))
							break;
						$email{$j}=$emails{$i};
						$j+=1;
						$i+=1;
						if(!isset($emails{$i}))
							break;
						if($emails{$i}==" " || $emails{$i}=="(" || $emails{$i}==")" || $emails{$i}=="]" || $emails{$i}=="[" || $emails{$i}=="\r\n" || $emails{$i}=="\r" || $emails{$i}=="\n" || $emails{$i}=="," || $emails{$i}==">"|| $emails{$i}=="<" || $emails{$i}==":" || $emails{$i}=="'" || $emails{$i}=="\"" || $emails{$i}=="\\" || $emails{$i}=="/"|| $emails{$i}=="&"|| $emails{$i}==";")
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
								if(is_valid_email($email))
								{ 
									if($mysql->total("".$table_prefix."email_advt","email='$email'")==0)
									{
										mysql_query("INSERT INTO `".$table_prefix."email_advt` ( `id` , `email` , `unsubstatus` , `time` )VALUES ('', '$email', '0', '".time()."');");
										echo "<span class=\"inserted\">".$email." - > Inserted Into Database. <br></span>"; 
										
										$roww=$mysql->select_last_row("".$table_prefix."email_advt","id");
										$id=$roww[0]; //echo $name;
										$arraycount=addEmailToList($mysql,$id,$table_prefix,$arraycount);
										
										$new+=1;
									}
									else
									{
										echo "<span class=\"already\">".$email." - > Already In Database <br</span>"; 
										$old+=1;
										$id=$mysql->echo_one("select id from `".$table_prefix."email_advt` where email='$email'");
										$arraycount=addEmailToList($mysql,$id,$table_prefix,$arraycount);

									}
								}
								$i-=1;
								break;
						}
					}
				}
				$i+=1;
			}?>
			  <br>
		<strong>
		Total number of new emails added to the database     :<?php echo $new."<br>";
		
			$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category]");
			echo "Total number of emails added to the list [$catlist]     :{$arraycount[1]}<br>";
			
			if(($_POST['category2']!=$_POST['category'])&&($_POST['category2']!=""))
			{
				$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category2]");
				echo "Total number of emails added to the list [$catlist]     :{$arraycount[2]}<br>";
			}
			if(($_POST['category3']!=$_POST['category2'])&&($_POST['category3']!=$_POST['category'])&&($_POST['category3']!=""))
			{
				$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category3]");
				echo "Total number of emails added to the list [$catlist]     :{$arraycount[3]}<br>";
			}
		if($log_enabled==1)
		{
			writeLog($new,$table_prefix,$extension,$arraycount,$mysql);
		}
		?>
		Total number of emails which are already in database :<?php echo $old; ?><br><br>
		<?php
		if($entityname!="")
		{
		?>
		<span class="already">Invalid Access!!! Emails not added to the list(s) [<?php echo $entityname; ?>]!!! </span>
		<?php
		}
		?>
		</strong>
		<?php
		
}

function writeLog($new,$table_prefix,$extension,$arraycount,$mysql)
{
			include("constants.php");
			$aid=0;
			if(isset($_COOKIE['inout_sub_admin']))
			{
				$aid=getAdminId($mysql);
			}	
			if($new>0)
			{
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$new email(s) added to database $extension','".time()."','$CST_MLM_EMAIL')");
			}

			if($arraycount[1]>0)
			{
				$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category]");
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$arraycount[1] email(s) added to list(s) $catlist $extension','".time()."','$CST_MLM_LIST')");
			}	
			if($arraycount[2]>0)
			{
				$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category2]");
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$arraycount[2] email(s) added to list(s) $catlist $extension','".time()."','$CST_MLM_LIST')");
			}
			if($arraycount[3]>0)
			{
				$catlist=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$_POST[category3]");
				mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$arraycount[3] email(s) added to list(s) $catlist $extension','".time()."','$CST_MLM_LIST')");
				
			}
}
?>

<?php 
if($rems_curr_batch < $rems_batch_count)
{
?>
<form name="form1" method="post" action="extractems.php">
<input type="hidden" name="category" value="<?php echo  $_POST['category'];?>">
<input type="hidden" name="category2" value="<?php echo  $_POST['category2'];?>">
<input type="hidden" name="category3" value="<?php echo  $_POST['category3'];?>">
<input type="hidden" name="server" value="<?php echo  $_POST['server'];?>">
<input type="hidden" name="user" value="<?php echo  $_POST['user'];?>">
<input type="hidden" name="pass" value="<?php echo  $_POST['pass'];?>">
<input type="hidden" name="db" value="<?php echo  $_POST['db'];?>">
<input type="hidden" name="table" value="<?php echo  $_POST['table'];?>">
<input type="hidden" name="fldem" value="<?php echo  $_POST['fldem'];?>">
<input type="hidden" name="fldname" value="<?php echo  $_POST['fldname'];?>">
<input type="hidden" name="secname" value="<?php echo  $_POST['secname'];?>">
<input type="hidden" name="curr_batch" value="<?php echo  $rems_curr_batch;?>">
<input type="hidden" name="emails_already" value="<?php echo  $emails_already;?>">
<input type="hidden" name="emails_added" value="<?php echo  $emails_added;?>">
<input type="hidden" name="emails_list1" value="<?php echo  $emails_list1;?>">
<input type="hidden" name="emails_list2" value="<?php echo  $emails_list2;?>">
<input type="hidden" name="emails_list3" value="<?php echo  $emails_list3;?>">
<?php 
$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
while($fielddetails=mysql_fetch_row($extrafields))
{
?>
<input type="hidden" name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" value="<?php echo $_POST["extra_personal_info".$fielddetails[0]];?>">
<?php
}
?>
</form>
<?php
//sleep(2);
?>
<script language="javascript" type="text/javascript">
document.form1.submit();
</script>
<?php
}
else
{
?>
<br><br>
<!--<a href="main.php">Back to Home</a>-->
<b>Email import completed.</b>
<br><br>
<?php
}
?>

<?php include("admin.footer.inc.php"); ?>