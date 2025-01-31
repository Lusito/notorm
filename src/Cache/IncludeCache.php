<?php namespace Lusito\NotORM\Cache;

/** Cache using PHP include
 */
class IncludeCache implements Cache {
	private $filename, $data = [];

	function __construct($filename) {
		$this->filename = $filename;
		$this->data = @include realpath($filename); // @ - file may not exist, realpath() to not include from include_path //! silently falls with syntax error and fails with unreadable file
		if (!is_array($this->data)) { // empty file returns 1
			$this->data = [];
		}
	}

	function load($key) {
		return $this->data[$key] ?? null;
	}

	function save($key, $data) {
		if (!isset($this->data[$key]) || $this->data[$key] !== $data) {
			$this->data[$key] = $data;
			file_put_contents($this->filename, '<?php return ' . var_export($this->data, true) . ';', LOCK_EX);
		}
	}
}
