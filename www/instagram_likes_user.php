<?php

	include("include/init.php");

	loadlib("instagram_users");
	loadlib("instagram_likes");

	login_ensure_loggedin();

	$id = get_int32("instagram_id");

	if (! $id){
		error_404();
	}

	$instagram_user = instagram_users_get_by_id($id);

	if (! $instagram_user){
		error_404();
	}

	$owner = users_get_by_id($instagram_user['user_id']);

	# for now...

	if ($owner['id'] != $GLOBALS['cfg']['user']['id']){
		error_403();
	}

	$more = array(
		'per_page' => 3
	);

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	if ($photo_owner_id = get_int32("photo_owner_id")){

		if ($instagram_user = instagram_users_get_by_id($photo_owner_id)){
			$more['owner_id'] = $instagram_user['user_id'];

			$photo_owner = users_get_by_id($instagram_user['user_id']);
			$GLOBALS['smarty']->assign_by_ref("photo_owner", $photo_owner);
		}
	}

	$rsp = instagram_likes_for_user($owner, $more);

	$GLOBALS['smarty']->assign_by_ref("photos", $rsp['rows']);
	$GLOBALS['smarty']->assign_by_ref("owner", $owner);

	$GLOBALS['smarty']->display("page_instagram_likes_user.txt");
	exit();
?>
