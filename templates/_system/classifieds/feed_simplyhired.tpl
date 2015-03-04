<?xml version="1.0" encoding="utf-8"?>
<jobs>
{foreach from=$listings item=listing}
<job>
	<title><![CDATA[{$listing.Title}]]></title>
	<job-code>{$listing.id}</job-code>
	<action/>
	<job-board-name>SmartJobBoard</job-board-name>
	<job-board-url><![CDATA[{$GLOBALS.site_url}]]></job-board-url>
	<detail-url><![CDATA[{$listing.listing_url}]]></detail-url>
	<apply-url/>
	<job-category><![CDATA[{foreach from=$listing.JobCategory item=list_value name="multifor"}{tr}{$list_value}{/tr}{if !$smarty.foreach.multifor.last}, {/if}{/foreach}]]></job-category>

	<description>
		<summary><![CDATA[{$listing.JobDescription|strip_tags:false}]]></summary>
		<required-skills><![CDATA[{$listing.JobRequirements|strip_tags:false}]]></required-skills>
		<required-education/>
		<required-experience/>
		
		<full-time><![CDATA[{$listing.myEmploymentType.Fulltime}]]></full-time>
		<part-time><![CDATA[{$listing.myEmploymentType.Parttime}]]></part-time>
		<flex-time/>
		<internship><![CDATA[{$listing.myEmploymentType.Intern}]]></internship>
		<volunteer/>
		<exempt/>
		<contract><![CDATA[{$listing.myEmploymentType.Contractor}]]></contract>
		<permanent/>
		<temporary><![CDATA[{$listing.myEmploymentType.Seasonal}]]></temporary>
		<telecommute/>
	</description>

	<compensation>
		<salary-range/>
		<salary-amount><![CDATA[{$listing.Salary.value} {foreach from=$listing.SalaryType item=list_value name="multifor"}{tr}{$list_value}{/tr}{if !$smarty.foreach.multifor.last}, {/if}{/foreach}]]></salary-amount>
		<salary-currency></salary-currency>
		<benefits/>
	</compensation>

	<posted-date>{$listing.activation_date}</posted-date>
	<close-date>{$listing.expiration_date}</close-date>

	<location>
		<address><![CDATA[{$listing.Address}]]></address>
		<city><![CDATA[{$listing.Location.City}]]></city>
		<state><![CDATA[{$listing.Location.State}]]></state>
		<zip><![CDATA[{$listing.Location.ZipCode}]]></zip>
		<country><![CDATA[{$listing.Location.Country}]]></country>
		<area-code/>
	</location>

	<contact>
		<name><![CDATA[{$listing.user.ContactName}]]></name>
		<email>{$listing.user.email}</email>
		<hiring-manager-name/>
		<hiring-manager-email/>
		<phone>{$listing.user.PhoneNumber}</phone>
		<fax/>
	</contact>

	<company>
		<name><![CDATA[{$listing.user.CompanyName}]]></name>
		<description><![CDATA[{$listing.user.CompanyDescription|strip_tags:false}]]></description>
		<industry/>
		<url><![CDATA[{$listing.user.WebSite}]]></url>
	</company>
</job>
{/foreach}
</jobs>