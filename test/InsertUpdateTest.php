<?php

final class InsertUpdateTest extends TestCase
{
    public function testInsertOrUpdate(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        for ($i=0; $i < 2; $i++)
            $result []= $db->application()->insert_update(array("id" => 5), array("author_id" => 12, "title" => "Texy", "web" => "", "slogan" => "$i"));
        $this->assertEquals($result, [1, 2]);

        $application = $db->application[5];
        $this->assertEquals($application->application_tag()->insert_update(array("tag_id" => 21), array()), 1);
        $db->application("id", 5)->delete();
    }
}
