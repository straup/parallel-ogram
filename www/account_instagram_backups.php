<?php

	include("include/init.php");

	login_ensure_loggedin();

	$crumb_key = 'logout';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if (post_isset('done') && crumb_check($crumb_key)){

		$update = array();

		if (post_int32("enable_backups")){
			$update['backup_photos'] = 1;
		}

		else if (post_int32("disable_backups")){
			$update['backup_photos'] = 0;
		}

		else {}

		if (count($update)){

			$ok = users_update_user($GLOBALS['cfg']['user'], $update);

			if ($ok){
				$GLOBALS['cfg']['user'] = array_merge($GLOBALS['cfg']['user'], $update);
			}

			$GLOBALS['smarty']->assign("update", 1);
			$GLOBALS['smarty']->assign("success", $ok);
		}
	}

	$GLOBALS['smarty']->assign_by_ref("owner", $GLOBALS['cfg']['user']);

	$GLOBALS['smarty']->display("page_account_instagram_backups.txt");
	exit();

?>
