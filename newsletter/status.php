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
error_reporting(0);
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

?><?php include("admin.header.inc.php");?>




<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="3%">&nbsp;</td>
    <td width="66%">&nbsp;</td>
    <td width="31%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Current Status Report</strong></td>
    <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td colspan="2">         <table width="96%"  border="0" cellspacing="2" cellpadding="0" bgcolor="#EEEEEE">
         <tr>
           <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
             <tr bgcolor="#CCCCCC">
               <td height="30" colspan="3"> <span class="inserted">&nbsp;Email Lists</span> [<?php 
			    if(isset($_COOKIE['inout_sub_admin']))
				{
	                              
	                 $subAdminId=getAdminId($mysql);
					 $relt=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
					 $n=mysql_num_rows($relt);
					 echo $n;
				}
				else
				{
					 echo $mysql->total("".$table_prefix."email_advt_category");
				} ?> Lists] </td>
             </tr>
           
             <tr>
               <td height="30"><strong>&nbsp;Name of List </strong></td>
               <td>&nbsp;</td>
               <td align="center"><strong>Number&nbsp;of&nbsp;active&nbsp;Emails</strong></td>
              </tr>
            
             <?php $count=0; $single=0;
			   $result=mysql_query("select * from ".$table_prefix."email_advt_category order by name"); 
			   if(isset($_COOKIE['inout_sub_admin']))
			   {
	                  $subAdminId=getAdminId($mysql);
					  $result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.id=b.eid order by a.name");
			   }
	           while($row=mysql_fetch_row($result))
			   {
			  ?>
               <tr <?php if(($single%2)==0) { ?>bgcolor="#FFFFFF"<?php }?>>
               <td height="25" style="border-bottom:1px solid #CCCCCC; ">&nbsp;<?php echo  $row[1]; ?></td>
               <td style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
               <td align="center" valign="middle" style="border-bottom:1px solid #CCCCCC; ">
			  <?php //echo $mysql->total("".$table_prefix."ea_em_n_cat".",".$table_prefix."email_advt",$table_prefix."ea_em_n_cat.cid=$row[0] AND ");
			   
		  		$tot= $mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$row[0]");
			    echo $tot;
	   		    $count+=$tot;
			   
			  ?></td>
               </tr>
              <?php $single+=1; 
			   }
			  ?>
             <tr>
               <td>&nbsp;</td>
               <td>&nbsp;</td>
               <td><hr width="35%" size="1"></td>
              </tr>
             <tr>
               <td> &nbsp;Total effective email addresses </td>
               <td>&nbsp;</td>
               <td align="center"><?php echo $count; ?></td>
              </tr>
           </table></td>
         </tr>
       </table></td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td colspan="2" rowspan="2"><table width="96%"  border="0" cellspacing="2" cellpadding="0" bgcolor="#EEEEEE">
         <tr>
           <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
               <tr bgcolor="#CCCCCC">
                 <td height="30" colspan="3"><strong><span class="inserted">&nbsp;Email Addresses</span></strong><span class="info">(Not considering single email in multiple lists) </span></td>
               </tr>
               <tr>
                 <td width="813">&nbsp;</td>
                 <td width="19">&nbsp;</td>
                 <td width="457">&nbsp;</td>
               </tr>
               <tr>
                 <td>Total active email addresses</td>
                 <td>&nbsp;</td>
                 <td align="center"><?php 
				if(isset($_COOKIE['inout_sub_admin']))
				{
	                              
	                $subAdminId=getAdminId($mysql);
					$relt="select count(distinct a.id) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
					inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
					where a.id= b.eid and a.unsubstatus=0 AND b.unsubstatus=0";
					$total=$mysql->echo_one($relt);
				    echo $total;
			    }
			    else
			    {
				    echo $mysql->total("".$table_prefix."email_advt"," unsubstatus=0");
			    }
				 ?></td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td>Total unsubscribed email addresses</td>
                 <td>&nbsp;</td>
                 <td align="center">
				 <?php 
				 if(isset($_COOKIE['inout_sub_admin']))
				 {
                      $subAdminId=getAdminId($mysql);
 					  $relt="select count(distinct a.id) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
					  inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
					  where a.id= b.eid and a.unsubstatus=1";
					  $total=$mysql->echo_one($relt);
					  echo $total;
				 }
				 else
				 {
					  echo $mysql->total("".$table_prefix."email_advt"," unsubstatus=1");
				 }
				 ?></td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
               <tr>
                 <td>Total email addresses</td>
                 <td>&nbsp;</td>
                 <td align="center"><?php 
				  if(isset($_COOKIE['inout_sub_admin']))
					{
	                              
	                   $subAdminId=getAdminId($mysql);
					   $relt="select count(distinct a.id) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
					   inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
				       where a.id= b.eid";
					   $total=$mysql->echo_one($relt);
					   echo $total;
				   }
				  else
				   {
					   echo $mysql->total("".$table_prefix."email_advt");
				   }
				 ?></td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
           </table></td>
         </tr>
       </table></td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td colspan="2"><table width="96%"  border="0" cellspacing="2" cellpadding="0" bgcolor="#EEEEEE">
         <tr>
           <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
               <tr bgcolor="#CCCCCC">
                 <td height="30" colspan="3"><strong><span class="inserted">&nbsp;Email Campaigns </span></strong></td>
               </tr>
               <tr>
                 <td width="813">&nbsp;</td>
                 <td width="21">&nbsp;</td>
                 <td width="455">&nbsp;</td>
               </tr>
               <tr>
                 <td>Total number of active email campaigns </td>
                 <td>&nbsp;</td>
                 <td align="center"><?php 
				 if(isset($_COOKIE['inout_sub_admin']))
			     {
			   	   $subAdminId=getAdminId($mysql);
				   echo $mysql->echo_one("SELECT count(distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join 
					( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
					on a.id=b.cid where a.status=1");
			     }	
			     else
			     {
					 echo $mysql->total("".$table_prefix."email_advt_curr_run","status=1"); 
			     }
				 
				 ?></td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td>Total number of inactive email campaigns </td>
                 <td>&nbsp;</td>
                 <td align="center"><?php 
				 if(isset($_COOKIE['inout_sub_admin']))
			     {
			   	   $subAdminId=getAdminId($mysql);
				   echo $mysql->echo_one("SELECT count(distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join 
					( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
					on a.id=b.cid where a.status=0");
			     }	
			     else
			     {
					 echo $mysql->total("".$table_prefix."email_advt_curr_run","status=0"); 
			     }
				  ?></td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td>Total number of pending email campaigns </td>
                 <td>&nbsp;</td>
                 <td align="center"><?php 
				 if(isset($_COOKIE['inout_sub_admin']))
			     {
			   	   $subAdminId=getAdminId($mysql);
				   echo $mysql->echo_one("SELECT count(distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join 
					( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
					on a.id=b.cid where a.status=-1");
			     }	
			     else
			     {
					 echo $mysql->total("".$table_prefix."email_advt_curr_run","status=-1"); 
			     }
				  ?>&nbsp;</td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
               <tr>
                 <td>Total number of email campaigns </td>
                 <td>&nbsp;</td>
                 <td align="center"><?php
				 if(isset($_COOKIE['inout_sub_admin']))
			     {
			   	   $subAdminId=getAdminId($mysql);
				   echo $mysql->echo_one("SELECT count(distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join 
					( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
					on a.id=b.cid ");
			     }	
			     else
			     {
				  echo $mysql->total("".$table_prefix."email_advt_curr_run");
				 }
				   ?></td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
           </table></td>
         </tr>
       </table></td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td colspan="2"><table width="96%"  border="0" cellspacing="2" cellpadding="0" bgcolor="#EEEEEE">
         <tr>
           <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
               <tr bgcolor="#CCCCCC">
                 <td height="30" colspan="5"><strong><span class="inserted">&nbsp;Active Campaigns </span></strong> [<?php 
				 if(isset($_COOKIE['inout_sub_admin']))
			     {
			   	   $subAdminId=getAdminId($mysql);
				   echo $mysql->echo_one("SELECT count(distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join 
					( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
					on a.id=b.cid where a.status=1");
			     }	
			     else
			     {
					 echo $mysql->total("".$table_prefix."email_advt_curr_run","status=1"); 
			     }				 
				 ?> Campaigns] </td>
               </tr>
               
               <tr>
                 <td height="30"><strong>Campaign</strong></td>
                 <td><strong>Email&nbsp;List&nbsp;&nbsp;&nbsp; </strong></td>
                 <td><strong>%&nbsp;Completed&nbsp;</strong>&nbsp;&nbsp;&nbsp;</td>
                 <td><strong>Emails&nbsp;Sent</strong>&nbsp;&nbsp;&nbsp;</td>
                 <td><strong>Remaining</strong></td>
               </tr>
               
               <?php 
			   $single=0;
			   $result=mysql_query("select * from ".$table_prefix."email_advt_curr_run where status=1");
			   if(isset($_COOKIE['inout_sub_admin']))
			   {
			   	   $subAdminId=getAdminId($mysql);
				   $result=mysql_query("SELECT a.* FROM ".$table_prefix."email_advt_curr_run a inner join 
					( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
					on a.id=b.cid where a.status=1 group by a.id  order by a.id desc ");
			   }	
			   while($row=mysql_fetch_row($result))
	 		   { ?>
               <tr <?php if(($single%2)==0) { ?>bgcolor="#FFFFFF"<?php }?>>
                 <td height="25" style="border-bottom:1px solid #CCCCCC; ">&nbsp;<?php  if( $row[11]=="") echo "&lt;  Subject : $row[4] &gt;"; else echo $row[11]; ?></td>
                 <td style="border-bottom:1px solid #CCCCCC; "><?php 
				if($mysql->total("".$table_prefix."ea_cnc","campid=$row[0]")!=0)
				{
		
					echo $mysql->echo_one("select ".$table_prefix."email_advt_category.name from ".$table_prefix."email_advt_category,".$table_prefix."ea_cnc where ".$table_prefix."email_advt_category.id=".$table_prefix."ea_cnc.catid and ".$table_prefix."ea_cnc.campid=$row[0]");
					$catid=$mysql->echo_one("select ".$table_prefix."email_advt_category.id from ".$table_prefix."email_advt_category,".$table_prefix."ea_cnc where ".$table_prefix."email_advt_category.id=".$table_prefix."ea_cnc.catid and ".$table_prefix."ea_cnc.campid=$row[0]");
		
		   
					if($row[13]=="")
						{
							$tot=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$catid ");
						}
			else
					{
						$rule ="b.name='".$row[13]."' and b.value " .$row[14]." '".$row[15]."'";
						$tot=$mysql->echo_one("select count(*) from ".$table_prefix."ea_em_n_cat a, ".$table_prefix."ea_extraparam b where a.eid=b.eid  AND a.cid=$catid  AND  a.unsubstatus=0  AND $rule");
					//	echo "select count(*) from ".$table_prefix."ea_em_n_cat a, ".$table_prefix."ea_extraparam b where a.eid=b.eid  AND a.cid=$catid  AND  a.unsubstatus=0  AND $rule";
					}
		  $sent=$row[12];
		  if($sent>$tot)
			  $sent=$tot;
		 // $tot= $mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$catid and b.id>$row[3]");
		  $rem= $tot-$sent;
		
				}
				else 
				{
					echo "All Emails";
		
							if($row[13]=="")
								{
								$tot=$mysql->total("".$table_prefix."email_advt","unsubstatus=0 ");
								}
								else
								{
									$rule ="b.name='".$row[13]."' and b.value " .$row[14]." '".$row[15]."'";
									$tot=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_extraparam b where a.id=b.eid   AND  a.unsubstatus=0  AND $rule");
								}
					
		$sent=$row[12];
		  if($sent>$tot)
			  $sent=$tot;
		$rem=$tot-$sent;
					$subAdminId=$mysql->echo_one("select aid from ".$table_prefix."campaign_access_control where cid=$row[0] and access_status=1");
				if($subAdminId!="")
					//if(isset($_COOKIE['inout_sub_admin']))
					{
				
					
					//	$subAdminId=getAdminId($mysql);
						$sent=$mysql->echo_one("SELECT count(distinct a.id ) FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
						( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
						 where a.unsubstatus=0 and b.unsubstatus=0 and  a.id= b.eid and b.id<=$row[3] order by a.id ");
						
						$rem=$mysql->echo_one("SELECT count(distinct a.id ) FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
						( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
						 where a.unsubstatus=0 and b.unsubstatus=0 and  a.id= b.eid and b.id>$row[3] order by a.id ");
					}

				}
				$perc=round(($sent/($sent+$rem))*100,1);
  				?>&nbsp;</td>
                 <td align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $perc;?>&nbsp;</td>
                 <td align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $sent;?>&nbsp;</td>
                 <td align="left" valign="middle" style="border-bottom:1px solid #CCCCCC; "><?php echo $rem;?>&nbsp;</td>
               </tr>

               <?php 
			   $single+=1;
			}?>
           </table></td>
         </tr>
       </table></td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td><br>&nbsp;<a href="main.php">Back</a></td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
</table>

<?php 



?>
<?php include("admin.footer.inc.php");?>