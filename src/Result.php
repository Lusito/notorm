<?php namespace Lusito\NotORM;

/** Filtered table representation
 * @method Result and(mixed $condition, mixed $parameters = []) Add AND condition
 * @method Result or(mixed $condition, mixed $parameters = []) Add OR condition
 */
class Result extends Friendly implements \Iterator, \ArrayAccess, \Countable, \JsonSerializable {
    protected $cfg;
	protected $single;
	protected $select = [], $conditions = [], $where = [], $parameters = [], $order = [], $limit = null, $offset = null, $group = "", $having = "", $lock = null;
	protected $union = [], $unionOrder = [], $unionLimit = null, $unionOffset = null;
	protected $data, $referencing = [], $aggregation = [], $accessed, $access, $keys = [];

	/** Create table result
	 * @param string
	 * @param Config
	 * @param bool single row
	 */
	function __construct($table, Config $cfg, $single = false) {
		$this->table = $table;
		$this->cfg = $cfg;
		$this->single = $single;
		$this->primary = $cfg->structure->getPrimary($table);
	}

	/** Save data to cache and empty result
	 */
	function __destruct() {
		if ($this->cfg->cache && !$this->select && isset($this->rows)) {
			$access = $this->access;
			if (is_array($access)) {
				$access = array_filter($access);
			}
			$this->cfg->cache->save("$this->table;" . implode(",", $this->conditions), $access);
		}
		$this->rows = null;
		unset($this->data);
	}

	protected function limitString($limit, $offset = null) {
		$return = "";
		if (isset($limit) && $this->cfg->driver != "oci" && $this->cfg->driver != "dblib" && $this->cfg->driver != "mssql" && $this->cfg->driver != "sqlsrv") {
			$return .= " LIMIT $limit";
			if ($offset) {
				$return .= " OFFSET $offset";
			}
		}
		return $return;
	}

	protected function removeExtraDots($expression) {
		return preg_replace('~(?:\\b[a-z_][a-z0-9_.:]*[.:])?([a-z_][a-z0-9_]*)[.:]([a-z_*])~i', '\\1.\\2', $expression); // rewrite tab1.tab2.col
	}

	protected function whereString() {
		$return = "";
		if ($this->group) {
			$return .= " GROUP BY $this->group";
		}
		if ($this->having) {
			$return .= " HAVING $this->having";
		}
		if ($this->order) {
			$return .= " ORDER BY " . implode(", ", $this->order);
		}
		$return = $this->removeExtraDots($return);

		$where = $this->where;
		if (isset($this->limit) && $this->cfg->driver == "oci") {
			$where[] = ($where ? " AND " : "") . "(" . ($this->offset ? "rownum > $this->offset AND " : "") . "rownum <= " . ($this->limit + $this->offset) . ")"; //! rownum > doesn't work - requires subselect (see adminer/drivers/oracle.inc.php)
		}
		if ($where) {
			$return = " WHERE " . implode($where) . $return;
		}

		$return .= $this->limitString($this->limit, $this->offset);
		if (isset($this->lock)) {
			$return .= ($this->lock ? " FOR UPDATE" : " LOCK IN SHARE MODE");
		}
		return $return;
	}

	protected function topString($limit, $offset = null) {
		if (isset($limit) && ($this->cfg->driver == "dblib" || $this->cfg->driver == "mssql" || $this->cfg->driver == "sqlsrv")) {
			return " TOP ($this->limit)"; //! offset is not supported
		}
		return "";
	}

	protected function createJoins($val) {
		$return = [];
		preg_match_all('~\\b([a-z_][a-z0-9_.:]*[.:])[a-z_*]~i', $val, $matches);
		foreach ($matches[1] as $names) {
			$parent = $this->table;
			if ($names != "$parent.") { // case-sensitive
				preg_match_all('~\\b([a-z_][a-z0-9_]*)([.:])~i', $names, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					list(, $name, $delimiter) = $match;
					$table = $this->cfg->structure->getReferencedTable($name, $parent);
					$column = ($delimiter == ':' ? $this->cfg->structure->getPrimary($parent) : $this->cfg->structure->getReferencedColumn($name, $parent));
					$primary = ($delimiter == ':' ? $this->cfg->structure->getReferencedColumn($parent, $table) : $this->cfg->structure->getPrimary($table));
					$return[$name] = " LEFT JOIN $table" . ($table != $name ? " AS $name" : "") . " ON $parent.$column = $name.$primary"; // should use alias if the table is used on more places
					$parent = $name;
				}
			}
		}
		return $return;
	}

