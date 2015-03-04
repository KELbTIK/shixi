<?php 



/*--------------------------------------------------+

|													 |

| Copyright © 2006 http://www.inoutscripts.com/      |

| All Rights Reserved.								 |

| Email: contact@inoutscripts.com                    |

|                                                    |

+---------------------------------------------------*/


include_once("config.inc.php"); 
error_reporting(1);
$email=trim(urldecode($_REQUEST['email']));
if($email!="")
{
 $id=-1;
 if( $mysql->total("".$table_prefix."email_advt","email='$email'")==0) //email not in db
 {
	mysql_query("INSERT INTO `".$table_prefix."email_advt` ( `id` , `email` , `unsubstatus` , `time` )VALUES ('', '$email', '0', '".time()."');");
	$roww=$mysql->select_last_row("".$table_prefix."email_advt","id");
	$id=$roww[0];
    $var=trim(urldecode ($_REQUEST['name']));
	if($var!="")
	    mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$var');");
	$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		$reqParamName="extra_personal_info".$fielddetails[0];
		$var=trim(urldecode ($_REQUEST[$reqParamName]));
		if($var!="")
			 mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$var');");
	}		
 }
 else//email already in db
 {
	if($mysql->echo_one("select  unsubstatus from ".$table_prefix."email_advt where email='$email'") == 1)
		exit(0); //dont add already unsubscribed user by automatic subscription s
	 $id = $mysql->echo_one("select id from ".$table_prefix."email_advt where email='$email'");
	if(isset($_REQUEST['name']))
	{
			$var=trim(urldecode ($_REQUEST['name']));
			if($var=="")
				mysql_query("delete from ".$table_prefix."ea_extraparam where eid='$id' and name='name'");
			else
				if($mysql->total("".$table_prefix."ea_extraparam","eid='$id' AND name='name'")==0)
					mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','name','$var');");
				else
					mysql_query("update `".$table_prefix."ea_extraparam` set value='$var' where eid='$id' AND name='name'");
	}
	$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		$reqParamName="extra_personal_info".$fielddetails[0];
		if(isset($_REQUEST[$reqParamName]))
		{
			$var=trim(urldecode ($_REQUEST[$reqParamName]));
			if($var=="")
				mysql_query("delete from ".$table_prefix."ea_extraparam where eid='$id' and name='$fielddetails[1]'");
			else
			    if($mysql->total("".$table_prefix."ea_extraparam","eid='$id' and name='$fielddetails[1]'")==0)
					mysql_query("insert into ".$table_prefix."ea_extraparam values('','$id','$fielddetails[1]','$var')");
			    else
					mysql_query("update ".$table_prefix."ea_extraparam set value ='$var' where eid='$id' and name='$fielddetails[1]' ");
		}		 
	}		
 }
 $catId  = $_REQUEST['cid'];
 $catIdArr = explode(",",$catId);
 addEmailToCategory($catIdArr,$mysql,$table_prefix,$id,$CST_MLM_SUBSCRIPTION,$email,$log_enabled); 
}


function addEmailToCategory($catIdArr,$mysql,$table_prefix,$id,$CST_MLM_SUBSCRIPTION,$email,$log_enabled)
{
    $catlist=""; 
	$cnt=count($catIdArr);
	for($i=0;$i<$cnt;$i++)
	{
		if($mysql->total("".$table_prefix."ea_em_n_cat","cid='$catIdArr[$i]' and eid='$id'")==0)
		{
			$t= time();
		    $catlist.=$mysql->echo_one("select name from`".$table_prefix."email_advt_category` where id='$catIdArr[$i]'").", ";  
			mysql_query("insert into ".$table_prefix."ea_em_n_cat values('','$id','$catIdArr[$i]',0,'$t')");
		}
	}
    if($catlist!="")
	{
		$catlist=substr($catlist,0,strrpos($catlist,","));
		if($log_enabled==1)
		   mysql_query("insert into ".$table_prefix."admin_log_info values('','-1','$email subscribed(php) to $catlist','".time()."','$CST_MLM_SUBSCRIPTION')");
	}
}
?>