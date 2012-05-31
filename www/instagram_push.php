<?php

	include("include/init.php");

	# http://instagram.com/developer/realtime/

	if (! $GLOBALS['cfg']['enable_feature_push']){
		error_disabled();
	}

	$sub = get_str("subscription");

	# ensure subscription (url) here

	if (get_str("hub.mode") == "subscription"){

		$challenge = get_str("hub.challenge");
		$verify = get_str("hub.verify_token");

		if (! $challenge){
			error_404();
		}

		if (! $verify){
			error_403();
		}

		# ensure matching verify string for subscription

		echo $challege;
		exit();
	}

	# otherwise something is posting to us

?>