	/** Get SQL query
	 * @return string
	 */
	function __toString() {
		$return = "SELECT" . $this->topString($this->limit, $this->offset) . " ";
		$join = $this->createJoins(implode(",", $this->conditions) . "," . implode(",", $this->select) . ",$this->group,$this->having," . implode(",", $this->order));
		if (!isset($this->rows) && $this->cfg->cache && !is_string($this->accessed)) {
			$this->accessed = $this->cfg->cache->load("$this->table;" . implode(",", $this->conditions));
			$this->access = $this->accessed;
		}
		if ($this->select) {
			$return .= $this->removeExtraDots(implode(", ", $this->select));
		} elseif ($this->accessed) {
			$return .= ($join ? "$this->table." : "") . implode(", " . ($join ? "$this->table." : ""), array_keys($this->accessed));
		} else {
			$return .= ($join ? "$this->table." : "") . "*";
		}
		$return .= " FROM $this->table" . implode($join) . $this->whereString();
		if ($this->union) {
			$return = ($this->cfg->driver == "sqlite" || $this->cfg->driver == "oci" ? $return : "($return)") . implode($this->union);
			if ($this->unionOrder) {
				$return .= " ORDER BY " . implode(", ", $this->unionOrder);
			}
			$return .= $this->limitString($this->unionLimit, $this->unionOffset);
			$top = $this->topString($this->unionLimit, $this->unionOffset);
			if ($top) {
				$return = "SELECT$top * FROM ($return) t";
			}
		}
		return $return;
	}

	protected function query($query, $parameters) {
		if ($this->cfg->debug) {
			if (!is_callable($this->cfg->debug)) {
				$debug = "$query;";
				if ($parameters) {
					$debug .= " -- " . implode(", ", array_map([$this, 'quote'], $parameters));
				}
				$pattern = '(^' . preg_quote(dirname(__FILE__)) . '(\\.php$|[/\\\\]))'; // can be static
				foreach (debug_backtrace() as $backtrace) {
					if (isset($backtrace["file"]) && !preg_match($pattern, $backtrace["file"])) { // stop on first file outside NotORM source codes
						break;
					}
				}
				print("$backtrace[file]:$backtrace[line]:$debug\n");
			} elseif (call_user_func($this->cfg->debug, $query, $parameters) === false) {
				return false;
			}
		}
		$return = $this->cfg->connection->prepare($query);
		if (!$return || !$return->execute(array_map([$this, 'formatValue'], $parameters))) {
			$return = false;
		}
		if ($this->cfg->debugTimer) {
			call_user_func($this->cfg->debugTimer);
		}
		return $return;
	}

	protected function formatValue($val) {
		if ($val instanceof \DateTime) {
			return $val->format("Y-m-d H:i:s"); //! may be driver specific
		}
		return $val;
	}

	protected function quote($val) {
		if (!isset($val)) {
			return "NULL";
		}
		if (is_array($val)) { // (a, b) IN ((1, 2), (3, 4))
			return "(" . implode(", ", array_map([$this, 'quote'], $val)) . ")";
		}
		$val = $this->formatValue($val);
		if (is_float($val)) {
			return sprintf("%F", $val); // otherwise depends on setlocale()
		}
		if ($val === false) {
			return "0";
		}
		if (is_int($val) || $val instanceof Literal) { // number or SQL code - for example "NOW()"
			return (string) $val;
		}
		return $this->cfg->connection->quote($val);
	}

	/** Shortcut for call_user_func_array([$this, 'insert'], $rows)
	 * @param array
	 * @return int number of affected rows or false in case of an error
	 */
	function insert_multi(array $rows) {
		if ($this->cfg->freeze) {
			return false;
		}
		if (!$rows) {
			return 0;
		}
		$data = reset($rows);
		$parameters = [];
		if ($data instanceof Result) {
			$parameters = $data->parameters; //! other parameters
			$data = (string) $data;
		} elseif ($data instanceof \Traversable) {
			$data = iterator_to_array($data);
		}
		$insert = $data;
		if (is_array($data)) {
			$values = [];
			foreach ($rows as $value) {
				if ($value instanceof \Traversable) {
					$value = iterator_to_array($value);
				}
				$values[] = $this->quote($value);
				foreach ($value as $val) {
					if ($val instanceof Literal && $val->parameters) {
						$parameters = array_merge($parameters, $val->parameters);
					}
				}
			}
			//! driver specific extended insert
			$insert = ($data || $this->cfg->driver == "mysql"
					? "(" . implode(", ", array_keys($data)) . ") VALUES " . implode(", ", $values)
					: "DEFAULT VALUES"
			);
		}
		// requires empty $this->parameters
		$return = $this->query("INSERT INTO $this->table $insert", $parameters);
		if (!$return) {
			return false;
		}
		$this->rows = null;
		return $return->rowCount();
	}

