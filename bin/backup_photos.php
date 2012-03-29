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
	loadlib("instagram_backups");
	loadlib("instagram_photos_import");

	function _backup($backup, $more=array()){

		$user = users_get_by_id($backup['user_id']);
		echo "backup photos for {$user['username']}\n";

		$photos_more = array(
			'per_page' => 1
		);

		$import_more = array();

		if ($last_update = json_decode($backup['details'], 'as hash')){

			$rsp = instagram_photos_for_user($user, $photos_more);

			$import_more['min_timestamp'] = $rsp['rows'][0]['created'];
		}

		$rsp = instagram_photos_import_for_user($user, $import_more);
		dumper($rsp);

		if ($rsp['ok']){

			$update = array(
				'details' => json_encode($rsp)
			);

			instagram_backups_update($backup, $update);
		}
	}

	$map = instagram_backups_type_map("string keys");
	$enc_type = AddSlashes($map['photos']);

	$sql = "SELECT * FROM InstagramBackups WHERE type_id='{$enc_type}' AND disabled=0";
	backfill_db_main($sql, '_backup');

	exit();
?>
