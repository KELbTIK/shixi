<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/




include("config.inc.php");
$e_id=intval($_REQUEST['e_id']);

$id=intval($_REQUEST['id']);
$result=mysql_query("select * from ".$table_prefix."email_advt_curr_run where id=$id");
$row=mysql_fetch_row($result);

if(!$row)
{
echo "Invalid request."; die;
}
//print_r($row);
//echo "hai";
$content=$row[5];
$disp_str="";

if($row[8]=="1") 
header('Content-Type: text/html; charset='.$charset_encoding);
else
header('Content-Type: text/plain; charset='.$charset_encoding);

if(!isset($_REQUEST['e_id']))  // preview by admin
	{
			$content=str_replace("{UNSUBSCRIBE-LINK}","preview_unsub.php",$row[5]); 
			if($enable_web_page_preview==1)
			{
					 if($row[8]=="1")  //html mail
								 {
									 $disp_str.="Email not displaying correctly ? <a href=\"#\">View it in your browser</a><br>";
								 }
			 }
	}
else // preview by user
	{
	
	if($enable_web_page_preview==1) // replace extra params with actual values
		{
			$content=replaceExtraParams($mysql,$table_prefix,"$defaultname",$content,$e_id);
		}
		
	$content=str_replace("{UNSUBSCRIBE-LINK}","#",$content); 
			
	}

							
if($row[16]!=0)
{
$final_str=$mysql->echo_one("select content from ".$table_prefix."email_template where id='$row[16]'");
$final_str=str_replace("{CONTENT}",$content,$final_str);
}
else
{
$final_str=$content;
}
$final_str=$disp_str.$final_str;
echo $final_str;

?><?php

function replaceExtraParams($mysql,$table_prefix,$defaultname,$input,$emailid)
{
	//echo "<br>,$table_prefix,$defaultname,$input,$emailid <br>";
//	if($mysql->total("".$table_prefix."ea_extraparam","name='name' and eid=$emailid")!=0)
		$name=  $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where  eid=$emailid and name='name'");
		//echo "$name <br>";
	if($name=="")
		$name=$defaultname;
	$input=str_replace("{NAME}",$name,$input);
    $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{
		//echo "<br>select value from ".$table_prefix."ea_extraparam where eid=$emailid  and name='$fielddetails[1]'<br>";
		//if($mysql->total("".$table_prefix."ea_extraparam","name='$fielddetails[1]' and eid=$emailid")!=0)
		$val=  $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid=$emailid  and name='$fielddetails[1]'");
		if($val=="")
			$val=$fielddetails[2];
		$input=str_replace($fielddetails[3],$val,$input);
	}	
	return $input;
}
?>