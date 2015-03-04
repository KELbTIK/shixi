<?php

class SJB_CommentManager extends SJB_ObjectManager
{
	public static function saveComment($comment)
	{
		SJB_CommentDBManager::saveComment($comment);
	}

	public static function deleteComment($comment)
	{
		return SJB_CommentDBManager::deleteComment($comment);
	}

	public static function getCommentsNumToListing($listing_sid)
	{
		return SJB_CommentDBManager::getCommentsNumToListing($listing_sid);
	}
	
	public static function getEnabledCommentsToListing($listing_sid)
	{
		$comments_raw = SJB_CommentDBManager::getEnabledCommentsToListing($listing_sid);
		return SJB_CommentManager::getCommentsInfo($comments_raw);
	}

	public static function getCommentsToListing($listing_sid)
	{
		$comments_raw = SJB_CommentDBManager::getCommentsToListing($listing_sid);
		return SJB_CommentManager::getCommentsInfo($comments_raw);
	}

	public static function getCommentsInfo($raw_comments)
	{
		$comments_tree = new SJB_CommentsTree($raw_comments);
		$comments_tree->build();
		$comments_to_listing = $comments_tree->toArray();
		$comments = array();
		foreach ($comments_to_listing as $comment) {
			if (intval($comment['user_id']) > 0) {
				$user = SJB_UserManager::getObjectBySID($comment['user_id']);
				$comment['user'] = SJB_UserManager::createTemplateStructureForUser($user);
			}
			$comment['added'] = strtotime($comment['added']);
			$comments[] = $comment;
		} 
		return $comments;
	}

	public static function getListingSIDByCommentSID($comment_sid)
	{
		return SJB_CommentDBManager::getListingSIDByCommentSID($comment_sid);
	}

	public static function enableComment($comment_id)
	{
		SJB_CommentDBManager::setDisabled(false, $comment_id);
	}

	public static function disableComment($comment_id)
	{
		SJB_CommentDBManager::setDisabled(true, $comment_id);
	}

	public static function deleteCommentsToListing($listing_sid)
	{
		SJB_CommentDBManager::deleteCommentsToListing($listing_sid);
	}  
}
