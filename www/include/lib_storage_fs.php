<?php

	#################################################################

	function storage_id_to_path($id){
		return storage_fs_id_to_path($id);
	}

	function storage_write_file($path, $bytes){
		return storage_fs_write_file($path, $bytes);
	}

	#################################################################

	function storage_fs_id_to_path($id){

		$parts = array();

		while (strlen($id)){

			$parts[] = substr($id, 0, 3);
			$id = substr($id, 3);
		}

		return implode("/", $parts);
	}

	#################################################################

	function storage_fs_write_file($path, $bytes){

		$root_path = dirname($path);

		# TO DO: check for umask config...

		if (! file_exists($root_path)){
			mkdir($root_path, 0755, true);
		}

		$fh = fopen($path, "wb");

		if (! $fh){
			return not_okay("failed to open '{$path}' for writing");
		}

		fwrite($fh, $bytes);
		fclose($fh);

		return okay();
	}

	#################################################################
?>
