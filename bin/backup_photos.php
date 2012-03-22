<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	#

	include("include/init.php");
	loadlib("instagram_users");
	loadlib("instagram_photos_import");

	# see this? it's not done yet...

	$user_id = 'fix me';
	$user = instagram_users_get_by_user_id($user_id);
	$rsp = instagram_photos_import_for_user($user);

	exit();
?>
