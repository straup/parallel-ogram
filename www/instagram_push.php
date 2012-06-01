<?php

	# http://instagram.com/developer/realtime/

	include("include/init.php");
	loadlib("instagram_push_subscriptions");
	loadlib("instagram_push");
	loadlib("instagram_photos_import");

	if (! $GLOBALS['cfg']['enable_feature_instagram_push']){
		error_disabled();
	}

	$secret = get_str("secret");
	$subscription = instagram_push_subscriptions_get_by_secret_url($secret);

	if (! $subscription){
		error_404();
	}

	# IMPORTANT: Note the '_' characters in the place of the '.'
	# separator for 'hub.*' parameters, as described in the Instagram
	# docs. This is a PHP-ism...

	if (get_str("hub_mode") == "subscribe"){

		# possibly too restrictive?

		if ($subscription['verified']){
			error_403();
		}

		$challenge = get_str("hub_challenge");
		$verify = get_str("hub_verify_token");

		if (! $challenge){
			error_404();
		}

		if (! $verify){
			error_403();
		}

		if ($verify != $subscription['verify_token']){
			error_403();
		}

		$update = array(
			'verified' => time(),
		);

		# error checking/handling?
		instagram_push_subscriptions_update($subscription, $update);

		echo $challenge;
		exit();
	}

	# okay, now assume something is posting to us

	$headers = getallheaders();
	$sig = $headers['X-Hub-Signature'];

	if (! $sig){
		error_404();
	}

	$raw = file_get_contents("php://input");

	if (! $raw){
		error_404();
	}

	if (! instagram_push_validate_payload($raw, $sig)){
		error_403();
	}

	$data = json_decode($raw, "as hash");

	# $fh = fopen("/tmp/instapush", "w");
	# fwrite($fh, $raw);
	# fclose($fh);

	if (! $data){
		error_500();
	}

	$users = array();

	foreach ($data as $row){

		$topic = $row['object'];

		if ($topic == 'user'){

			$ts = $row['time'];
			$user_id = $row['object_id'];

			$ts = (isset($users[$user_id])) ? min($ts, $users[$user_id]) : $ts;
			$users[$user_id] = $ts;
		}
	}

	foreach ($users as $user_id => $ts){

		$insta_user = instagram_users_get_by_id($user_id);
		$user = users_get_by_id($insta_user['user_id']);

		# There appears to be a need to use the past since
		# just passing $ts sometimes yields no results...

		$more = array(
			'min_timestamp' => $ts - 600,
		);

		# See this: it assumes you've made the directory defined
		# in the  $GLOBALS['cfg']['instagram_static_path'] config
		# variable writeable by the web server. All the usual
		# caveats apply.

		$rsp = instagram_photos_import_for_user($user, $more);
		$rsp['args'] = $more;

		$users[$user_id] = $rsp;
	}

	$update = array(
		'last_update' => time(),
		'last_update_details' => json_encode($users),
	);

	# error checking/handling?
	instagram_push_subscriptions_update($subscription, $update);

	exit();
?>
