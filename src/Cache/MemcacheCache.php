<?php namespace Lusito\NotORM\Cache;

/** Cache using "NotORM." prefix in Memcache
 */
class MemcacheCache implements Cache {
	private $memcache;

	function __construct(\Memcache $memcache) {
		$this->memcache = $memcache;
	}

	function load($key) {
		$return = $this->memcache->get("NotORM.$key");
		return $return === false ? null : $return;
	}

	function save($key, $data) {
		$this->memcache->set("NotORM.$key", $data);
	}
}
