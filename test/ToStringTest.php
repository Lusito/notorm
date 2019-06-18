<?php
use Lusito\NotORM\DB;

final class ToStringTest extends TestCase
{
    public function testToStringDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::application() as $application)
            $result []= "$application";

        $this->assertEquals($result, ['1', '2', '3', '4']);
    }

    public function testToStringDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application() as $application)
            $result []= "$application";

        $this->assertEquals($result, ['1', '2', '3', '4']);
    }
}
