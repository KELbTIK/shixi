<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php
/*

 "mysql.cls.php" this file is a class which supports some basic mysql operation.
  All rights reserved and all the rights of the file and script goes to Jacob Baby[jacobbbc@yahoo.co.in] only.
  
  Created on:22/8/2005
  Modified on:23/8/2005
  
*/


/*

Funtion HELP

1) mysql(SERVER NAME, MYSQL USERNAME, MYSQL PASSWORD, MYSQL DATABASE NAME)
2) select_one_row(QUERY)
	This funtion reurns one row according to the sql query given as the argument.
	eg: $row=$mysql->select_one_row("select * from articles");
3) select_many_rows(QUERY,[NUMBER OF ROWS])
	This funtion reurns more than one  row according to the sql query given as the argument.
	eg: $row=$mysql->select_many_rows("select * from articles",5);  -> returns 5 rows and $row[0][0] represents the first field.
	    Also $row=$mysql->select_many_rows("select * from articles") -> return all articles.
4) select_last_row(TABLE NAME, PRIMARY KEY NAME)
	Returns last row.
5) select_last_key(TABLE NAME, PRIMARY KEY NAME)
	Returns the primary key value of the last element of a table.
6) total(TABLE NAME [, CONDITION])
	Returns the total number of rows of the table with/without a condition.

7) echo_one(QUERY); // used to return one field depending upon the query given. 
*/

class mysql
{
	 
	function mysql($server="localhost",$username="root",$password="",$dbname)
	{	
 		mysql_connect($server, $username, $password) or
 		die("Could not connect: " . mysql_error());
		mysql_select_db($dbname);
		global $charset_encoding;
		if(strcasecmp($charset_encoding, "UTF-8") == 0)
			{
				mysql_query("set names utf8 collate utf8_unicode_ci");
			}	
	}
	
	
	function select_one_row($query)
	{
		$result=mysql_query($query);
		echo mysql_error();
		if(mysql_num_rows($result)==0)
			return false;
		else
			{
			$row=mysql_fetch_row($result);
			return $row;
			}
		
	}
	
		
	function select_many_rows($query,$count="0")
	{
		$result=mysql_query($query);
		echo mysql_error();
		if(mysql_num_rows($result)==0)
			return false;
		else
			{
				if($count==0)
				{	
					$i=0;
					while($row[$i]=mysql_fetch_row($result))
						$i+=1;
					return $row;
				}
				else
				{
					$i=0;
					while(($row[$i]=mysql_fetch_row($result)) && ($i<($count-1)))
						$i+=1;
					return $row;
					
				}
			}
		
	}	
	
	
	function select_last_row($tablename,$primarykey)
	{
		$result=mysql_query("SELECT * FROM $tablename order by $primarykey DESC LIMIT 1");
		echo mysql_error();
		if(mysql_num_rows($result)==0)
			return false;
		else
			{
			$row=mysql_fetch_row($result);
			return $row;
			}
		
	}
	
	
	function select_last_key($tablename,$primarykey)
	{
		$result=mysql_query("SELECT $primarykey FROM $tablename order by $primarykey DESC LIMIT 1");
		echo mysql_error();
		if(mysql_num_rows($result)==0)
			return false;
		else
			{
			$row=mysql_fetch_row($result);
			$value=$row[0];
			return $value;
			}
		
	}
	
	function total($tablename,$condition="")
	{
		if($condition!="")
			$condition=" WHERE ".$condition;
		$result=mysql_query("SELECT count(*) FROM $tablename $condition");
		echo mysql_error();
		$row=mysql_fetch_row($result);
		return $row[0];
	}
	
	
	function echo_one($query)
	{
		$result=mysql_query($query);
		echo mysql_error();
		if(mysql_num_rows($result)==0)
			return false;
		else
			{
			$row=mysql_fetch_row($result);
			return $row[0];
			//echo $row[0];
			}
		
	}
	
	
}
?>