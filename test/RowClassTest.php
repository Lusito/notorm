<?php
use Lusito\NotORM\Row;

class TestRow extends Row {
	
	function offsetExists($key) {
		return parent::offsetExists(preg_replace('~^test_~', '', $key));
	}
	
	function offsetGet($key) {
		return parent::offsetGet(preg_replace('~^test_~', '', $key));
	}
}

final class RowClassTest extends TestCase
{
    public function testCustomRowClass(): void
    {
        $db = $this->setupDatabase();
        $db->rowClass = 'TestRow';

        $application = $db->application[1];

        $this->assertEquals($application['test_title'], 'Adminer');
        $this->assertEquals($application->author["test_name"], 'Jakub Vrana');

        $db->rowClass = 'Lusito\NotORM\Row';
    }
}
