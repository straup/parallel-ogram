<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	include("include/init.php");

	loadlib("cli");
	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$spec = array(
		"id" => array("flag" => "i", "required" => 0, "help" => "the ID of the subscription"),
		"secret" => array("flag" => "s", "required" => 0, "help" => "the secret url of the subscription"),
	);

	$opts = cli_getopts($spec);

	if ($opts['id']){
		$sub = instagram_push_subscriptions_get_by_id($opts['id']);
	}

	else if ($opts['secret']){
		$sub = instagram_push_subscriptions_get_by_secret_url($opts['secret']);
	}

	else {}

	if (! $sub){
		echo "Missing or invalid subscription ID\n";
		exit();
	}

	# TO DO: prompt

	$rsp = instagram_push_subscriptions_delete($sub);

	dumper($rsp);
	exit();
?>
