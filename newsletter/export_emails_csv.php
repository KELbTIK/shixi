<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php


$file="export_data";

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

include_once("admin.header.inc.php");?>


<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="export_emails.php">Export as IEF</a> | <a href="export_emails_csv.php">Export as CSV</a> </td>
  </tr>
</table>
<br>
<?php 
$getListSql="select * from ".$table_prefix."email_advt_category order by name";
if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.name";
}

$result=mysql_query($getListSql);
if(mysql_num_rows($result)==0)
{
	echo "<br>-No Email Lists Found-<br><br>";
	include_once("admin.footer.inc.php");
	exit(0);
}
$i=0;
//$result=mysql_query("select * from ".$table_prefix."email_advt_category");


?>
<form name="form1" method="post" action="exportcsv.php">
  <table width="99%" cellpadding="5" cellspacing="0">
   <tr>
     <td height="35" colspan="3" valign="top"><span class="inserted">Select the data you want to export</span></td>
    </tr>
   <tr bgcolor="#CCCCCC">
      <td width="414" height="30" valign="middle" bgcolor="#CCCCCC"><strong>Export Lists</strong> (<a href="javascript:checkListsAll();">All</a> , <a href="javascript:uncheckListsAll();">None</a>) <span class="style4">*</span></td>
      <td valign="middle" bgcolor="#CCCCCC" ><strong>Number of Emails</strong></td>
    <td valign="middle" bgcolor="#CCCCCC" colspan="2"><strong>Status</strong></td>
   </tr>
    <?php

while($row=mysql_fetch_row($result))
{

?>
   
    <tr <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>>
      <td height="25" valign="middle" style="border-bottom:1px solid #CCCCCC; "><input name="<?php echo "List".$i; ?>" type="checkbox"  id="List<?php echo $i; ?>" value="<?php echo $row[0]; ?>">        <?php echo $row[1]; ?> <input name="ListName<?php echo $i; ?>" type="hidden" value="<?php echo $row[1]; ?>"></td>
      <td style="border-bottom:1px solid #CCCCCC; " >&nbsp;<?php echo $mysql->echo_one("select count(*) from ".$table_prefix."ea_em_n_cat where  cid =".$row[0]); ?>
      <input name="<?php echo "Email".$i; ?>" type="hidden"  id="Email<?php echo $i; ?>" value="<?php echo $row[0]; ?>">   
      <input name="<?php echo "completed".$i; ?>" type="hidden"  id="completed<?php echo $i; ?>" value="0">   </td>
 <td style="border-bottom:1px solid #CCCCCC; " colspan="2"><input name="status<?php echo $i; ?>" type="radio" value="1" checked>
        All
          <input name="status<?php echo $i; ?>" type="radio" value="2">
      Subscribed
      <input name="status<?php echo $i; ?>" type="radio" value="3">
      Unsubscribed</td>
    </tr>
	
    <?php $i+=1; 
	
	}

?>
<?php if(!isset($_COOKIE['inout_sub_admin']))

