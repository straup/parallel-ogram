<?php

	loadlib("instagram_likes");

	#################################################################

	function instagram_photos_permissions_can_view_photo($photo, $viewer_id=0){

		if (! $viewer_id){
			return 0;
		}

		if ($photo['user_id'] == $viewer_id){
			return 1;
		}

		# has the viewer 'liked' the photo? we'll just assume
		# that if we have a record of that then they can see
		# the photo (20120323/straup)

		if (instagram_likes_has_liked_photo($photo, $viewer_id)){
			return 1;
		}

		return 0;
	}

	#################################################################

?>
