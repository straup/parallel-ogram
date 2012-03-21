<?php

	function instagram_photos_id_to_path($id){

		$parts = array();

		while (strlen($id)){

			$parts[] = substr($id, 0, 3);
			$id = substr($id, 3);
		}

		return implode("/", $parts);
	}

?>
