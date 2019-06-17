<?php namespace Lusito\NotORM\Cache;

/** Cache using "NotORM." prefix in APC
 */
class ApcCache implements Cache {

	function load($key) {
		$return = apc_fetch("NotORM.$key", $success);
		return $success ? $return : null;
	}

	function save($key, $data) {
		apc_store("NotORM.$key", $data);
	}
}
