<?php namespace Lusito\NotORM\Cache;

/** Cache using $_SESSION["NotORM"]
 */
class SessionCache implements Cache {

	function load($key) {
		return $_SESSION["NotORM"][$key] ?? null;
	}

	function save($key, $data) {
		$_SESSION["NotORM"][$key] = $data;
	}
}
