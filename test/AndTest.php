<?php
use Lusito\NotORM\DB;

final class AndTest extends TestCase
{
    public function testCallingAndDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::application("author_id", 11)->and("maintainer_id", 11) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer']);
    }

    public function testCallingAndDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("author_id", 11)->and("maintainer_id", 11) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer']);
    }
}
