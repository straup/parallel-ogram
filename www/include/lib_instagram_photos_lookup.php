<?php

	#################################################################

	function instagram_photos_lookup_get_by_photo_id($photo_id){

		$enc_photo = AddSlashes($photo_id);
		$sql = "SELECT * FROM InstagramPhotosLookup WHERE photo_id='{$enc_photo}'";

		return db_single(db_fetch($sql));
	}

	#################################################################

	function instagram_photos_lookup_add_photo($photo_id, $user_id){

		$insert = array(
			'photo_id' => AddSlashes($photo_id),
			'user_id' => AddSlashes($user_id),
		);

		$rsp = db_insert('InstagramPhotosLookup', $insert);
		return $rsp;
	}

	#################################################################

?>
