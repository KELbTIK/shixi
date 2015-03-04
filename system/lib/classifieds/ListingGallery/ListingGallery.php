<?php


class SJB_ListingGallery
{
	var $listing_sid;
	var $error;
	var $upload_files_directory;
	
	function SJB_ListingGallery()
	{
		$this->upload_files_directory = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");
	}
	
	function setListingSID($listing_sid)
	{
		$this->listing_sid = $listing_sid;
	}
	
	function getPicturesInfo()
	{
		$pictures_info = SJB_DB::query("SELECT *, sid AS id FROM listings_pictures WHERE listing_sid = ?n ORDER BY `order`", $this->listing_sid);
		foreach ($pictures_info as $key => $picture_info) {
			$pictures_info[$key]['picture_url'] = $this->getPictureURLByInfo($picture_info);
			$pictures_info[$key]['thumbnail_url'] = $this->getThumbURLByInfo($picture_info);
		}
		return $pictures_info;
	}
	
	function updatePictureCaption($picture_sid, $picture_caption)
	{
		SJB_DB::query("UPDATE listings_pictures SET caption = ?s WHERE sid = ?n", $picture_caption, $picture_sid);
	}
	
	function getPictureInfoBySID($picture_sid)
	{
		$pictures_info = SJB_DB::query("SELECT * FROM listings_pictures WHERE sid = ?n ORDER BY `order`", $picture_sid);
		if (empty($pictures_info)) {
			return null;
		} else {
			$picture_info = array_pop($pictures_info);
			$picture_info['picture_url'] = $this->getPictureURLByInfo($picture_info);
			$picture_info['thumbnail_url'] = $this->getThumbURLByInfo($picture_info);
			return $picture_info;
		}
	}
	
	function moveUpImageBySID($image_sid)
	{
		$image_info = $this->getPictureInfoBySID($image_sid);
		$less_order = SJB_DB::query("SELECT * FROM listings_pictures WHERE `order` < ?n AND listing_sid = ?n ORDER BY `order` DESC LIMIT 1", $image_info['order'], $this->listing_sid);
		if (!empty($less_order)) {
			$less_order = array_pop($less_order);
			SJB_DB::query("UPDATE listings_pictures SET `order` = ?n WHERE sid = ?n", $image_info['order'], $less_order['sid']);
			SJB_DB::query("UPDATE listings_pictures SET `order` = ?n WHERE sid = ?n", $less_order['order'], $image_sid);
		}
	}
	
	function moveDownImageBySID($image_sid)
	{
		$image_info = $this->getPictureInfoBySID($image_sid);
		$more_order = SJB_DB::query("SELECT * FROM listings_pictures WHERE `order` > ?n AND listing_sid = ?n ORDER BY `order` ASC LIMIT 1", $image_info['order'], $this->listing_sid);
		if (!empty($more_order)) {
			$more_order = array_pop($more_order);
			SJB_DB::query("UPDATE listings_pictures SET `order` = ?n WHERE sid = ?n", $image_info['order'], $more_order['sid']);
			SJB_DB::query("UPDATE listings_pictures SET `order` = ?n WHERE sid = ?n", $more_order['order'], $image_sid);
		}
	}
	
	function getPicturesAmount()
	{
		$count = SJB_DB::queryValue("SELECT COUNT(*) FROM listings_pictures WHERE listing_sid = ?n", $this->listing_sid);
		if (empty($count))
			return 0;
		return $count;
	}
	
	function deleteImageBySID($image_sid)
	{
		$image_info = $this->getPictureInfoBySID($image_sid);
		@unlink($this->upload_files_directory . "/pictures/" . $image_info['picture_saved_name']);
		@unlink($this->upload_files_directory . "/pictures/" . $image_info['thumb_saved_name']);
		SJB_DB::query("DELETE FROM listings_pictures WHERE sid = ?n", $image_sid);
		$this->setListingPictureAmount($this->getPicturesAmount());
	}
	
	function setListingPictureAmount($pictures_amount)
	{
		SJB_DB::query("UPDATE listings SET pictures = ?n WHERE sid = ?n", $pictures_amount, $this->listing_sid);
	}
	
