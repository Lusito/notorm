<?php namespace Lusito\NotORM;

/** Single row representation
 */
class Row extends Friendly implements \IteratorAggregate, \ArrayAccess, \Countable, \JsonSerializable {
	private $modified = [];
	protected $row, $result, $cfg, $primary;

	/** @access protected must be public because it is called from Result */
	function __construct(array $row, Result $result, Config $cfg) {
		$this->row = $row;
		$this->result = $result;
		$this->cfg = $cfg;
		if (array_key_exists($result->primary, $row)) {
			$this->primary = $row[$result->primary];
		}
	}

	/** Get primary key value
	 * @return string
	 */
	function __toString() {
		return (string) $this[$this->result->primary]; // (string) - PostgreSQL returns int
	}

	/** Get referenced row
	 * @param string
	 * @return Row or null if the row does not exist
	 */
	function __get($name) {
		$column = $this->cfg->structure->getReferencedColumn($name, $this->result->table);
		$referenced = &$this->result->referenced[$name];
		if (!isset($referenced)) {
			$keys = [];
			foreach ($this->result->rows as $row) {
				if ($row[$column] !== null) {
					$keys[$row[$column]] = null;
				}
			}
			if ($keys) {
				$table = $this->cfg->structure->getReferencedTable($name, $this->result->table);
				$referenced = new Result($table, $this->cfg);
				$referenced->where("$table." . $this->cfg->structure->getPrimary($table), array_keys($keys));
			} else {
				$referenced = [];
			}
		}

		// referenced row may not exist
		return $referenced[$this[$column]] ?? null;
	}

	/** Test if referenced row exists
	 * @param string
	 * @return bool
	 */
	function __isset($name) {
		return ($this->__get($name) !== null);
	}

	/** Store referenced value
	 * @param string
	 * @param Row or null
	 * @return null
	 */
	function __set($name, Row $value = null) {
		$column = $this->cfg->structure->getReferencedColumn($name, $this->result->table);
		$this[$column] = $value;
	}

	/** Remove referenced column from data
	 * @param string
	 * @return null
	 */
	function __unset($name) {
		$column = $this->cfg->structure->getReferencedColumn($name, $this->result->table);
		unset($this[$column]);
	}

	/** Get referencing rows
	 * @param string table name
	 * @param array (["condition"[, ["value"]]])
	 * @return MultiResult
	 */
	function __call($name, array $args) {
		$table = $this->cfg->structure->getReferencingTable($name, $this->result->table);
		$column = $this->cfg->structure->getReferencingColumn($table, $this->result->table);
		$return = new MultiResult($table, $this->cfg, $this->result, $column, $this[$this->result->primary]);
		$return->where("$table.$column", array_keys((array) $this->result->rows)); // (array) - is null after insert
		if ($args) {
			call_user_func_array([$return, 'where'], $args);
		}
		return $return;
	}

	/** Update row
	 * @param array or null for all modified values
	 * @return int number of affected rows or false in case of an error
	 */
	function update($data = null) {
		// update is an SQL keyword
		if (!isset($data)) {
			$data = $this->modified;
		}
		$result = new Result($this->result->table, $this->cfg);
		$return = $result->where($this->result->primary, $this->primary)->update($data);
		$this->primary = $this[$this->result->primary];
		return $return;
	}

	/** Delete row
	 * @return int number of affected rows or false in case of an error
	 */
	function delete() {
		// delete is an SQL keyword
		$result = new Result($this->result->table, $this->cfg);
		$return = $result->where($this->result->primary, $this->primary)->delete();
		$this->primary = $this[$this->result->primary];
		return $return;
	}

	protected function access($key, $delete = false) {
		if ($this->cfg->cache && !isset($this->modified[$key]) && $this->result->access($key, $delete)) {
			$id = (isset($this->primary) ? $this->primary : $this->row);
			$this->row = $this->result[$id]->row;
		}
	}

	// IteratorAggregate implementation

	function getIterator() {
		$this->access(null);
		return new \ArrayIterator($this->row);
	}

	// Countable implementation

	function count() {
		return count($this->row);
	}

	// ArrayAccess implementation

	/** Test if column exists
	 * @param string column name
	 * @return bool
	 */
	function offsetExists($key) {
		$this->access($key);
		$return = array_key_exists($key, $this->row);
		if (!$return) {
			$this->access($key, true);
		}
		return $return;
	}

	/** Get value of column
	 * @param string column name
	 * @return string
	 */
	function offsetGet($key) {
		$this->access($key);
		if (!array_key_exists($key, $this->row)) {
			$this->access($key, true);
		}
		return $this->row[$key];
	}

	/** Store value in column
	 * @param string column name
	 * @return null
	 */
	function offsetSet($key, $value) {
		$this->row[$key] = $value;
		$this->modified[$key] = $value;
	}

	/** Remove column from data
	 * @param string column name
	 * @return null
	 */
	function offsetUnset($key) {
		unset($this->row[$key]);
		unset($this->modified[$key]);
	}

	// JsonSerializable implementation

	function jsonSerialize() {
		return $this->row;
	}
}
