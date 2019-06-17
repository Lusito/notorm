<?php

final class MultiResultLoopTest extends TestCase
{
    public function testUsingMultiResultSeveralTimes(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $application = $db->application[1];
        for ($i = 0; $i < 4; $i++)
            $result []= count($application->application_tag());

        $this->assertEquals($result, [2, 2, 2, 2]);
    }
}
