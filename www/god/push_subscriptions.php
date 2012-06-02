<?php

	include("../include/init.php");
	loadlib("god");

	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$topic_map = instagram_push_topic_map();
	$GLOBALS['smarty']->assign_by_ref("topic_map", $topic_map);

	$crumb_key = "create_feed";
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if ((post_str("create") && (crumb_check($crumb_key)))){

		$topic_id = post_str("topic_id");

		if (isset($topic_map[$topic_id])){

			$topic = $topic_map[$topic_id];

			$sub = array(
				'topic_id' => $topic_id
			);

			$rsp = instagram_push_subscriptions_create($sub);
			$GLOBALS['smarty']->assign("create_sub", $rsp);

			if ($rsp['ok']){

				$sub = $rsp['subscription'];

				$rsp = instagram_push_subscribe($sub);
				$GLOBALS['smarty']->assign("create_feed", $rsp);

				if ($rsp['ok']){

					$sub_id = $rsp['details']['data']['id'];

					$update = array(
						'instagram_subscription_id' => $sub_id
					);

					instagram_push_subscriptions_update($sub, $update);
				}

				else {
					instagram_push_subscriptions_delete($sub);
				}
			}
		}
	}

	$more = array();

	if ($page = get_int32("page")){
		$more['page'] = $page;
	}

	$rsp = instagram_push_subscriptions_get_subscriptions($more);
	$GLOBALS['smarty']->assign_by_ref("subscriptions", $rsp['rows']);

	$GLOBALS['smarty']->display("page_god_push_subscriptions.txt");
	exit();
?>
