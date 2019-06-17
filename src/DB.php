<?php namespace Lusito\NotORM;

/**
 * Static Database access
 */
class DB {
    /** @var Config */
    private static $cfg = null;

    public static function setConfig(Config $cfg) {
        self::$cfg = $cfg;
    }

    public static function hasConfig() {
        return !is_null(self::$cfg);
    }

    public static function __callStatic($table, $where) {
        return self::getTable($table, $where);
    }

	/** Get table
	 * @param string
	 * @param array (["condition"[, array("value")]]) passed to Result::where()
	 * @return Result
	 */
    public static function getTable($table, ...$where) {
		$return = new Result(self::$cfg->structure->getReferencingTable($table, ''), self::$cfg);
		if ($where)
			call_user_func_array(array($return, 'where'), $where);
		return $return;
    }

    public static function getRow($table, $id) {
        $result = new Result(self::$cfg->structure->getReferencingTable($table, ''), self::$cfg, true);
		return $result[$id];
    }

    public static function setConfigValue($key, $value) {
        if(in_array($key, Config::WRITE_ONLY_PROPS))
            self::$cfg->$key = $value;
    }

    public static function beginTransaction() {
        self::$cfg->connection->beginTransaction();
    }

    public static function commitTransaction() {
        self::$cfg->connection->commit();
    }

    public static function rollbackTransaction() {
        self::$cfg->connection->rollback();
    }

	/** Get last insert ID
	 * @return string number
	 */
	public static function lastInsertId() {
		return self::$cfg->connection->lastInsertId();
	}
}
