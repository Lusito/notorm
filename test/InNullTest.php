<?php
use Lusito\NotORM\DB;

final class IsNullTest extends TestCase
{
    public function testInWithNullValueDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::application("maintainer_id", [11, null]) as $application)
            $result []= $application['id'];

        $this->assertEquals($result, ['1', '2']);
    }

    public function testInWithNullValueDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("maintainer_id", [11, null]) as $application)
            $result []= "$application[id]";

        $this->assertEquals($result, ['1', '2']);
    }
}
