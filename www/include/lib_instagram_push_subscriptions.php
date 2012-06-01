<?php

	loadlib("random");

	#################################################################

	function instagram_push_subscriptions_get_by_id($id){

		$enc_id = AddSlashes($id);
		$sql = "SELECT * FROM InstagramPushSubscriptions WHERE id='{$enc_id}'";
		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#################################################################

	function instagram_push_subscriptions_get_by_secret_url($url){

		$enc_url = AddSlashes($url);
		$sql = "SELECT * FROM InstagramPushSubscriptions WHERE secret_url='{$enc_url}'";
		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#################################################################

	function instagram_push_subscriptions_create($sub){

		$sub['id'] = db_tickets_create();
		$sub['created'] = time();

		$sub['verify_string'] = instagram_push_subscriptions_genereate_verify_string();
		$sub['secret_url'] = instagram_push_subscriptions_genereate_secret_url();

		if (is_array($sub['topic_args'])){
			ksort($sub['topic_args']);
			$sub['topic_args'] = json_encode($sub['topic_args']);
		}

		$insert = array();

		foreach ($sub as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('InstragramPushSubscriptions', $insert);

		if ($rsp['ok']){
			$rsp['subscription'] = $sub;
		}

		return $rsp;
	}

	#################################################################

	function instagram_push_subscriptions_update(&$subscription, $update){

		$insert = array();

		foreach ($update as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($subscription['id']);
		$where = "id='{$enc_id}'";

		return db_update('InstagramPushSubscriptions', $insert, $where);
	}

	#################################################################

	function instagram_push_subscriptions_delete(&$subscription){

		$enc_id = AddSlashes($subscription['id']);
		$sql = "DELETE FROM InstagramPushSubscriptions WHERE id='{$enc_id}'";

		return db_write($sql);
	}

	#################################################################

	function instagram_push_subscriptions_generate_verify_string(){
		return random_string(32);
	}

	#################################################################

	function instagram_push_subscriptions_generate_secret_url(){

		$secret = null;

		while (! $secret){

			$secret = random_string(64);

			if (instagram_push_subscriptions_get_by_secret_url($secret)){
				$secret = null;
			}
		}

		return $secret;
	}

	#################################################################

?>
