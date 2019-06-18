<?php
use Lusito\NotORM\DB;

final class FindOneTest extends TestCase
{
    public function testFindOneItemByTitleDB(): void
    {
        $this->setupDB();
        $result = [];
        $application = DB::application("title", "Adminer")->fetch();
        foreach ($application->application_tag("tag_id", 21) as $application_tag)
            $result []= $application_tag->tag["name"];

        $this->assertEquals($result, ['PHP']);
        $this->assertEquals(DB::application("title", "Adminer")->fetch("slogan"), 'Database management in single PHP file');
    }

    public function testFindOneItemByTitleDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $application = $db->application("title", "Adminer")->fetch();
        foreach ($application->application_tag("tag_id", 21) as $application_tag)
            $result []= $application_tag->tag["name"];

        $this->assertEquals($result, ['PHP']);
        $this->assertEquals($db->application("title", "Adminer")->fetch("slogan"), 'Database management in single PHP file');
    }
}
