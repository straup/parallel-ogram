<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	include("include/init.php");

	loadlib("cli");
	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$spec = array(
		"topic" => array("flag" => "t", "required" => 1, "help" => "the name of the subscription topic"),
	);

	$opts = cli_getopts($spec);
	$topic = $opts['topic'];

	$map = instagram_push_topic_map("string keys");

	if (! isset($map[$topic])){
		echo "Invalid topic";
		exit();
	}

	$sub = array(
		'topic_id' => $map[$topic],
	);

	$rsp = instagram_push_subscriptions_create($sub);
	dumper($rsp);
	exit();
?>