	/** Insert row in a table
	 * @param mixed [$column => $value]|Traversable for single row insert or Result|string for INSERT ... SELECT
	 * @param ... used for extended insert
	 * @return mixed inserted Row or false in case of an error or number of affected rows for INSERT ... SELECT
	 */
	function insert($data) {
		$rows = func_get_args();
		$return = $this->insert_multi($rows);
		if (!$return) {
			return false;
		}
		if (!is_array($data)) {
			return $return;
		}
		if (!isset($data[$this->primary]) && ($id = $this->cfg->connection->lastInsertId($this->cfg->structure->getSequence($this->table)))) {
			$data[$this->primary] = $id;
		}
		return new $this->cfg->rowClass($data, $this, $this->cfg);
	}

	/** Update all rows in result set
	 * @param array ($column => $value)
	 * @return int number of affected rows or false in case of an error
	 */
	function update(array $data) {
		if ($this->cfg->freeze) {
			return false;
		}
		if (!$data) {
			return 0;
		}
		$values = [];
		$parameters = [];
		foreach ($data as $key => $val) {
			// doesn't use binding because $this->parameters can be filled by ? or :name
			$values[] = "$key = " . $this->quote($val);
			if ($val instanceof Literal && $val->parameters) {
				$parameters = array_merge($parameters, $val->parameters);
			}
		}
		if ($this->parameters) {
			$parameters = array_merge($parameters, $this->parameters);
		}
		// joins in UPDATE are supported only in MySQL
		$return = $this->query("UPDATE" . $this->topString($this->limit, $this->offset) . " $this->table SET " . implode(", ", $values) . $this->whereString(), $parameters);
		if (!$return) {
			return false;
		}
		return $return->rowCount();
	}

	/** Insert row or update if it already exists
	 * @param array ($column => $value)
	 * @param array ($column => $value)
	 * @param array ($column => $value), empty array means use $insert
	 * @return int number of affected rows or false in case of an error
	 */
	function insert_update(array $unique, array $insert, array $update = []) {
		if (!$update) {
			$update = $insert;
		}
		$insert = $unique + $insert;
		$values = "(" . implode(", ", array_keys($insert)) . ") VALUES " . $this->quote($insert);
		//! parameters
		if ($this->cfg->driver == "mysql") {
			$set = [];
			if (!$update) {
				$update = $unique;
			}
			foreach ($update as $key => $val) {
				$set[] = "$key = " . $this->quote($val);
				//! parameters
			}
			return $this->insert("$values ON DUPLICATE KEY UPDATE " . implode(", ", $set));
		} else {
			$connection = $this->cfg->connection;
			$errorMode = $connection->getAttribute(\PDO::ATTR_ERRMODE);
			$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			try {
				$return = $this->insert($values);
				$connection->setAttribute(\PDO::ATTR_ERRMODE, $errorMode);
				return $return;
			} catch (\PDOException $e) {
				$connection->setAttribute(\PDO::ATTR_ERRMODE, $errorMode);
				if ($e->getCode() == "23000" || $e->getCode() == "23505") { // "23000" - duplicate key, "23505" unique constraint pgsql
					if (!$update) {
						return 0;
					}
					$clone = clone $this;
					$return = $clone->where($unique)->update($update);
					return ($return ? $return + 1 : $return);
				}
				if ($errorMode == \PDO::ERRMODE_EXCEPTION) {
					throw $e;
				} elseif ($errorMode == \PDO::ERRMODE_WARNING) {
					trigger_error("PDOStatement::execute(): " . $e->getMessage(), E_USER_WARNING); // E_WARNING is unusable
				}
			}
		}
	}

