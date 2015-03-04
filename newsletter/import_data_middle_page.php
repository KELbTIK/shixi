<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/

?>
<?php

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

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {color: #666666}
.style3 {color: #FF0000}
.style4 {color: #666666}
-->
</style>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="importfromfile.php">Import from CSV</a> | <a href="import_data.php">Import from IEF</a></td>
  </tr>
</table>

<form name="form1" method="post" action="extractems.php?type=many&val=ief">
  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
      <th width="1%" scope="row">&nbsp;</th>
      <td width="98%" align="center">&nbsp;</td>
      <td width="1%">&nbsp;</td>
    </tr>
	 <?php 
	 $filename=$_FILES['file']['name'];
	  if($filename!="" && substr($filename,strlen($filename)-4)==".ief")
	 {
	 ?>
    <tr>
      <th height="21" colspan="3" align="center" class="inserted" scope="row">Import Data From Your IEF File!!! </th>
    </tr>
	 <?php 
	}
	 ?>
    <tr>
      <td align="left" scope="row">
		<td align="left" scope="row"><?php 
		
		$filename=$_FILES['file']['name'];
	    if($filename=="")
	    {
	  	 	 echo "<strong>Please select a file to import</strong>&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	    }
	     else
	     {  
		     if(substr($filename,strlen($filename)-4)!=".ief")
			 {
			  echo "<strong>Please select only .ief file to import</strong>&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
			  include_once("admin.footer.inc.php");
			  exit(0);
			 }
            $day=date("l_dS_F_Y_h_i_A");
             mkdir("import/$day/",0777);
	         copy($_FILES['file']['tmp_name'],"import/$day/".$_FILES['file']['name']);
	         $handle = fopen ("import/$day/$filename", "r");
             $buffer = fgets($handle);
			 $buffer=str_replace("\r","",$buffer);
			 $buffer=str_replace("\n","",$buffer);
             if($buffer!="EmailsNotInAnyList"&&$buffer!="")
	         {
	         	 $result=mysql_query("select * from ".$table_prefix."email_advt_category where name='$buffer'");
	         	 if(mysql_num_rows($result)==0)
	          	 {
	            	 $time=time();
					 echo "Emails will be added to newly created list <span class=\"already\">".$buffer."</span>. Click the import button to continue!<br></br>";
	                 echo "<strong>You may select upto two more lists if you want to add the emails to additional lists.</strong><br></br>";
	             	 mysql_query("insert into ".$table_prefix."email_advt_category values('','$buffer','$time')"); 
					 $aid=0;
		              if(isset($_COOKIE['inout_sub_admin']))
		              {
		                $aid=getAdminId($mysql);
			            $id=$mysql->echo_one("select id from ".$table_prefix."email_advt_category  where name='$buffer'");
			             //$uid=getAdminId($mysql);
			             mysql_query("insert into ".$table_prefix."admin_access_control values('','$aid','$id')");
		                 }
		               if($log_enabled==1)
		                {
		            	 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','List created:".$buffer."','".time()."','$CST_MLM_LIST')");
		                }
				 }
				 else
				 {
				       $id=$mysql->echo_one("select id from ".$table_prefix."email_advt_category  where name='$buffer'");
					    if(isset($_COOKIE['inout_sub_admin']))
								{
	                              $subAdminId=getAdminId($mysql);
	                              if($mysql->total($table_prefix."admin_access_control","aid=$subAdminId and eid=$id ")==0)
								 			 {
											 echo "<br>You are trying to import a list named <span class=\"already\">".$buffer."</span>.You don't have access to this list.So you can't import specified file&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
											 include_once("admin.footer.inc.php");
											 exit(0);
								  			 }
								  }
					 echo "You already have a list named <span class=\"already\">".$buffer."</span>. If you want to add the emails to the existing list click the Import button.<br></br>";
					 echo "<strong>You may select upto two more lists if you want to add the emails to additional lists.</strong><br></br>";
				 }	 
	             $listid=$mysql->echo_one("select id from ".$table_prefix."email_advt_category where name='$buffer'");?>                    
    				<td align="left" scope="row">                    
    <tr>
   	 	 <td align="left" scope="row">
 	     <input type="hidden" name="category" value="<?php echo $listid; ?>">					 </td>
	 	 <td><select name="category2">
	 	   <option value="" selected>- Select a List - </option>
	 	   <?php $result=mysql_query("select * from ".$table_prefix."email_advt_category order by name"); 
							 if(isset($_COOKIE['inout_sub_admin']))
								{
	                              $subAdminId=getAdminId($mysql);
								   $result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
								  }
	            	  	 while($row=mysql_fetch_row($result))
				  		 {
	                	  	echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	              		 }?>
	     </select></td>
		 <td>&nbsp;</td>
    				</tr>
	          		<tr>
              			 <td align="left" scope="row">
	          			 <input type="hidden" name="emails" value="
						 <?php 
						  echo getFormattedMultipleEmail($handle,$table_prefix);
						 ?>">
						   <td><select name="category3">
                             <option value="" selected>- Select a List - </option>
                             <?php
							 $result=mysql_query("select * from ".$table_prefix."email_advt_category order by name"); 
							 if(isset($_COOKIE['inout_sub_admin']))
								{
	                              $subAdminId=getAdminId($mysql);
								   $result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
								  }
							 
							
	       				  while($row=mysql_fetch_row($result))
						  {
	        			 	echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	        			  }?>
                           </select></td>
                        <td>&nbsp;</td>
    				</tr>
						<?php
				}
              	else
                {
					  echo "<strong>Select upto 3 Email Lists where you want to add the emails. You can add emails to more lists later. </strong><br></br>";
					  ?>
					<tr>
                	  <td align="left" scope="row">&nbsp;					  </td>
       			   	   <td><select name="category">
                     <option value="" selected>- Select a List - </option>
                     <?php $result=mysql_query("select * from ".$table_prefix."email_advt_category order by name"); 
							 if(isset($_COOKIE['inout_sub_admin']))
								{
	                              $subAdminId=getAdminId($mysql);
								   $result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
								  }
	                 while($row=mysql_fetch_row($result))
					 {
	                 	 echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	                 }?>
                   </select>
				   <span class="style3">*</span>
				   </td>
              			 <td>&nbsp;</td>
                     </tr>
					 <tr>
                	 <td align="left" scope="row">
					  <input type="hidden" name="emails" value="<?php 

						  echo getFormattedMultipleEmail($handle,$table_prefix);
						 ?>">					  </td>
       			   <td><select name="category2">
                     <option value="" selected>- Select a List - </option>
                     <?php $result=mysql_query("select * from ".$table_prefix."email_advt_category order by name"); 
							 if(isset($_COOKIE['inout_sub_admin']))
								{
	                              $subAdminId=getAdminId($mysql);
								   $result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
								  }
	                 while($row=mysql_fetch_row($result))
					 {
	                 	 echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	                 }?>
                   </select></td>
              			 <td>&nbsp;</td>
                     </tr>
					 <tr>
                	 <td height="21" align="left" scope="row">&nbsp;					  </td>
       			   <td><select name="category3">
                     <option value="" selected>- Select a List - </option>
                     <?php $result=mysql_query("select * from ".$table_prefix."email_advt_category order by name"); 
							 if(isset($_COOKIE['inout_sub_admin']))
								{
	                              $subAdminId=getAdminId($mysql);
								   $result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
								  }
	                 while($row=mysql_fetch_row($result))
					 {
	                  	echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	                 }?>
                   </select></td>
              			 <td>&nbsp;</td>
                     </tr>

					 <?php
                    }
                     ?>
                   </td>
 
		
				 </tr>
           		 <?php
				 fclose ($handle);
	         }
			 function getFormattedMultipleEmail($handle,$table_prefix)
				 {
				 		 $str= array();
						 $i=0;
						 while(!feof($handle))
						 {
							 $str[$i]=fgets($handle);
							 $str[$i]=str_replace("\r","",$str[$i]);
				 			 $str[$i]=str_replace("\n","",$str[$i]);
							// echo " ".$str[$i];
							 $i+=1;
						 }
						  if($i>0)
						  	$i-=1;
						  $str[$i]="EOF";

						  $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
						  $fields=array();
						  $fields[0]="Email";
						  $fields[1]="Name";
						  $i=2;
						  while($fielddetails=mysql_fetch_row($extrafields))
						 {
						 	$fields[$i]= $fielddetails[1];
							$i+=1;
						 }
						 
						 $formattedInput="";
						 $i=0;

						 while($i<count($str))
						 {
						 	$subArray=array();
							$j=0;
							while($str[$i]!="<EOR>")
							{
							 	if($str[$i]=="EOF" )
								{
									break;
								}
								$subArray[$j]=$str[$i];
								$j+=1;
								$i+=1;
							}
							
							$j=0;
							while($j<count($fields))
							{
								$fieldFound=false;
								$k=0;
								while($k<count($subArray))
								{
									if(false == stristr($subArray[$k], $fields[$j].":"))
									{	
										$k+=1;
									}
									else
									{
										$fieldFound=true;
										$temp=str_replace( "\n"," ",$subArray[$k]);
										$temp=str_replace( "\r"," ",$temp);
										if($j==0)
										{
											$temp=str_replace( $fields[$j].":","",$temp);
										}
										else
										{
											$temp=str_replace( $fields[$j],"",$temp);
										}
										$formattedInput.=$temp;
										break;
									}
								}
								if($fieldFound==false)
								{
									if($j==0)//no email present in the particular record.
										break;
									else//no value for the particular field
										$formattedInput.=":";
								}
								$j+=1;
							}
							
							
							$formattedInput.="\n";
							$i++;
						 }
						 return $formattedInput;
				 }//end of getFormattedMultipleEmail()
	        ?>
			 <?php
				 				 if($filename!="")
								 {
								 	?> 
             <tr>
               <th align="left" scope="row">&nbsp;</th>
               <td>&nbsp;</td>
               <td >&nbsp;</td>
             </tr>
             <tr>
	             <th align="left" scope="row">&nbsp;</th>
	           <td><input type="submit" name="Submit" value="Import Data !"></td>
								 
    </tr>
		<?php
								 }
								?>
  </table>
</form>

<?php
include_once("admin.footer.inc.php");
?>
