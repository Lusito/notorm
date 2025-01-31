<?php
use Lusito\NotORM\DB;
use Lusito\NotORM\Structure\ConventionStructure;

final class PrefixTest extends TestCase
{
    public function testTablePrefixDB(): void
    {
        $this->setupDB(function($builder) {
            $builder->structure(new ConventionStructure('id', '%s_id', '%s', 'prefix_'));
        });

        $applications = DB::application("author.name", "Jakub Vrana");
        $this->assertEquals("$applications", 'SELECT prefix_application.* FROM prefix_application LEFT JOIN prefix_author AS author ON prefix_application.author_id = author.id WHERE (author.name = \'Jakub Vrana\')');
    }

    public function testTablePrefixDatabase(): void
    {
        $db = $this->setupDatabase(function($builder) {
            $builder->structure(new ConventionStructure('id', '%s_id', '%s', 'prefix_'));
        });

        $applications = $db->application("author.name", "Jakub Vrana");
        $this->assertEquals("$applications", 'SELECT prefix_application.* FROM prefix_application LEFT JOIN prefix_author AS author ON prefix_application.author_id = author.id WHERE (author.name = \'Jakub Vrana\')');
    }
}

