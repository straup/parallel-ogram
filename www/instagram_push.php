<?php

	# http://instagram.com/developer/realtime/

	include("include/init.php");
	loadlib("instagram_push_subscriptions");

	if (! $GLOBALS['cfg']['enable_feature_instagram_push']){
		error_disabled();
	}

	$secret = get_str("secret");
	$subscription = instagram_push_subscriptions_get_by_secret_url($secret);

	if (! $subscription){
		error_404();
	}

	# IMPORTANT: Note the '_' characters in the place of the '.'
	# separator described in the Instagram docs. This is a PHP-ism...

	if (get_str("hub_mode") == "subscribe"){

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

	# otherwise something is posting to us

	$raw = file_get_contents("php://input");
	$data = json_decode($raw, "as hash");

	$fh = fopen("/tmp/instapush", "w");
	fwrite($fh, $raw);
	fclose($fh);

	if (! $data){
		error_500();
	}

	$update = array(
		'last_update' => time(),
	);

	# error checking/handling?
	instagram_push_subscriptions_update($subscription, $update);

	exit();
?>
