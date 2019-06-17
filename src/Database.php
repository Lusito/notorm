<?php namespace Lusito\NotORM;

/** Classic database representation, in case you prefer the old ways.
 * @property-write mixed $debug = false Enable debugging queries, true for error_log($query), callback($query, $parameters) otherwise
 * @property-write bool $freeze = false Disable persistence
 * @property-write string $rowClass = 'Row' Class used for created objects
 * @property-write bool $jsonAsArray = false Use array instead of object in Result JSON serialization
 * @property-write string $transaction Assign 'BEGIN', 'COMMIT' or 'ROLLBACK' to start or stop transaction
 */
class Database {
    private $cfg;

	/** Create database representation
	 * @param Config
	 */
	public function __construct(Config $cfg) {
        $this->cfg = $cfg;
	}

	/** Get table data
	 * @param string
	 * @param array (["condition"[, array("value")]]) passed to Result::where()
	 * @return Result
	 */
	public function __call($table, array $where) {
		$return = new Result($this->cfg->structure->getReferencingTable($table, ''), $this->cfg);
		if ($where) {
			call_user_func_array(array($return, 'where'), $where);
		}
		return $return;
	}

	/** Get table data to use as $db->table[1]
	 * @param string
	 * @return Result
	 */
	public function __get($table) {
		return new Result($this->cfg->structure->getReferencingTable($table, ''), $this->cfg, true);
	}

	/** Set write-only properties
	 * @return null
	 */
	public function __set($key, $value) {
        if(in_array($key, Config::WRITE_ONLY_PROPS))
			$this->cfg->$key = $value;
		else if ($key == "transaction") {
			switch (strtoupper($value)) {
				case "BEGIN": return $this->cfg->connection->beginTransaction();
				case "COMMIT": return $this->cfg->connection->commit();
				case "ROLLBACK": return $this->cfg->connection->rollback();
			}
		}
	}

	/** Get last insert ID
	 * @return string number
	 */
	public static function lastInsertId() {
		return self::$cfg->connection->lastInsertId();
	}
}
