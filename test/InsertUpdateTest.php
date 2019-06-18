<?php
use Lusito\NotORM\DB;

final class InsertUpdateTest extends TestCase
{
    public function testInsertOrUpdateDB(): void
    {
        $this->setupDB();
        $result = [];
        for ($i=0; $i < 2; $i++)
            $result []= DB::application()->insert_update(["id" => 5], ["author_id" => 12, "title" => "Texy", "web" => "", "slogan" => "$i"]);
        $this->assertEquals($result, [1, 2]);

        $application = DB::getRow('application', 5);
        $this->assertEquals($application->application_tag()->insert_update(["tag_id" => 21], []), 1);
        DB::application("id", 5)->delete();
    }

    public function testInsertOrUpdateDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        for ($i=0; $i < 2; $i++)
            $result []= $db->application()->insert_update(["id" => 5], ["author_id" => 12, "title" => "Texy", "web" => "", "slogan" => "$i"]);
        $this->assertEquals($result, [1, 2]);

        $application = $db->application[5];
        $this->assertEquals($application->application_tag()->insert_update(["tag_id" => 21], []), 1);
        $db->application("id", 5)->delete();
    }
}
