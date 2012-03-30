<?php

	include("include/init.php");
	loadlib("instagram_backups");

	$redir = "account/instagram/backups/";
	login_ensure_loggedin($redir);

	if (! $GLOBALS['cfg']['enable_feature_backups_registration']){

		if (! $GLOBALS['cfg']['user']['backup_photos']){
			error_disabled();
		}
	}

	#

	$backups = instagram_backups_for_user($GLOBALS['cfg']['user']);
	$registered = (count($backups['rows'])) ? 0 : 0;

	#

	if ($GLOBALS['cfg']['enable_feature_invite_codes']){

		loadlib("invite_codes");

		if ((! $registered) && (! invite_codes_get_by_cookie())){

			$cookie = login_get_cookie('invite');

			if (! $cookie){
				header("location: /invite/?redir=" . urlencode($redir));
				exit();
			}
		}
	}

	#

	$crumb_key = 'logout';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if (post_isset('done') && crumb_check($crumb_key)){

		if (post_int32("enable_backups")){

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
