<?php

class SJB_HTMLTagConverter
{
	function getConverted($string)
	{
		return htmlentities($string, ENT_QUOTES, "UTF-8");
	}
}

