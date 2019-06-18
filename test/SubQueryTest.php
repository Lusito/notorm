<?php
use Lusito\NotORM\DB;

final class SubQueryTest extends TestCase
{
    public function testSubQueriesDB(): void
    {
        $this->setupDB();
        $result = [];
        $unknownBorn = DB::author("born", null); // authors with unknown date of born
        foreach (DB::application("author_id", $unknownBorn) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'JUSH', 'Nette', 'Dibi']);
    }

    public function testSubQueriesDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $unknownBorn = $db->author("born", null); // authors with unknown date of born
        foreach ($db->application("author_id", $unknownBorn) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'JUSH', 'Nette', 'Dibi']);
    }
}
