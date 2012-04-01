<?php

	include("include/init.php");

	# for now...

	login_ensure_loggedin();

	loadlib("instagram_photos");
	loadlib("instagram_photos_permissions");

	if ($id = get_int64("id")){
		$photo = instagram_photos_get_by_id($id);
	}

	else if ($code = get_str("short_code")){
		$photo = instagram_photos_get_by_short_code($code);
	}

	else {
		error_404();
	}

	if (! $photo){
		error_404();
	}

	$owner = users_get_by_id($photo['user_id']);

	# for now...

	if (! instagram_photos_permissions_can_view_photo($photo, $GLOBALS['cfg']['user']['id'])){
		error_403();
	}

	$is_own = ($owner['id'] == $GLOBALS['cfg']['user']['id']);
	$GLOBALS['smarty']->assign("is_own", $is_own);

	# TO DO: check for 'with-FILTER' style params and update
	# query accordingly

	if ($is_own){
		$bookends = instagram_photos_get_bookends($photo);
		$GLOBALS['smarty']->assign_by_ref("bookends", $bookends);
	}

	$GLOBALS['smarty']->assign_by_ref("owner", $owner);
	$GLOBALS['smarty']->assign_by_ref("photo", $photo);

	$GLOBALS['smarty']->display("page_instagram_photo.txt");
	exit();

?>