{
?>
    <tr <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>>
	
      <td  valign="top" style="border-bottom:1px solid #CCCCCC; ">
          <input name="Emailnolist" type="checkbox" id="Emailnolist" value="1">
        Export emails which are not in any list  </td>
	  <td style="border-bottom:1px solid #CCCCCC; ">&nbsp;<?php echo $mysql->echo_one("select count(*) from ".$table_prefix."email_advt ") - $mysql->echo_one("select count(distinct(eid)) from ".$table_prefix."ea_em_n_cat") ;?></td>
    <td style="border-bottom:1px solid #CCCCCC; "  colspan="2"><input name="status" type="radio" value="1" checked>
        All
          <input name="status" type="radio" value="2">
      Subscribed
      <input name="status" type="radio" value="3">
      Unsubscribed</td>
	  
	  <input name="completed" type="hidden" value="0" >
	  
    </tr>
	<?php
	$i++;
	?>

    <tr  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>>
	
      <td  valign="top" style="border-bottom:1px solid #CCCCCC; ">
          <input name="Allemails" type="checkbox" id="Allemails" value="1">
        Export all emails in the system </td>
	  <td style="border-bottom:1px solid #CCCCCC; " >&nbsp;<?php echo $mysql->echo_one("select count(*) from ".$table_prefix."email_advt ");?></td>
    <td style="border-bottom:1px solid #CCCCCC; " colspan="2"><input name="allstatus" type="radio" value="1" checked>
        All
          <input name="allstatus" type="radio" value="2">
      Subscribed
      <input name="allstatus" type="radio" value="3">
      Unsubscribed</td>
	
	   <input name="allcompleted" type="hidden" value="0" >
    </tr>
	<?php
	}
	?>
	
	    <tr>
      <td colspan="3"   valign="top">        
        <em>[Exporting may take a few minutes depending on the size of the list.]</em> 
        
      </td>
	  </tr>
	   <tr>
	   <?php
	   $result3=mysql_query("select fieldname from ".$table_prefix."extra_personal_info order by 'id' ");
	    $str="";
	    while($row1=mysql_fetch_row($result3))
	   {
	  
	   $str.=", ".$row1[0];
	   
	   }
	   ?>
      <td colspan="3"   valign="top">        
        <span class="inserted">Data will be exported in the format </span><em><strong><br>
        Email, Name<?php echo $str;?>.</strong></em><span class="inserted"> <br>
        If you want to change the order please select the order below and click export button.</span><br>		</td>
	</tr>
		<TR>
		<TD>
		<select name="fields1" size="20" multiple id="fields1" style="width:150px; ">
	   <option value="Email">Email</option>
		<option value="name">Name</option>
					  
				  <?php
	   $result3=mysql_query("select id,fieldname from ".$table_prefix."extra_personal_info order by 'id' ");
	   // $str="";
	    while($row1=mysql_fetch_row($result3))
	   {
	   ?>
                  <option value="<?php echo $row1[0];?>"><?php echo $row1[1];?></option>
                  
		<?PHP
		}
		?>
                
          </select>
		</TD>
		<td width="225" align="left"><p>&nbsp;&nbsp;<a href="javascript:addfield(document.getElementById('fields1'));"><img src="images/right.jpeg" width="44" height="39" border="0"> </a> </p>
                  <p>&nbsp;</p>
                  <p><a href="javascript:removefield(document.getElementById('fields2'));"> <img src="images/left.gif" width="44" height="39" border="0"></a></p>
                  <p>&nbsp;</p>
          <p>&nbsp;</p></td>
		<td width="497"><select name="fields2" size="20" multiple id="fields2" style="width:150px; ">
                </select>  <input type="hidden" name="hf3" id="hf3"></td>
		</TR>
		<tr>
		<td colspan="3">
		<br>
          <input type="submit" name="Submit" value="Export Data !">
          </p>
      </td>
	  </tr>
  </table>
</form>
<script language="javascript" type="text/javascript">
function addfield(contlist)
		{		
			//alert(contlist.options[0].value);
			var cntry2=document.getElementById('fields2');
			var flag=0;
			for(i = 0; i < contlist.length; i++)
				{
					if(contlist.options[i].selected)
					{	
						flag=0;
					
						for(j=0; j < cntry2.length; j++)
						{ 
							if(cntry2.options[j].value==contlist.options[i].value)
							{
							//alert(contlist.options[i].value);
							flag=1;
							}
						}
						if(flag==0)
						{
						//alert(contlist.options[i].text);
							
							
							var opt1=document.createElement("OPTION");
							opt1.value=contlist.options[i].value;
							opt1.text=contlist.options[i].text;   
							document.getElementById('fields2').options.add(opt1);
							
						}	
					
					}
				}	
								document.getElementById('hf3').value="";
							for(k=0;k<cntry2.length;k++)
								   {
										 document.getElementById('hf3').value+=cntry2.options[k].value+",";
								   }
								 //  alert( document.getElementById('hf2').value);
		}
		function removefield(contlist)
		{
				for(i = 0; i<contlist.length; i++)
					{
						if(contlist.options[i].selected)
						{
							contlist.remove(contlist.selectedIndex);
							i--;
						}
					}	
					
					document.getElementById('hf3').value="";
					cntry2=document.getElementById('fields2');
					for(k=0;k<cntry2.length;k++)
					{
						document.getElementById('hf3').value+=cntry2.options[k].value+",";
					}
					
								   
		}
		
		
		
		
		
		
		</script>		
<?php
include_once("admin.footer.inc.php");
?>
