<?php
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
    public function testStructureForNonConventionalColumn(): void
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
