<?php
use Lusito\NotORM\DB;

final class ViaTest extends TestCase
{
    public function testViaDB(): void
    {
        $this->setupDB();
        $result = [];

        foreach (DB::author() as $author) {
            $applications = $author->application()->via("maintainer_id");
            foreach ($applications as $application)
                $result []= [$author['name'], $application['title']];
        }

        $this->assertEquals($result, [['Jakub Vrana', 'Adminer'], ['David Grudl', 'Nette'], ['David Grudl', 'Dibi']]);
        $this->assertEquals("$applications", 'SELECT * FROM application WHERE (application.maintainer_id IN (11, 12))');
    }

    public function testViaDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];

        foreach ($db->author() as $author) {
            $applications = $author->application()->via("maintainer_id");
            foreach ($applications as $application)
                $result []= [$author['name'], $application['title']];
        }

        $this->assertEquals($result, [['Jakub Vrana', 'Adminer'], ['David Grudl', 'Nette'], ['David Grudl', 'Dibi']]);
        $this->assertEquals("$applications", 'SELECT * FROM application WHERE (application.maintainer_id IN (11, 12))');
    }
}
