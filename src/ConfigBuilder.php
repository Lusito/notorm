<?php namespace Lusito\NotORM;

use Lusito\NotORM\Structure\ConventionStructure;
use Lusito\NotORM\Structure\Structure;
use Lusito\NotORM\Cache\Cache;

class ConfigBuilder {
    private $data = [];
    private $pdoOptions = [];
    private $pdoAttributes = [];

    public function build() {
        $cfg = new Config();

        $cfg->connection = new \PDO(
            $this->data['dsn'],
            $this->data['username'],
            $this->data['password'],
            $this->pdoOptions
        );

        foreach($this->pdoAttributes as $key => $value)
            $cfg->connection->setAttribute($key, $value);

        if (isset($this->data['sqlMode']))
            $cfg->connection->query("SET sql_mode='{$this->data['sqlMode']}'");

		$cfg->driver = $cfg->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);

        $cfg->cache = $this->data['cache'] ?? null;
        if($cfg->cache && method_exists($cfg->cache, 'setConnection')) 
            $cfg->cache->setConnection($cfg->connection);

        $cfg->structure = $this->data['structure'] ?? new ConventionStructure();
        if(method_exists($cfg->structure, 'setConnection')) 
            $cfg->structure->setConnection($cfg->connection);
        if($cfg->cache && method_exists($cfg->structure, 'setCache')) 
            $cfg->structure->setCache($cfg->cache);

        $cfg->debug = $this->data['debug'] ?? false;
        $cfg->debugTimer = $this->data['debugTimer'] ?? null;
        $cfg->freeze = $this->data['freeze'] ?? false;
        $cfg->rowClass = $this->data['rowClass'] ?? 'Lusito\NotORM\Row';
        $cfg->jsonAsArray = $this->data['jsonAsArray'] ?? false;

        return $cfg;
    }

    public function __construct(string $dsn, string $username=null, string $password=null) {
        $this->data['dsn'] = $dsn;
        $this->data['username'] = $username;
        $this->data['password'] = $password;
    }

    public function pdoOption(int $attribute, $value) {
        $this->pdoOptions[$attribute] = $value;
        return $this;
    }
    
    public function pdoAttribute(int $attribute, $value) {
        $this->pdoAttributes[$attribute] = $value;
        return $this;
    }

    public function sqlMode($sqlMode) {
        $this->data['sqlMode'] = $sqlMode;
        return $this;
    }

    public function structure(Structure $structure) {
        $this->data['structure'] = $structure;
        return $this;
    }

    public function cache(Cache $cache) {
        $this->data['cache'] = $cache;
        return $this;
    }

    public function debug(bool $debug) {
        $this->data['debug'] = $debug;
        return $this;
    }

    public function debugTimer(callable $debugTimer) {
        $this->data['debugTimer'] = $debugTimer;
        return $this;
    }

    public function freeze(bool $freeze) {
        $this->data['freeze'] = $freeze;
        return $this;
    }

    public function rowClass(string $rowClass) {
        $this->data['rowClass'] = $rowClass;
        return $this;
    }

    public function jsonAsArray(bool $jsonAsArray) {
        $this->data['jsonAsArray'] = $jsonAsArray;
        return $this;
    }
}

