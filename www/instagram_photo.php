<?php

	include("include/init.php");

	# for now...

	login_ensure_loggedin();

	loadlib("instagram_photos");

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

	if ($owner['id'] != $GLOBALS['cfg']['user']['id']){
		error_403();
	}

	$GLOBALS['smarty']->assign_by_ref("owner", $owner);
	$GLOBALS['smarty']->assign_by_ref("photo", $photo);

	$GLOBALS['smarty']->display("page_instagram_photo.txt");
	exit();

?>
