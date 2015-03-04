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
error_reporting(0);
include("admin.header.inc.php");
?>
<table width="90%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="15%">&nbsp;</td>
    <td width="82%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>A Practical Example </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td> <p>Let's see how the script can enhance your business and meet all your email and mailing list management requirements. <br>
      <br>
      Suppose you have a website or you are planning to start one for classifieds with different categories. <br>
      <br>
      First of all, you need to find out those areas where  the script will be helpful to you.. 
        For example, the script can be used for your email advertising needs; it can be used for regular newsletter management for your home page; it can be used to send emails to your members; you can use the script for your members to refer a friend.You can  create separate newsletters for your each classified categories. Also the script can create  client side html interface and manage the emails that they subscribe. You may find many other areas where you can use the script. Now let's see how you can implement the above requirements. <br>
        <br>
        Considering the above prepared list, create some email Lists. Create lists 'email advertising' , ' newsletters' , 'refer a friend' , 'members email' , 'classified category 1 ' , 'classified category 2'..etc <br>
        <br>
      Once you have finished creating the lists, you need to add emails to the corresponding lists from various resources. You NEED NOT add any emails to the newsletter and refer a friend lists because people can directly insert their emails to the database if you paste the HTML code generated for those Lists in your site pages. <br>
      <br>
      Now you have to prepare email subject and to send emails to the corresponding email lists. Email campaigns will allow you to do this. Create different email campaigns for different Lists. Set various parameters regarding with the email when you create a new email campaign. <br>
      <br>
      The settings are over.<?php
if(!isset($_COOKIE['inout_sub_admin']))
{
?> Now you can set a cron job using your website control panel to the cron.php file to send emails. The system will send emails automatically according to the campaigns created.<?php
	 }
	 ?>    You may click on 'Send Emails Now' button in admin area homepage to manually run the cron file. When an email campaign is over, the system will send emails only to other campaigns in its next run. <br>
      <br>
      You may have 100 other websites. You may  install the script in those all sites or run the script from one domain for those sites. With our multiple site license you are free to use the script in and for any other site which is under your ownership.
</p>
<?php
if(!isset($_COOKIE['inout_sub_admin']))
{
?>
      <p>You can also create different administrators and define privilege to manage different lists .Each administrator can  manage only the lists assigned to him. He can add new email ids to those lists , remove email ids from those lists, fire campaigns on those lists. You can redefine the access privilege any time.</p>
      <p>Also from your admin area you can view the complete logs of any activity happening in your system . You can see activity logs for each administrator as well as as each entity.You can even track any unauthorized access attempts done by any of your administrators. <br>
        <br>
      </p>
	 <?php
	 }
	 ?>       </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><a href="tutor1.php" class="mainmenu">First Time User Guide</a> </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php include("admin.footer.inc.php"); ?>