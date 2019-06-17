<?php namespace Lusito\NotORM;

class Config {
    public const WRITE_ONLY_PROPS = ['debug', 'debugTimer', 'freeze', 'rowClass', 'jsonAsArray'];
	public $connection, $driver, $structure;
    public $cache = null;
	public $debug = false;
	public $debugTimer;
	public $freeze = false;
	public $rowClass = 'Lusito\NotORM\Row';
	public $jsonAsArray = false;
}
