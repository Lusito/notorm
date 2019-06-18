<?php namespace Lusito\NotORM;

/** SQL literal value */
class Literal {
	protected $value = '';

	/** @var array */
	public $parameters = [];

	/** Create literal value
	 * @param string
	 * @param mixed parameter
	 * @param mixed ...
	 */
	function __construct($value, ...$parameters) {
		$this->value = $value;
		$this->parameters = $parameters;
	}

	/** Get literal value
	 * @return string
	 */
	function __toString() {
		return $this->value;
	}
}
