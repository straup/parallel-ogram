<?php

	include("../include/init.php");
	loadlib("god");

	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	$rsp = instagram_push_subscriptions_get_subscriptions($more);
	$GLOBALS['smarty']->assign_by_ref("subscriptions", $rsp['rows']);

	$topic_map = instagram_push_topic_map();
	$GLOBALS['smarty']->assign_by_ref("topic_map", $topic_map);

	$GLOBALS['smarty']->display("page_god_push_subscriptions.txt");
	exit();
?>
