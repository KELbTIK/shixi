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

set_time_limit(0);

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
.style1 {color: #999999}
-->
</style>

        <?php 
		if(isset($_POST['Emailnolist']))
   {
 if($script_mode=="demo") 
 {?>
	<span class="info">You cannot export emails which are not in a list in online demo. </a></span><br><br>
 <?php 
  include_once("admin.footer.inc.php");
  exit(0);
 }
 }
?>

<table width="779"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr align="center">
    <td height="30" colspan="2" scope="row"><a href="export_emails.php">Export
		as IEF</a> | <a href="export_emails_csv.php">Export as CSV</a></td>
  </tr>
    <tr align="center">
    <td colspan="2" scope="row"></td>
  </tr>
  <tr align="left">
    <td  width="18" height="41" scope="row"> <span class="style1"> </span>
        <br>
        <br>
    </td>
    <td width="761" height="41" scope="row"><?php		
		$show="";
		$invalid="";
		$valid="";
$day=date("l_dS_F_Y_h_i_A");
//echo $day;
$i=0;
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
  if(isset($_POST[ "List".$i]))
  {
   $lid=$_POST[ "List".$i]; 
   if(!isValidAccess($lid,$CST_MLM_LIST,$table_prefix,$mysql))
   {
	$n=$_REQUEST['ListName'.$i];
	 if($invalid=="")
		$invalid.="[".$n;
	 else	
		$invalid.=", ".$n;

  }
  else
  {
					$str="";
					$status=$_POST['status'.$i];
					if($status==2)
					$str="and b.unsubstatus=0";
					if($status==3)
					$str="and b.unsubstatus=1";

   if($mysql->total($table_prefix."ea_em_n_cat b","b.cid=$lid $str")>0)
   {
 	 $name=$_REQUEST[ 'ListName'.$i];
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
	 $name=str_replace(" ","_",$name);
	 //echo $name;
  //echo $name;inbuilt
	 mkdir("export/$day/",0777);
   	 $handle = fopen ("export/$day/$name.ief", "wb");
	 $listname=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$row[0]");
	//echo $listname;
     fwrite($handle, $listname); 
	 $resultstring=$resultstring."<a href=\"export/$day/$name.ief\">$listname.ief</a><br><br>";
	 fwrite($handle, "\r\n");
	 if(isset($_POST[ "Email".$i]))
	 {
	   $result1=mysql_query("select a.email,b.eid from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where a.id=b.eid and b.cid=$row[0] $str");
	   writeEmailsToFile($result1,$handle,$table_prefix);
  	 }
     fclose($handle);
	}
	else
	{
	 $name=$_REQUEST['ListName'.$i];
	 if($show=="")
		$show.="[".$name;
	 else	
		$show.=", ".$name;
	//echo "You cant export list having no emails. ";
	//echo"<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	//include_once("admin.footer.inc.php");
//exit(0);
	 }
	}
 }
 $i+=1;
}

function writeEmailsToFile($result1,$handle,$table_prefix)
{
	  
	   while($row1=mysql_fetch_row($result1))
	   {
	   	 fwrite($handle, "Email:".$row1[0]);
	     fwrite($handle, "\r\n"); 
	   	$result3=mysql_query("select value from ".$table_prefix."ea_extraparam where name='name' and eid=$row1[1]");
	   	if(mysql_num_rows($result3)!=0)
		{
			$row2=mysql_fetch_row($result3);
			fwrite($handle, "Name:".$row2[0]);
			fwrite($handle, "\r\n"); 
		}
		else
		{
		      fwrite($handle, "Name:\r\n"); 
		}
		$result3=mysql_query("select name,value from ".$table_prefix."ea_extraparam where name <> 'name' and eid=$row1[1]");
		$num=mysql_num_rows($result3);
		$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
		while($fielddetails=mysql_fetch_row($extrafields))
		{
			$flag=0;
			for($j=0;$j<$num;$j++)
			{
				$row3=mysql_fetch_row($result3);
				if($row3[0]==$fielddetails[1])
				{
					fwrite($handle, $row3[0].":".$row3[1]);
			 		fwrite($handle, "\r\n");
					$flag=1;
					break; 
				}
			}
			if($flag==0)
			{
				fwrite($handle, $fielddetails[1].":");
				fwrite($handle, "\r\n");
			}
			mysql_data_seek($result3,0);
		}
		fwrite($handle, "<EOR>\r\n"); 
	   }
}


if(isset($_POST['Emailnolist']))
{
  $str="";
 $result4=mysql_query("select distinct(eid) from ".$table_prefix."ea_em_n_cat");
 while($row4=mysql_fetch_row($result4))
 {
 	$str=$str."'".$row4[0]."',";
 }
 $str=$str."'0'";

 			$status=$_POST['status'];

			$str1="";
			if($status==2)
			$str1="and unsubstatus=0";
			if($status==3)
			$str1="and unsubstatus=1";

if(isset($_COOKIE['inout_sub_admin']))
 {
 		if($invalid=="")
		$invalid.="[emails not in any list";
		else	
		$invalid.=", emails not in any list";

		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname="emails not in any list";
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to export $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

 }	
 else
 {
 $result5=mysql_query("select email,id from ".$table_prefix."email_advt where id NOT IN(".$str.")  $str1");
 if(mysql_num_rows($result5)>0)
 {
 		$aid=0;
			if($log_enabled==1)
		    {
			 		if($valid=="")
						$valid.="[emails not in any list";
					else	
						$valid.=", emails not in any list";
			}
 mkdir("export/$day/",0777);
 $handle1 = fopen ("export/$day/Emails_Not_in_List.ief", "wb");
 $resultstring=$resultstring."<a href=\"export/$day/Emails_Not_in_List.ief\">Emails_Not_in_List.ief</a><br><br>";
 fwrite($handle1, "EmailsNotInAnyList"); 
 fwrite($handle1, "\r\n");
 writeEmailsToFile($result5,$handle1,$table_prefix);
 	fclose($handle1);
	}
	else
	{
		if($show=="")
		$show.="[emails not in any list";
		else	
		$show.=", emails not in any list";
	}
	}
}

if($resultstring=="" && $show=="" && $invalid=="")
{
	echo "Please go back and select a list. ";
	echo"<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
}

if($resultstring!="")
{
	echo "You have successfully exported the selected data. You may find the exported files in the following directory. <br><br>
 	<span class=\"inserted\">export/$day/</span>";
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
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to export the list(s) $invalid','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}

}

if($valid!="")
{
	$valid.="]";
	$aid=getAdminId($mysql);
	mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','List(s) exported:$valid','".time()."','$CST_MLM_LIST')");
}
?></td>
  </tr>
</table>
<?php
include_once("admin.footer.inc.php");
?>
