<?php

	$GLOBALS['cfg']['instagram_push_endpoint'] = 'https://api.instagram.com/v1/subscriptions/';

	#################################################################

	function instagram_push_topic_map($string_keys=0){

		$map = array(
			0 => 'user',
			1 => 'tag',
			2 => 'geography',
			3 => 'location',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function instagram_push_subscribe(&$subscription){

		$type_map = instagram_push_type_map();
		$object = $type_map[$subscription['type_id']];

		$callback = "{$GLOBALS['cfg']['asb_root_url']}push/{$subscription['secret_url']}/";

		$params = array(
			'client_id' => $GLOBALS['cfg']['instagram_oauth_key'],
			'client_secret' => $GLOBALS['cfg']['instagram_oauth_secret'],
			'object' => $object,
			'aspect' => 'media',
			'verify_token' => $subscription['verify_token'],
			'callback_url' => $callback,
		);

		# note that $params takes precedence over $args

		if ($args = $subscription['topic_args']){
			$args = json_decode($args, 'as hash');
			$params = array_merge($args, $params);
		}

		$url = $GLOBALS['instagram_push_endpoint'];

		$ret = http_post($url, $params);
		return $ret;
	}

	#################################################################


	#################################################################

?>
