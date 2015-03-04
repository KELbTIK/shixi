function deletePicture(listingId, pictureId, accessType) {

	var url = window.SJB_GlobalSiteUrl;

	if (accessType == 'admin') {
		var url = window.SJB_AdminSiteUrl;
	}

	if (confirm(confirmPhrase) ) {
		var options = {
			target: "#UploadPics",
			url: url + '/manage-pictures/?listing_sid=' + listingId + '&action=delete&picture_id=' + pictureId,
			beforeSend: function(data) {
				$("#UploadPics").css({"opacity" : "0.3"});
                $("#loading-progbar").css("display", "block");
			},
			success: function(data) {
				$("#UploadPics").css({"opacity" : "1"});
                $("#loading-progbar").css("display", "none");
			}
		};
		$("#messageBox").ajaxSubmit(options);
	}
};

function editPicture(listingId, pictureId, Title, accessType) {
	var params = 'listing_id=' + listingId + '&picture_id=' + pictureId;
	var url    = window.SJB_UserSiteUrl + '/classifieds/edit-picture/?' + params;

	if (accessType == 'admin') {
		url = window.SJB_AdminSiteUrl + '/edit-picture/?' + params;
		popUpWindow(url, 300, 200, Title);
	} else {
		popUpWindow(url, 320, Title);
	}
}

function loadPicturesForm($url) {
	if (url != undefined) {
		$.ajax({
			url: url,
			beforeSend: function() {
				$("#UploadPics").hide();
			},
			success: function(data) {
				$("#UploadPics").html(data);
				$("#UploadPics").show();
			}
		});
	}
}