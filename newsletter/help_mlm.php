<div>
<?php 
include("config.inc.php");
$id=1;
if(isset($_GET['id']))
	$id=$_GET['id'];
if($id==1)
{
echo nl2br("
<strong>Administrators</strong>
	 
<li>Create New Administrator</li>
		You can create sub-admins from here. Sub-admins can be given access to specific lists to manage campaigns to those lists.
			
<li>Manage Administrators</li>
You can manage sub-admins from this area. You can change email, block/activate, reset password and modify access control. This allows admin to have a control over sub admins.

 ");
}		
if($id==2)
{
echo nl2br("
<strong>Email Templates</strong>

	<li>New Email Template </li> 
Form this area you can create new email templates. Templates can be used while creating email campaigns.

	<li> Manage Email Templates</li> 
	 You can manage email templates here. You can edit or delete already created templates.
	");
}		
if($id==3)
{
echo nl2br("

<strong>Extra Subscription Fields</strong>

<li>Add an Extra Field</li>
			You can add new subscription fields from here. It will allow to accept more data while users subscribe and can be used for sending personalized emails. While adding extra fileds you can specify whether it should accept any data (text field) or a choice from a predefined option list ( html select )

	<li> Manage Existing Fields</li> 
		Extra fields and corresponding default values are listed here. You can edit/delete existing fields. 'Name' is a default parameter and cannot be removed. The deafult value for this parameter is specified in config.inc.php

");
}		
if($id==4)
{
echo nl2br("		
<strong>Add Email Addresses</strong>

Here you can add emails to the email list. You can add emails to the above list from various resources.  You can either add a single email, add many emails together, ask the script to automatically extract emails from the given HTML code or put the URLs from where you want to extract the emails.       

	<li>Add a Single Email</li> 
	You can add single email from here.Select upto 3 Email Lists where you want to add the emails. You can add emails to more lists later. You should select at least one list here. You can add more information if required.
		
	<li>Add Multiple Emails</li> 
		 You can add multiple emails from here.Select upto 3 Email Lists where you want to add the emails. You can give emails address and extra fields. Add extraparams after corresponding email address separated with a colon(:) or comma(,). Give new line as separator when you add more email addresses.
		
	<li>Import Emails from HTML </li> 
		You can  add emails embedded in html codes. Select the lists you need to add the emails ids and give the html code in the text area. All emails in the source code will be  identified and added to the database. 

	<li>Import Emails from MySQL DB</li> 
		You can  add emails from database. Fill the db and table details, system will extract the emails from the table and add it in to the system for the lists you selected.

<li>Import Emails from URLs</li> 
		Enter the urls from where you want to extract emails. Place one URL in one line. The system will extract emails from the urls you entered.

");
}		
if($id==5)
{
echo nl2br("		
		
<strong>Email Campaigns</strong>

Email campaigns are used to send out newsletters to your subscribers. Prepare an email subject and body and specify the list to which the campaign should be fired. You can use either plain text or html campaigns. Email campaigns will be created in pending state initially.  You can attach any number of files to a campaign. You need to activate the campaign after finalizing the content. Only active campaigns can be fired. You may inactivate an active campaign anytime. Also you can restart  active/inactive campaigns anytime.

<li>Create a New Email Campaign </li> 
		You can create new Email Campaign from here. Fill the details and select the list, to which you need to send emails. You can also specify filter rules based on users' personal info.
		
		<li>Manage all Email Campaigns </li>
All email campaigns in the system will be listed here. 

		<li>Manage Active Email Campaigns </li> 
		All active email campaigns in the system will be listed here. 

		<li>Manage Inactive Email Campaigns </li> 
		Inactive email campaigns in the system will be listed here. 

 ");
}		
if($id==6)
{
echo nl2br("		

<strong>Email Lists</strong>
	
		<li>Create a New List</li> 
		You can create new email list here. It will help to group emails. You can fire a specific campaign to a list.

	<li>Manage Email Lists</li> 
	You can see and manage all lists in the system from here. You can edit or delete list and view the active/unsubscribed emails in a list.

<li>Subscribe HTML Code </li> 
		 HTML subscription is the most common form of code. You can copy and place the code in any pages in your websites(s) for allowing  users to subscribe/unsubscribe to your lists.

		<li>Automatic Subscription Code (PHP)</li> 
		 This feature enables you to automatically subscribe emails which are entered through  some already existing forms in your site to any desired list. 
		");
}		
if($id==7)
{
echo nl2br("		

<strong>Manage Email Addresses</strong>

	<li>View all Emails</li> 
		You can view all emails registered in the system. You can list each emails in the list separately. You are allowed to modify the details of each emails if you want.

 <li>Manage Unsubscribed Emails </li> 
		Shows all unsubscribed emails in the system/list; you can change the details or delete it from the system.

 <li>Manage Active Emails</li> 
		You can see all active emails in the system or a specific list. 

 <li>Search Emails </li> 
		You can search emails form here.Enter the full email address or a part of an email address in search box. You can also search by id.

 ");

 if(!isset($_COOKIE['inout_sub_admin']))
echo nl2br("	 <li>Bulk Emails Unsubscription</li> 
		You can unsubscribe large number of email addresses at a time. Enter the full email addresses in the text area and press unsubscribe button.
		
 ");
}		
if($id==8)
{
echo nl2br("		

<strong>Create/Restore Backup</strong>

	<li> Export Lists as IEF (Inout Exported File) </li> 
		You can backup data to an IEF file from this area. Select the lists you wanted to backup and click export.

	<li> Export Email Lists as CSV</li> 
		CSV file backup can be performed from here. Select the lists you wanted to backup and click export. You can export the data in the order you give.

<li> Import from IEF (Inout Exported File)</li> 
		Give your IEF file and click the Proceed button. The system will add the details from the backup file to the system.

<li> Import Emails from CSV</li> 
		You can  add emails from csv files here. Please browse your csv or text based file and click extract, system will import the emails from the files.

		");
}		

if($id==9)
{
echo nl2br("		

<strong>Manage Bounced Emails</strong>

	<li>Manage Bounced Emails</li> 
		You will get the deatils of bounced mails from here. You may unsubscribe/delete them from the system.

	<li>Detect Bounced Emails </li> 
		You can identify  the bounced mails from here. Enter the imap/pop3 details of the account containing bounce reports and click proceed, bounce mail handler will parse mails in your mail box and identify bounce reports.

<li>BMH Rules</li> 
		Sometimes the system will not detect bounce reports with the preconfigured rules. In such cases you may create new rules from here. You need to be familiar with regular expressions for writing rules.
		");
}	

if($id==10)
{
echo nl2br("		

<strong>Activity Logs</strong>

	<li>Time Based Logs </li> 
		Activity logs sorted by time will be listed here. You can delete the old logs from here.

	<li> Administrator Logs </li> 
		You can view the administrator/sub admin logs.
		
		<li>Categorized Logs</li> 
		You can view category based logs. You can select different categories and view logs.
		");
}	
if($id==11)
{
echo nl2br("		

<strong>Instant and Scheduled Mailing </strong>

You may either fire your campaign manually or setup queue for your campaigns and create a cron job in your server to fire teh campaigns.  

	<li>Manage Email Queue</li> 
		You can manage number of emails you want to send in each batch for each campaign.

	<li>Send Emails </li> 
		You can select a campaign and fire the same or fire all campaigns at same time.
		
	<li>View Status Report</li> 
		It shows total system details like email lists, email addresses, email campaigns etc.
		");
}	
if($id==12)
{
echo nl2br("		

<strong>Quick User Guide</strong>

	<li> First Time User Guide </li> 
		This is a step by step guide to implement a basic solution for a first time user.

	<li>A Practical Example </li> 
		It explains how the script can enhance your business, meet all your email and mailing list management requirements. 
		
		<li>Script Settings</li> 
	All avaliable configuration information will be shown in this file. Please fill required information in config.inc.php
		");
}	
	?>	
</div>			