	function deleteImages()
	{
		$images_info = SJB_DB::query("SELECT sid FROM listings_pictures WHERE listing_sid = ?n", $this->listing_sid);
		foreach ($images_info as $image_info)
			$this->deleteImageBySID($image_info['sid']);
        return true;
	}
	
	function getPictureURLByInfo($picture_info)
	{
		return SJB_System::getSystemSettings("SITE_URL") . "/" . $this->upload_files_directory . "/pictures/" . $picture_info['picture_saved_name'];
	}
	
	function getThumbURLByInfo($thumb_info)
	{
		return SJB_System::getSystemSettings("SITE_URL") . "/" . $this->upload_files_directory . "/pictures/" . $thumb_info['thumb_saved_name'];
	}
	
	function uploadImage($image_file_name, $image_caption)
	{
		$image_info = getimagesize($image_file_name);		// $image_info['2'] = 1 {GIF}, 2 {JPG}, 3 {PNG}, 4 {SWF}, 5 {PSD}, 6 {BMP}, 7 {TIFF}, 8 {TIFF}, 9 {JPC}, 10 {JP2}, 11 {JPX}
		if ( $image_info['2'] >= 1 && $image_info['2'] <= 3 ) {
			if ($image_info['2'] == 1) {
				$image_resource = imagecreatefromgif($image_file_name);
			} elseif ($image_info['2'] == 2) {
				$image_resource = imagecreatefromjpeg($image_file_name);
			} else {
				$image_resource = imagecreatefrompng($image_file_name);
			}
			
			$picture_max_size['width']  = SJB_System::getSettingByName('listing_picture_width');
			$picture_max_size['height'] = SJB_System::getSettingByName('listing_picture_height');
			
			$picture_resource = $this->getResizedImageResource($image_resource, $picture_max_size);
			$thumb_max_size['width']  = SJB_System::getSettingByName('listing_thumbnail_width');
			$thumb_max_size['height'] = SJB_System::getSettingByName('listing_thumbnail_height');
			
			$thumb_resource = $this->getResizedImageResource($image_resource, $thumb_max_size);
			
			$max_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM listings_pictures WHERE `listing_sid` = ?n", $this->listing_sid);
			$max_order = (empty($max_order) ? 0 : $max_order);
			
			$order = $max_order + 1;

			$upload_file_directory = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY');
			$picture_sid = SJB_DB::query("INSERT INTO listings_pictures"
				. " SET `listing_sid` = ?n, `caption` = ?s, `order` = ?n"
				,	$this->listing_sid, mb_substr($image_caption, 0, 255), $order);
			$picture_file_basename = 'picture_'.$picture_sid.'.jpg';
			$thumb_file_basename = 'thumb_'.$picture_sid.'.jpg';
			$picture_name = $upload_file_directory . "/pictures/" . $picture_file_basename;
			$thumb_name = $upload_file_directory . "/pictures/" . $thumb_file_basename;
			imagejpeg($picture_resource, $picture_name);
			imagejpeg($thumb_resource, $thumb_name);
			SJB_DB::query("UPDATE listings_pictures SET `picture_saved_name` = ?s, `thumb_saved_name` = ?s WHERE sid = ?n",
						$picture_file_basename, $thumb_file_basename, $picture_sid);

			$this->setListingPictureAmount($this->getPicturesAmount());
		} else {
			$this->error = 'NOT_SUPPORTED_IMAGE_FORMAT';
			return false;
		}
	}

	function getResizedImageResource($image_resource, $image_max_size)
	{
		$image_width = imagesx($image_resource);	$image_height = imagesy($image_resource);
		if (($image_width > $image_max_size['width']) || ($image_height > $image_max_size['height'])) {
			$k_w = $image_width / $image_max_size['width'];
			$k_h = $image_height / $image_max_size['height'];
			$k = max($k_w, $k_h);
			$picture_width = round($image_width / $k);
			$picture_height = round($image_height / $k);
		} else {
			$picture_width = $image_width;
			$picture_height = $image_height;
		}
		$resized_image_resource = imagecreatetruecolor($picture_width, $picture_height);
		imagecopyresampled($resized_image_resource, $image_resource, 0, 0, 0, 0, $picture_width, $picture_height, $image_width, $image_height);
		return $resized_image_resource;
	}
	
	function getError()
	{
		return $this->error;
	}
}

