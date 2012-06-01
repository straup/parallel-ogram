<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	include("include/init.php");

	loadlib("cli");
	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$map = instagram_push_topic_map("string keys");
	$valid_topics = implode(", ", array_keys($map));

	$spec = array(
		"topic" => array("flag" => "t", "required" => 1, "help" => "the name of the subscription topic, valid topics are: {$valid_topics}"),
		"url" => array("flag" => "u", "required" => 1, "help" => "the *root* URL of your copy of parallel-ogram (the need to specify this here is not a feature...)")
	);

	$opts = cli_getopts($spec);
	$topic = $opts['topic'];

	# This sucks to have to do but I am uncertain what the
	# better alternative is right now... (20120601/straup)

	$root = rtrim($opts['url'], '/') . "/";	
	$GLOBALS['cfg']['abs_root_url'] = $root;

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
		instagram_push_subscriptions_delete($sub);
	}

	exit();
?>
