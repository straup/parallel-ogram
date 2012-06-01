<?php

	$GLOBALS['instagram_push_endpoint'] = 'https://api.instagram.com/v1/subscriptions/';

	#################################################################

	function instagram_push_topic_map($string_keys=0){

		$map = array(
			0 => 'user',
			# 1 => 'tag',
			# 2 => 'geography',
			# 3 => 'location',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function instagram_push_is_valid_topic($topic){

		$map = instagram_push_topic_map("string keys");
		return (isset($map[$topic])) ? 1 : 0;
	}

	#################################################################

	function instagram_push_subscribe(&$subscription){

		$topic_map = instagram_push_topic_map();
		$object = $topic_map[$subscription['topic_id']];

		# this does not work when run from the command line...
		$callback = "{$GLOBALS['cfg']['abs_root_url']}push/{$subscription['secret_url']}/";

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

		$rsp = http_post($url, $params);

		if ((! $rsp['ok']) && (! $rsp['body'])){
			return $rsp;
		}

		$data = json_decode($rsp['body'], 'as hash');

		if (! $data){
			return not_okay("failed to parse json");
		}

		return array(
			'ok' => $rsp['ok'],
			'details' => $data,
		);
	}

	#################################################################

	function instagram_push_unsubscribe(&$subscription){
		# please write me
	}

	#################################################################

	# To verify that the payload you received comes from us, you can verify the
	# "X-Hub-Signature" header. This will be a SHA-1-signed hexadecimal
	# digest, using your client secret as a key and the payload as the
	# message.
	
	function instagram_push_validate_payload($data, $sig){

		# note: we're just not that concerned about doing
		# an == test, it's okay...

		$test = hash_hmac("sha1", $data, $GLOBALS['cfg']['instagram_oauth_secret']);
		return ($test == $sig) ? 1 : 0;
	}

	#################################################################	

?>
