<?php

	include("include/init.php");
	loadlib("instagram_backups");

	login_ensure_loggedin("/account/instagram/backups/");

	if (! $GLOBALS['cfg']['enable_feature_backups_registration']){

		if (! $GLOBALS['cfg']['user']['backup_photos']){
			error_disabled();
		}
	}

	$crumb_key = 'logout';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if (post_isset('done') && crumb_check($crumb_key)){

		if (post_int32("enable_backups")){

			$backups = instagram_backups_for_user($GLOBALS['cfg']['user']);

			if (count($backups['rows'])){
				$rsp = instagram_backups_reenable_user($GLOBALS['cfg']['user']);
			}

			else {
				$rsp = instagram_backups_register_user($GLOBALS['cfg']['user']);
			}
		}

		else if (post_int32("disable_backups")){
			$rsp = instagram_backups_disable_user($GLOBALS['cfg']['user']);
		}

		else {}

		if ($rsp){
			$GLOBALS['smarty']->assign("update", 1);
			$GLOBALS['smarty']->assign("success", $rsp['ok']);
		}
	}

	$backups = instagram_backups_for_user($GLOBALS['cfg']['user']);

	$GLOBALS['smarty']->assign_by_ref("owner", $GLOBALS['cfg']['user']);
	$GLOBALS['smarty']->assign_by_ref("backups", $backups['rows']);

	$GLOBALS['smarty']->display("page_account_instagram_backups.txt");
	exit();

?>
