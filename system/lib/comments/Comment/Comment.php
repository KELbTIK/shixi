<?php

class SJB_Comment extends SJB_Object
{
	function SJB_Comment($comment_info, $listing_sid)
	{
		$this->db_table_name = 'comments';
		$fields = array('email', 'name', 'message');
		foreach ($fields as $field)
			if (isset($comment_info[$field]))
				$comment_info[$field] = htmlspecialchars($comment_info[$field]);

		$this->details = new SJB_CommentDetails($comment_info);
		$this->listing_id = $listing_sid;
	}
}