	/** Delete all rows in result set
	 * @return int number of affected rows or false in case of an error
	 */
	function delete() {
		if ($this->cfg->freeze) {
			return false;
		}

		$join = $this->createJoins(implode(",", $this->conditions));
        $top = $this->topString($this->limit, $this->offset);
		$query = "DELETE$top FROM $this->table" . implode($join);
		if ($this->where)
			$query .= " WHERE " . implode($this->where);
		$return = $this->query($query, $this->parameters);

		if (!$return) {
			return false;
		}
		return $return->rowCount();
	}

	/** Add select clause, more calls appends to the end
	 * @param string for example "column, MD5(column) AS column_md5", empty string to reset previously set columns
	 * @param string ...
	 * @return Result fluent interface
	 */
	function select($columns) {
		$this->__destruct();
		if ($columns != "") {
			foreach (func_get_args() as $columns) {
				$this->select[] = $columns;
			}
		} else {
			$this->select = [];
		}
		return $this;
	}

	/** Add where condition, more calls appends with AND
	 * @param mixed string possibly containing ? or :name; or [$condition => $parameters, ...]
	 * @param mixed array accepted by PDOStatement::execute or a scalar value
	 * @param mixed ...
	 * @return Result fluent interface
	 */
	function where($condition, $parameters = []) {
		$args = func_get_args();
		return $this->whereOperator("AND", $args);
	}

	protected function whereOperator($operator, array $args) {
		$condition = $args[0];
		$parameters = (count($args) > 1 ? $args[1] : []);
		if (is_array($condition)) { // where(["column1" => 1, "column2 > ?" => 2])
			foreach ($condition as $key => $val) {
				$this->where($key, $val);
			}
			return $this;
		}
		$this->__destruct();
		$this->conditions[] = "$operator $condition";
		$condition = $this->removeExtraDots($condition);
		if (count($args) != 2 || strpbrk($condition, "?:")) { // where("column < ? OR column > ?", [1, 2])
			if (count($args) != 2 || !is_array($parameters)) { // where("column < ? OR column > ?", 1, 2)
				$parameters = array_slice($args, 1);
			}
			$this->parameters = array_merge($this->parameters, $parameters);
		} elseif ($parameters === null) { // where("column", null)
			$condition .= " IS NULL";
		} elseif ($parameters instanceof Result) { // where("column", $db->$table())
			$clone = clone $parameters;
			if (!$clone->select) {
				$clone->select($this->cfg->structure->getPrimary($clone->table));
			}
			if ($this->cfg->driver != "mysql") {
				if ($clone instanceof MultiResult) {
					array_shift($clone->select);
					$clone->single();
				}
				$condition .= " IN ($clone)";
				$this->parameters = array_merge($this->parameters, $clone->parameters);
			} else {
				$in = [];
				foreach ($clone as $row) {
					$row = array_values(iterator_to_array($row));
					if ($clone instanceof MultiResult && count($row) > 1) {
						array_shift($row);
					}
					if (count($row) == 1) {
						$in[] = $this->quote($row[0]);
					} else {
						$in[] = $this->quote($row);
					}
				}
				if ($in) {
					$condition .= " IN (" . implode(", ", $in) . ")";
				} else {
					$condition = "($condition) IS NOT NULL AND $condition IS NULL"; // $condition = "NOT id"
				}
			}
		} elseif (!is_array($parameters)) { // where("column", "x")
			$condition .= " = " . $this->quote($parameters);
		} else { // where("column", [1, 2])
			$condition = $this->whereIn($condition, $parameters);
		}
		$this->where[] = (preg_match('~^\)+$~', $condition)
				? $condition
				: ($this->where ? " $operator " : "") . "($condition)"
		);
		return $this;
	}

	protected function whereIn($condition, $parameters) {
		if (!$parameters) {
			$condition = "($condition) IS NOT NULL AND $condition IS NULL";
		} elseif ($this->cfg->driver != "oci") {
			$column = $condition;
			$condition .= " IN " . $this->quote($parameters);
			$nulls = array_filter($parameters, 'is_null');
			if ($nulls) {
				$condition = "$condition OR $column IS NULL";
			}
		} else { // http://download.oracle.com/docs/cd/B19306_01/server.102/b14200/expressions014.htm
			$or = [];
			for ($i=0; $i < count($parameters); $i += 1000) {
				$or[] = "$condition IN " . $this->quote(array_slice($parameters, $i, 1000));
			}
			$condition = implode(" OR ", $or);
		}
		return $condition;
	}

