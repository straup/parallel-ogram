<?php

	include("include/init.php");

	loadlib("http");
	loadlib("random");
	loadlib("instagram_api");
	loadlib("instagram_users");

	# Some basic sanity checking like are you already logged in?

	if ($GLOBALS['cfg']['user']['id']){
		header("location: {$GLOBALS['cfg']['abs_root_url']}");
		exit();
	}


	if (! $GLOBALS['cfg']['enable_feature_signin']){
		$GLOBALS['smarty']->display("page_signin_disabled.txt");
		exit();
	}

	$code = get_str("code");
	$redir = get_str("redir");

	if (! $code){
		error_404();
	}

	$rsp = instagram_api_get_auth_token($code, $redir);

	if (! $rsp['ok']){
		$GLOBALS['error']['oauth_access_token'] = 1;
		$GLOBALS['smarty']->display("page_auth_callback_instagram_oauth.txt");
		exit();
	}

	$oauth_token = $rsp['oauth_token'];

	$instagram_user = instagram_users_get_by_oauth_token($oauth_token);

	if (($instagram_user) && ($user_id = $instagram_user['user_id'])){
		$user = users_get_by_id($user_id);
	}

	# If we don't ensure that new users are allowed to create
	# an account (locally).

	else if (! $GLOBALS['cfg']['enable_feature_signup']){
		$GLOBALS['smarty']->display("page_signup_disabled.txt");
		exit();
	}

	# Hello, new user! This part will create entries in two separate
	# databases: Users and InstagramUsers that are joined by the primary
	# key on the Users table.

	else {

		$instagram_id = $rsp['user']['id'];
		$username = $rsp['user']['username'];

		# Check to see if this copy of parallel-ogram has created an
                # entry for this user (probably as part of the likes import).
		# If so then just update the oauth token. 

		if ($instagram_user = instagram_users_get_by_id($instagram_id)){

			$user = users_get_by_id($instagram_user['user_id']);

			$update = array(
				'oauth_token' => $oauth_token,
			);

			$rsp = instagram_users_update_user($instagram_user, $update);

			if (! $rsp['ok']){

				$GLOBALS['error']['dbupdate_instagramuser'] = 1;
				$GLOBALS['smarty']->display("page_auth_callback_instagram_oauth.txt");
				exit();
			}

		}

		# Otherwise this a brand new user

		else {

			$rsp = instagram_users_register_user($instagram_id, $username, $oauth_token);

			if (! $rsp['ok']){

				$GLOBALS['error'][ $rsp['error'] ] = 1;
				$GLOBALS['smarty']->display("page_auth_callback_instagram_oauth.txt");
				exit();
			}

			$user = $rsp['user'];
		}

	}

	# Okay, now finish logging the user in (setting cookies, etc.) and
	# redirecting them to some specific page if necessary.

	if ($redir){
		$redir = str_replace($GLOBALS['cfg']['abs_root_url'], "", $redir);
	}

	login_do_login($user, $redir);
	exit();
?>
