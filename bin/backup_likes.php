<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	include("include/init.php");

	loadlib("backfill");
	loadlib("instagram_backups");
	loadlib("instagram_likes_import");

	function _backup($backup, $more=array()){

		$user = users_get_by_id($backup['user_id']);

		$likes_more = array(
			'per_page' => 1
		);

		if ($last_update = json_decode($backup['details'], 'as hash')){

			$rsp = instagram_likes_for_user($user, $likes_more);

			$import_more = array();

			if (($rsp['ok']) && (count($rsp['rows']))){

				$like = $rsp['rows'][0];
				$import_more['max_like_id'] = $like['id'];
			}
		}

		$rsp = instagram_likes_import_for_user($user, $import_more);
		dumper($rsp);

		if ($rsp['ok']){

			$update = array(
				'details' => json_encode($rsp)
			);

			instagram_backups_update($backup, $update);
		}

	}

	$map = instagram_backups_type_map("string keys");
	$enc_type = AddSlashes($map['likes']);

	$sql = "SELECT * FROM InstagramBackups WHERE type_id='{$enc_type}' AND disabled=0";
	backfill_db_main($sql, '_backup');

	exit();
?>
