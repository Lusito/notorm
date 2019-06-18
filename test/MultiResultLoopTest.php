<?php
use Lusito\NotORM\DB;

final class MultiResultLoopTest extends TestCase
{
    public function testUsingMultiResultSeveralTimesDB(): void
    {
        $this->setupDB();
        $result = [];
        $application = DB::getRow('application', 1);
        for ($i = 0; $i < 4; $i++)
            $result []= count($application->application_tag());

        $this->assertEquals($result, [2, 2, 2, 2]);
    }

    public function testUsingMultiResultSeveralTimesDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $application = $db->application[1];
        for ($i = 0; $i < 4; $i++)
            $result []= count($application->application_tag());

        $this->assertEquals($result, [2, 2, 2, 2]);
    }
}
