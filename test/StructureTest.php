<?php
use Lusito\NotORM\DB;
use Lusito\NotORM\Structure\ConventionStructure;

class SoftwareConvention extends ConventionStructure {
	function getReferencedTable($name, $table) {
		switch ($name) {
			case 'maintainer': return parent::getReferencedTable('author', $table);
		}
		return parent::getReferencedTable($name, $table);
	}
}

final class StructureTest extends TestCase
{
    public function testStructureForNonConventionalColumnDB(): void
    {
        $this->setupDB(function($builder) {
            $builder->structure(new SoftwareConvention);
        });
        $maintainer = DB::getRow('application', 1)->maintainer;
        $this->assertEquals($maintainer['name'], 'Jakub Vrana');

        $result = [];
        foreach ($maintainer->application()->via('maintainer_id') as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer']);
    }

    public function testStructureForNonConventionalColumnDatabase(): void
    {
        $db = $this->setupDatabase(function($builder) {
            $builder->structure(new SoftwareConvention);
        });
        $maintainer = $db->application[1]->maintainer;
        $this->assertEquals($maintainer['name'], 'Jakub Vrana');

        $result = [];
        foreach ($maintainer->application()->via('maintainer_id') as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer']);
    }
}
