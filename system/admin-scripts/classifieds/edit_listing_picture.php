<?php


class SJB_Admin_Classifieds_EditPicture extends SJB_Function
{
	public function execute()
	{
		$pictureId = isset($_REQUEST['picture_id']) ? $_REQUEST['picture_id'] : null;
		$listingId = isset($_REQUEST['listing_id']) ? $_REQUEST['listing_id'] : null;
		$tp = SJB_System::getTemplateProcessor();

		if ($pictureId && $listingId) {
			$gallery = new SJB_ListingGallery();
			$pictureInfo = $gallery->getPictureInfoBySID($pictureId);
			$pictureCaption = SJB_Request::getVar('picture_caption', false);

			if ($pictureCaption) {
				$pictureInfo['caption'] = $pictureCaption;
				$gallery->updatePictureCaption($pictureId, $pictureCaption);
			}

			$tp->assign('picture', $pictureInfo);
			$tp->assign('listing_id', $listingId);
			$tp->assign('picture_id', $pictureId);
			$tp->display('edit_picture.tpl');
		}
	}
}
