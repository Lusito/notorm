<?php namespace Lusito\NotORM;

/** Friend visibility emulation */
abstract class Friendly {
	protected $table, $primary, $rows, $referenced = [];

	protected function access($key, $delete = false) {
	}
}

