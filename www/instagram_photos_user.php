<?php

	include("include/init.php");

	loadlib("instagram_users");
	loadlib("instagram_photos");
	loadlib("instagram_urls");

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

	if ($owner['id'] != $GLOBALS['cfg']['user']['id']){
		error_403();
	}

	$more = array(
		'per_page' => 9
	);

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}
	
	$rsp = instagram_photos_for_user($owner, $more);
	$GLOBALS['smarty']->assign_by_ref("photos", $rsp['rows']);

	$GLOBALS['smarty']->display("page_instagram_photos_user.txt");
	exit();
?>
