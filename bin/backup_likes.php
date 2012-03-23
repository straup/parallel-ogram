<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	# Honestly, it feels a bit weird to do this in a backfill script
	# and I bet there will be enough administrivia around backups
	# (maybe?) that they will need to be moved in to their own table
	# but for now... it works. (20120321/straup)

	include("include/init.php");

	loadlib("backfill");
	loadlib("instagram_likes_import");

	function _backup($insta_user, $more=array()){

		$user = users_get_by_id($insta_user['user_id']);

		$more = array('force' => 1);

		$rsp = instagram_likes_import_for_user($user, $more);
		dumper($rsp);
	}

	$sql = "SELECT * FROM InstagramUsers WHERE backup_photos=1";
	backfill_db_users($sql, '_backup');

	exit();
?>
