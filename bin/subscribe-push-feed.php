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
		echo "Invalid topic\n";
		exit();
	}

	$sub = array(
		'topic_id' => $map[$topic],
	);

	$rsp = instagram_push_subscriptions_create($sub);

	if (! $rsp['ok']){
		echo "{$rsp['error']}\n";
		exit();
	}

	$sub = $rsp['subscription'];
	dumper($rsp);

	$rsp = instagram_push_subscribe($sub);
	dumper($rsp);

	if (! $rsp['ok']){
#		instagram_push_subscriptions_delete($sub);
	}

	exit();
?>
