<?php

class SJB_NewsArticle extends SJB_Object
{
	
	public function __construct($articleInfo = array())
	{
		$this->db_table_name = 'news';
		$this->details = new SJB_NewsArticleDetails($articleInfo);
		$this->categoryId = isset($articleInfo['category_id'])   ? $articleInfo['category_id']   : 0;
	}
	
	public function getCategoryId()
	{
		return $this->categoryId;
	}
}