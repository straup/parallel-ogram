<?php

	include("../include/init.php");
	loadlib("god");

	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$id = get_int32("id");
	$sub = instagram_push_subscriptions_get_by_id($id);

	if (! $sub){
		error_404();
	}

	$topic_map = instagram_push_topic_map();
	$sub['str_topic'] = $topic_map[$sub['topic_id']];

	if ($sub['last_update_details']){
		$sub['last_update_details'] = json_decode($sub['last_update_details'], "as hash");
	}

	$GLOBALS['smarty']->assign_by_ref("subscription", $sub);

	$GLOBALS['smarty']->display("page_god_push_subscription.txt");
	exit();
?>
