<?php
use Lusito\NotORM\DB;

final class DetailTest extends TestCase
{
    public function testSingleRowDetailDB(): void
    {
        $this->setupDB();
        $result = [];
        $application = DB::getRow('application', 1);
        foreach ($application as $key => $val)
            $result []= [$key, $val];

        $this->assertEquals($result, [
            ['id', 1],
            ['author_id', 11],
            ['maintainer_id', 11],
            ['title', 'Adminer'],
            ['web', 'http://www.adminer.org/'],
            ['slogan', 'Database management in single PHP file'],
        ]);
    }

    public function testSingleRowDetailDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $application = $db->application[1];
        foreach ($application as $key => $val)
            $result []= [$key, $val];

        $this->assertEquals($result, [
            ['id', 1],
            ['author_id', 11],
            ['maintainer_id', 11],
            ['title', 'Adminer'],
            ['web', 'http://www.adminer.org/'],
            ['slogan', 'Database management in single PHP file'],
        ]);
    }
}
