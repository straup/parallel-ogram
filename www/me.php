<?php

	include("include/init.php");
	loadlib("instagram_users");

	$whoami = $_SERVER['REQUEST_URI'];
	login_ensure_loggedin($whoami);

	$insta_user = instagram_users_get_by_user_id($GLOBALS['cfg']['user']['id']);

	if (! $insta_user){
		error_404();
	}

	$path = get_str("path");

	# honestly, who the fuck knows... none of this stuff
	# exists on instagram (20120321/straup)

	$url = "{$GLOBALS['cfg']['abs_root_url']}user/{$insta_user['instagram_id']}/{$path}";

	header("location: {$url}");
	exit();
?>
