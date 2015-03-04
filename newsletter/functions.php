<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php
// crawl -  used to crawl an entire page and return the content of the page eg: $content=crawl($url);

function crawl($url) {

	if($fp=fopen($url)) {
		while(!feof($fp))
		$ch.=fgetc($fp);
		echo $fp;
	}
	else {
		return false;
	}


}

function is_valid_email($email)
{
  $result = TRUE;
  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
    $result = FALSE;
  }
  return $result;
}

function isValidAccess($cid,$entity,$table_prefix,$mysql)
{
		include("constants.php");
		if(!isset($_COOKIE['inout_sub_admin']))
			{
				return true;
			}	
		$tablename="";	
		$entity_fld_name="";
		if($entity==$CST_MLM_LIST)
		{
			$tablename=$table_prefix."admin_access_control";	
			$entity_fld_name="eid";
		}
		if($entity==$CST_MLM_CAMPAIGN)
		{
			$tablename=$table_prefix."campaign_access_control";	
			$entity_fld_name="cid";
		}
		$aid=getAdminId($mysql);
		if($mysql->total($tablename,"aid=$aid and {$entity_fld_name}=$cid")==0)
		{
			return false;
		}
		else
		{
			return true;
		}
}

function isValidEmailAccess($id,$table_prefix,$mysql)
{
		if(!isset($_COOKIE['inout_sub_admin']))
			{
				return true;
			}	
		if($id==-1)
			return false;
		$accessflag=0;
		$aid=getAdminId($mysql);
		$catId=mysql_query("select eid from ".$table_prefix."admin_access_control where aid=$aid");
		$email_cnt=$mysql->total($table_prefix."email_advt","id=$id");
		while($row=	mysql_fetch_row($catId))
		{
			if($row[0]==0)
			{
				if($mysql->total($table_prefix."ea_em_n_cat","eid=$id")==0&&$email_cnt>0 )
					$accessflag=1;
			}
			else
			{
				if($mysql->total($table_prefix."ea_em_n_cat","eid=$id and cid=$row[0]")>0)
					$accessflag=1;
			}
		}
		if($accessflag==0)
		{
			return false;
		}
		else
		{
			return true;
		}
}

function getAdminId($mysql)
{
	global $table_prefix;
	$aid=0;
	$inout_username=$_COOKIE['admin'];
	$inout_password=$_COOKIE['inout_pass'];
	if(isset($_COOKIE['inout_sub_admin']))
	{
		$aid=$mysql->echo_one("select id from ".$table_prefix."subadmin_details where username='$inout_username' and password='$inout_password' and status=1");
		if($aid=="")
		{
			$aid=-1;
		}
	}
	else
		$aid=0;
	return 	$aid;
}

function replaceAllSubStr($str,$find,$replace)
{
	while(1)
	{
	 	if( (substr_count($str, $find)==0) )
		{
	 		return $str;
		}
	 	$str = str_replace($find, $replace, $str);
	}
}

function phpSafe(&$val)
{
if(!get_magic_quotes_gpc())
		{
			$val=mysql_real_escape_string($val);
		}
		$val=htmlspecialchars($val,ENT_QUOTES);
}

?>