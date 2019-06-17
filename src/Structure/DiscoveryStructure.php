<?php namespace Lusito\NotORM\Structure;

use Lusito\NotORM\Cache\Cache;

/** Structure reading meta-informations from the database
 */
class DiscoveryStructure implements Structure {
	protected $connection = null, $cache = null, $structure = array();
	protected $foreign;

	/** Create autodisovery structure
	 * @param string use "%s_id" to access $name . "_id" column in $row->$name
	 */
	function __construct($foreign = '%s') {
		$this->foreign = $foreign;
	}

	function setConnection(\PDO $connection) {
		$this->connection = $connection;
	}

	function setCache(Cache $cache) {
		$this->cache = $cache;
		if ($cache) {
			$this->structure = $cache->load("structure");
		}
	}

	/** Save data to cache
	 */
	function __destruct() {
		if ($this->cache) {
			$this->cache->save("structure", $this->structure);
		}
	}

	function getPrimary($table) {
		$return = &$this->structure["primary"][$table];
		if (!isset($return)) {
			$return = "";
			foreach ($this->connection->query("EXPLAIN $table") as $column) {
				if ($column[3] == "PRI") { // 3 - "Key" is not compatible with \PDO::CASE_LOWER
					if ($return != "") {
						$return = ""; // multi-column primary key is not supported
						break;
					}
					$return = $column[0];
				}
			}
		}
		return $return;
	}

	function getReferencingColumn($name, $table) {
		$name = strtolower($name);
		$return = &$this->structure["referencing"][$table];
		if (!isset($return[$name])) {
			foreach ($this->connection->query("
				SELECT TABLE_NAME, COLUMN_NAME
				FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA = DATABASE()
				AND REFERENCED_TABLE_SCHEMA = DATABASE()
				AND REFERENCED_TABLE_NAME = " . $this->connection->quote($table) . "
				AND REFERENCED_COLUMN_NAME = " . $this->connection->quote($this->getPrimary($table)) //! may not reference primary key
			) as $row) {
				$return[strtolower($row[0])] = $row[1];
			}
		}
		return $return[$name];
	}

	function getReferencingTable($name, $table) {
		return $name;
	}

	function getReferencedColumn($name, $table) {
		return sprintf($this->foreign, $name);
	}

	function getReferencedTable($name, $table) {
		$column = strtolower($this->getReferencedColumn($name, $table));
		$return = &$this->structure["referenced"][$table];
		if (!isset($return[$column])) {
			foreach ($this->connection->query("
				SELECT COLUMN_NAME, REFERENCED_TABLE_NAME
				FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA = DATABASE()
				AND REFERENCED_TABLE_SCHEMA = DATABASE()
				AND TABLE_NAME = " . $this->connection->quote($table) . "
			") as $row) {
				$return[strtolower($row[0])] = $row[1];
			}
		}
		return $return[$column];
	}

	function getSequence($table) {
		return null;
	}
}
