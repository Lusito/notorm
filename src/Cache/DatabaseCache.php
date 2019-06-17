<?php namespace Lusito\NotORM\Cache;

/** Cache storing data to the "notorm" table in database
 */
class DatabaseCache implements Cache {
	private $connection;

	function setConnection(\PDO $connection) {
		$this->connection = $connection;
	}

	function load($key) {
		$result = $this->connection->prepare("SELECT data FROM notorm WHERE id = ?");
		$result->execute([$key]);
		$return = $result->fetchColumn();
		return $return ? unserialize($return) : null;
	}

	function save($key, $data) {
		// REPLACE is not supported by PostgreSQL and MS SQL
		$parameters = array(serialize($data), $key);
		$result = $this->connection->prepare("UPDATE notorm SET data = ? WHERE id = ?");
		$result->execute($parameters);
		if (!$result->rowCount()) {
			$result = $this->connection->prepare("INSERT INTO notorm (data, id) VALUES (?, ?)");
			try {
				@$result->execute($parameters); // @ - ignore duplicate key error
			} catch (\PDOException $e) {
				if ($e->getCode() != "23000") { // "23000" - duplicate key
					throw $e;
				}
			}
		}
	}
}
