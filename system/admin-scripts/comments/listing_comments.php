<?php

class SJB_Admin_Comments_ListingComments extends SJB_Function
{
	public function isAccessible()
	{
		$listingId = SJB_Request::getVar('listing_id', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
		$listingType = !in_array($listingTypeId, array('Resume', 'Job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel('manage_' . strtolower($listingType));
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_id = SJB_Request::getVar('listing_id', null);
		$tp = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['action'])) {
			$action = strtolower($_REQUEST['action']);

			$comment_id = SJB_Request::getVar('comment_id', null);
			if (is_null($listing_id) && !is_null($comment_id))
				$listing_id = SJB_CommentManager::getListingSIDByCommentSID($comment_id);

			$comment_ids = array();
			if (isset($_REQUEST['comment']) && is_array($_REQUEST['comment']))
				$comment_ids = array_keys($_REQUEST['comment']);
			else
				$comment_ids = array($comment_id);

			switch ($action) {
				case 'delete':
					foreach ($comment_ids as $comment_id)
						SJB_CommentManager::deleteComment($comment_id);
					break;
				case 'disable':
					foreach ($comment_ids as $comment_id)
						SJB_CommentManager::disableComment($comment_id);
					break;
				case 'enable':
					foreach ($comment_ids as $comment_id)
						SJB_CommentManager::enableComment($comment_id);
					break;
				case 'edit':
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
						SJB_DB::query("UPDATE `comments` SET `message` = ?s WHERE `sid` = ?n", $_REQUEST['message'], $comment_id);
					}
					else {
						$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
						$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listingInfo['listing_type_sid']);
						$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
						$tp->assign('comment', SJB_CommentManager::getObjectInfoBySID('comments', $comment_id));
						$tp->display('edit_comment.tpl');
						return;
					}
					break;
			}

			header('Location: ' . SJB_System::getSystemSettings('SITE_URL') . '/listing-comments/?listing_id=' . $listing_id);
			exit;
		}

		if (!is_null($listing_id)) {
			$comments = SJB_CommentManager::getCommentsToListing($listing_id);
			$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
			$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listingInfo['listing_type_sid']);

			$tp->assign('comments', $comments);
			$tp->assign('comments_num', count($comments));
			$tp->assign('listing_id', $listing_id);
			$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));

			$tp->display('listing_comments.tpl');
		}
	}
}
