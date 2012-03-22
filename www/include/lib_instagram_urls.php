<?php

	loadlib("storage");

	#################################################################

	function instagram_urls_for_photo($photo){

		# This is a *total hack* until I can work out how to
		# fix this in flamework proper (20120321/straup)

		$photo_root = $GLOBALS['cfg']['abs_root_url'] . "static/";

		$photo_path = storage_id_to_path($photo['id']);
		$photo_fname = "{$photo['id']}_{$photo['secret']}.jpg";

		return $photo_root . $photo_path . "/" . $photo_fname;
	}

	#################################################################

?>
