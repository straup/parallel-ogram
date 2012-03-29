<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	include("include/init.php");

	loadlib("backfill");
	loadlib("instagram_backups");

	function _backup($user, $more=array()){

		$rsp = instagram_backups_register_user($user);

		dumper($rsp);

		$rsp = instagram_backups_for_user($user);

		foreach ($rsp['rows'] as $backup){

			$update = array(
				'details' => $user['backup_last_update'],
			);

			$_rsp = instagram_backups_update($backup, $update);
			dumper($_rsp);
		}
	}

	$sql = "SELECT * FROM users WHERE backup_photos=1";
	backfill_db_main($sql, '_backup');

	exit();
?>
