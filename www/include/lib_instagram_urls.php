<?php

	loadlib("storage");
	loadlib("instagram_users");

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

	function instagram_urls_for_photo_page($photo){

		$user = users_get_by_id($photo['user_id']);
		$root = instagram_urls_for_user_photos($user);

		return $root . "{$photo['id']}/";
	}

	#################################################################

	function instagram_urls_for_user_photos($user){

		$root = instagram_urls_for_user($user);
		return $root . "photos/";
	}

	#################################################################

	function instagram_urls_for_user($user){

		$insta_user = instagram_users_get_by_user_id($user['id']);

		return $GLOBALS['cfg']['abs_root_url'] . "user/{$insta_user['instagram_id']}/";
	}

	#################################################################
?>
