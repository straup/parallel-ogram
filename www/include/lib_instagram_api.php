<?php

	loadlib("http");

	#################################################################

	# http://instagr.am/developer/auth/
	# http://instagr.am/developer/endpoints/

	$GLOBALS['instagram_api_endpoint'] = 'https://api.instagram.com/v1/';
	$GLOBALS['instagram_oauth_endpoint'] = 'https://api.instagram.com/oauth/';

	#################################################################

	function instagram_api_call($method, $args=array(), $more=array()){

		$method = ltrim($method, "/");
		$args['v'] = gmdate("Ymd", time());

		if ($more['method'] == 'POST'){

			$url = $GLOBALS['instagram_api_endpoint'] . $method;
			$rsp = http_post($url, $args);
		}

		else{
			$query = http_build_query($args);
			$url = $GLOBALS['instagram_api_endpoint'] . $method . "?{$query}";
			$rsp = http_get($url);
		}

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], "as hash");

		if (! $data){
			return not_okay("failed to parse response");
		}

		return okay(array(
			"rsp" => $data,
	 	));
	}

	#################################################################

	function instagram_api_get_auth_url($redir=null){

		$callback = $GLOBALS['cfg']['abs_root_url'] . $GLOBALS['cfg']['instagram_oauth_callback'];

		if ($redir){
	 		$callback .= "?redir=" . urlencode($redir);
		}

		$oauth_key = $GLOBALS['cfg']['instagram_oauth_key'];
        	$oauth_redir = urlencode($callback);

		$url = "{$GLOBALS['instagram_oauth_endpoint']}authorize?client_id={$oauth_key}&response_type=code&redirect_uri=$oauth_redir";

		return $url;
	}

	#################################################################

	function instagram_api_get_auth_token($code, $redir=null){

		$callback = $GLOBALS['cfg']['abs_root_url'] . $GLOBALS['cfg']['instagram_oauth_callback'];

		if ($redir){
			$callback .= "?redir=" . urlencode($redir);
		}

		$args = array(
			'client_id' => $GLOBALS['cfg']['instagram_oauth_key'],
			'client_secret' => $GLOBALS['cfg']['instagram_oauth_secret'],
			'grant_type' => 'authorization_code',
			'redirect_uri' => $callback,
			'code' => $code,
		);

		# Of course the 4sq kids only require that you GET this...
		# Go, standards! (20120227/straup)

		$url = "{$GLOBALS['instagram_oauth_endpoint']}access_token";
		$rsp = http_post($url, $args);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], 'as hash');

		if ((! $data) || (! $data['access_token'])){
			return not_okay("failed to parse response");
		}

		return okay(array(
			'oauth_token' => $data['access_token'],
			'user' => $data['user'],
		));
	}

	#################################################################

?>
