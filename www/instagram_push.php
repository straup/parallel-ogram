<?php

	# http://instagram.com/developer/realtime/

	include("include/init.php");
	loadlib("instagram_push_subscriptions");

	if (! $GLOBALS['cfg']['enable_feature_push']){
		error_disabled();
	}

	$secret = get_str("secret");
	$subscription = instagram_push_subscriptions_get_by_secret_url($secret);

	if (! $subscription){
		error_404();
	}

	if (get_str("hub.mode") == "subscription"){

		$challenge = get_str("hub.challenge");
		$verify = get_str("hub.verify_token");

		if (! $challenge){
			error_404();
		}

		if (! $verify){
			error_403();
		}

		if ($verify != $subscription['verify_string']){
			error_403();
		}

		$update = array(
			'verified' => time(),
		);

		# error checking/handling?
		instagram_push_subscriptions_update($subscription, $update);

		echo $challege;
		exit();
	}

	# otherwise something is posting to us

	$update = array(
		'last_update' => time(),
	);

	# error checking/handling?
	instagram_push_subscriptions_update($subscription, $update);

?>
