<?php

	$root = dirname(dirname(__FILE__));
	ini_set("include_path", "{$root}/www:{$root}/www/include");

	set_time_limit(0);

	include("include/init.php");

	loadlib("cli");
	loadlib("backfill");
	loadlib("instagram_backups");

	$spec = array(
		"user" => array("flag" => "u", "required" => 1, "help" => "...",
		"directory" => array("flag" => "d", "required" => 1, "help" => "...",
	);

	$opts = cli_getopts($spec);

	# make static dir(s)

	# copy site images, css, js

	# copy instagram photos, faves


	exit();
?>
