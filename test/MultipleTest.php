<?php
use Lusito\NotORM\DB;

final class MultipleTest extends TestCase
{
    public function testMultipleArgumentsDB(): void
    {
        $this->setupDB();
        $result = [];
        $application = DB::getRow('application', 1);
        $tags = $application->application_tag()
            ->select("application_id", "tag_id")
            ->order("application_id DESC", "tag_id DESC");
        foreach ($tags as $application_tag)
            $result []= [$application_tag['application_id'], $application_tag['tag_id']];

        $this->assertEquals($result, [[1, 22], [1, 21]]);
    }

    public function testMultipleArgumentsDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $application = $db->application[1];
        $tags = $application->application_tag()
            ->select("application_id", "tag_id")
            ->order("application_id DESC", "tag_id DESC");
        foreach ($tags as $application_tag)
            $result []= [$application_tag['application_id'], $application_tag['tag_id']];

        $this->assertEquals($result, [[1, 22], [1, 21]]);
    }
}