	function __call($name, array $args) {
		$operator = strtoupper($name);
		switch ($operator) {
			case "AND":
			case "OR":
				return $this->whereOperator($operator, $args);
		}
		trigger_error("Call to undefined method Result::$name()", E_USER_ERROR);
	}

	/** Shortcut for where()
	 * @param string
	 * @param mixed
	 * @param mixed ...
	 * @return Result fluent interface
	 */
	function __invoke($where, $parameters = []) {
		$args = func_get_args();
		return $this->whereOperator("AND", $args);
	}

	/** Add order clause, more calls appends to the end
	 * @param mixed "column1, column2 DESC" or ["column1", "column2 DESC"], empty string to reset previous order
	 * @param string ...
	 * @return Result fluent interface
	 */
	function order($columns) {
		$this->rows = null;
		if ($columns != "") {
			$columns = (is_array($columns) ? $columns : func_get_args());
			foreach ($columns as $column) {
				if ($this->union) {
					$this->unionOrder[] = $column;
				} else {
					$this->order[] = $column;
				}
			}
		} elseif ($this->union) {
			$this->unionOrder = [];
		} else {
			$this->order = [];
		}
		return $this;
	}

	/** Set limit clause, more calls rewrite old values
	 * @param int
	 * @param int
	 * @return Result fluent interface
	 */
	function limit($limit, $offset = null) {
		$this->rows = null;
		if ($this->union) {
			$this->unionLimit = +$limit;
			$this->unionOffset = +$offset;
		} else {
			$this->limit = +$limit;
			$this->offset = +$offset;
		}
		return $this;
	}

	/** Set group clause, more calls rewrite old values
	 * @param string
	 * @param string
	 * @return Result fluent interface
	 */
	function group($columns, $having = "") {
		$this->__destruct();
		$this->group = $columns;
		$this->having = $having;
		return $this;
	}

	/** Set select FOR UPDATE or LOCK IN SHARE MODE
	 * @param bool
	 * @return Result fluent interface
	 */
	function lock($exclusive = true) {
		$this->lock = $exclusive;
		return $this;
	}

	/**
	 * @param Result
	 * @param bool
	 * @return Result fluent interface
	 */
	function union(Result $result, $all = false) {
		$this->union[] = " UNION " . ($all ? "ALL " : "") . ($this->cfg->driver == "sqlite" || $this->cfg->driver == "oci" ? $result : "($result)");
		$this->parameters = array_merge($this->parameters, $result->parameters);
		return $this;
	}

	/** Execute aggregation function
	 * @param string
	 * @return string
	 */
	function aggregation($function) {
		$join = $this->createJoins(implode(",", $this->conditions) . ",$function");
		$query = "SELECT $function FROM $this->table" . implode($join);
		if ($this->where) {
			$query .= " WHERE " . implode($this->where);
		}
		foreach ($this->query($query, $this->parameters)->fetch() as $return) {
			return $return;
		}
	}

	/** Count number of rows
	 * @param string
	 * @return int
	 */
	function count($column = "") {
		if (!$column) {
			$this->execute();
			return count($this->data);
		}
		return $this->aggregation("COUNT($column)");
	}

	/** Return minimum value from a column
	 * @param string
	 * @return int
	 */
	function min($column) {
		return $this->aggregation("MIN($column)");
	}

	/** Return maximum value from a column
	 * @param string
	 * @return int
	 */
	function max($column) {
		return $this->aggregation("MAX($column)");
	}

	/** Return sum of values in a column
	 * @param string
	 * @return int
	 */
	function sum($column) {
		return $this->aggregation("SUM($column)");
	}

	/** Execute the built query
	 * @return null
	 */
	protected function execute() {
		if (!isset($this->rows)) {
			$result = false;
			$exception = null;
			$parameters = [];
			foreach (array_merge($this->select, [$this, $this->group, $this->having], $this->order, $this->unionOrder) as $val) {
				if (($val instanceof Literal || $val instanceof self) && $val->parameters) {
					$parameters = array_merge($parameters, $val->parameters);
				}
			}
			try {
				$result = $this->query($this->__toString(), $parameters);
			} catch (PDOException $exception) {
				// handled later
			}
			if (!$result) {
				if (!$this->select && $this->accessed) {
					$this->accessed = '';
					$this->access = [];
					$result = $this->query($this->__toString(), $parameters);
				} elseif ($exception) {
					throw $exception;
				}
			}
			$this->rows = [];
			if ($result) {
				$result->setFetchMode(\PDO::FETCH_ASSOC);
				foreach ($result as $key => $row) {
					if (isset($row[$this->primary])) {
						$key = $row[$this->primary];
						if (!is_string($this->access)) {
							$this->access[$this->primary] = true;
						}
					}
					$this->rows[$key] = new $this->cfg->rowClass($row, $this, $this->cfg);
				}
			}
			$this->data = $this->rows;
		}
	}

