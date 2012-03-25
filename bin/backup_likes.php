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

	function _backup($user, $more=array()){

		$likes_more = array(
			'per_page' => 1
		);

		$rsp = instagram_likes_for_user($user, $likes_more);

		$import_more = array();

		if (($rsp['ok']) && (count($rsp['rows']))){

			$like = $rsp['rows'][0];
			$import_more['max_like_id'] = $like['id'];
		}

		$rsp = instagram_likes_import_for_user($user, $import_more);
		dumper($rsp);
	}

	$sql = "SELECT * FROM users WHERE backup_photos=1";
	backfill_db_main($sql, '_backup');

	exit();
?>
