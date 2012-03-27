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
	loadlib("instagram_photos_import");

	function _backup($user, $more=array()){

		echo "backup photos for {$user['username']}\n";

		$photos_more = array(
			'per_page' => 1
		);

		$import_more = array();

		if ($last_update = json_decode($user['backup_last_update'], 'as hash')){

			$rsp = instagram_photos_for_user($user, $photos_more);

			$import_more['min_timestamp'] = $rsp['rows'][0]['created'];
		}

		$rsp = instagram_photos_import_for_user($user, $import_more);
		dumper($rsp);

		if ($rsp['ok']){
			$update = array('backup_last_update' => json_encode($rsp));
			users_update_user($user, $update);
		}
	}

	$sql = "SELECT * FROM users WHERE backup_photos=1";
	backfill_db_main($sql, '_backup');

	exit();
?>