	/** Fetch next row of result
	 * @param string column name to return or an empty string for the whole row
	 * @return mixed string or null with $column, Row without $column, false if there is no row
	 */
	function fetch($column = '') {
		// no $this->select($column) because next calls can access different columns
		$this->execute();
		$return = current($this->data);
		next($this->data);
		if ($return && $column != '') {
			return $return[$column];
		}
		return $return;
	}

	/** Fetch all rows as associative array
	 * @param string
	 * @param string column name used for an array value or an empty string for the whole row
	 * @return array
	 */
	function fetchPairs($key, $value = '') {
		$return = [];
		$clone = clone $this;
		if ($value != "") {
			$clone->select = [];
			$clone->select("$key, $value"); // MultiResult adds its column
		} elseif ($clone->select) {
			array_unshift($clone->select, $key);
		} else {
			$clone->select = ["$key, $this->table.*"];
		}
		foreach ($clone as $row) {
			$values = array_values(iterator_to_array($row));
			if ($value != "" && $clone instanceof MultiResult) {
				array_shift($values);
			}
			$return[(string) $values[0]] = ($value != "" ? $values[(array_key_exists(1, $values) ? 1 : 0)] : $row); // isset($values[1]) - fetchPairs("id", "id")
		}
		return $return;
	}

	protected function access($key, $delete = false) {
		if ($delete) {
			if (is_array($this->access)) {
				$this->access[$key] = false;
			}
			return false;
		}
		if (!isset($key)) {
			$this->access = '';
		} elseif (!is_string($this->access)) {
			$this->access[$key] = true;
		}
		if (!$this->select && $this->accessed && (!isset($key) || !isset($this->accessed[$key]))) {
			$this->accessed = '';
			$this->rows = null;
			return true;
		}
		return false;
	}

	protected function single() {
	}

	// Iterator implementation (not IteratorAggregate because $this->data can be changed during iteration)

	function rewind() {
		$this->execute();
		$this->keys = array_keys($this->data);
		reset($this->keys);
	}

	/** @return Row */
	function current() {
		return $this->data[current($this->keys)];
	}

	/** @return string row ID */
	function key() {
		return current($this->keys);
	}

	function next() {
		next($this->keys);
	}

	function valid() {
		return current($this->keys) !== false;
	}

	// ArrayAccess implementation

	/** Test if row exists
	 * @param string row ID or array for where conditions
	 * @return bool
	 */
	function offsetExists($key) {
		$row = $this->offsetGet($key);
		return isset($row);
	}

	/** Get specified row
	 * @param string row ID or array for where conditions
	 * @return Row or null if there is no such row
	 */
	function offsetGet($key) {
		if ($this->single && !isset($this->data)) {
			$clone = clone $this;
			if (is_array($key)) {
				$clone->where($key)->limit(1);
			} else {
				$clone->where($this->primary, $key);
			}
			$return = $clone->fetch();
			if ($return) {
				return $return;
			}
		} else {
			$this->execute();
			if (is_array($key)) {
				foreach ($this->data as $row) {
					foreach ($key as $k => $v) {
						if ((isset($v) && $row[$k] !== null ? $row[$k] != $v : $row[$k] !== $v)) {
							continue 2;
						}
					}
					return $row;
				}
			} elseif (isset($this->data[$key])) {
				return $this->data[$key];
			}
		}
	}

	/** Mimic row
	 * @param string row ID
	 * @param Row
	 * @return null
	 */
	function offsetSet($key, $value) {
		$this->execute();
		$this->data[$key] = $value;
	}

	/** Remove row from result set
	 * @param string row ID
	 * @return null
	 */
	function offsetUnset($key) {
		$this->execute();
		unset($this->data[$key]);
	}

	// JsonSerializable implementation

	function jsonSerialize() {
		$this->execute();
		if ($this->cfg->jsonAsArray) {
			return array_values($this->data);
		} else {
			return $this->data;
		}
	}
}
