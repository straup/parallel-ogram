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

	if (! $code){
		error_404();
	}

	$rsp = instagram_api_get_auth_token($code);

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
		$email = "{$instagram_id}@donotsend-instagram.com";

		$password = random_string(32);

		$user = users_create_user(array(
			"username" => $username,
			"email" => $email,
			"password" => $password,
		));

		if (! $user){
			$GLOBALS['error']['dberr_user'] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_instagram_oauth.txt");
			exit();
		}

		$instagram_user = instagram_users_create_user(array(
			'user_id' => $user['id'],
			'oauth_token' => $oauth_token,
			'instagram_id' => $instagram_id,
		));

		if (! $instagram_user){
			$GLOBALS['error']['dberr_instagramuser'] = 1;
			$GLOBALS['smarty']->display("page_auth_callback_instagram_oauth.txt");
			exit();
		}
	}

	# Okay, now finish logging the user in (setting cookies, etc.) and
	# redirecting them to some specific page if necessary.

	$redir = (isset($extra['redir'])) ? $extra['redir'] : '';

	login_do_login($user, $redir);
	exit();
?>
