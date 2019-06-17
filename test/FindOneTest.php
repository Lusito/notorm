<?php

final class FindOneTest extends TestCase
{
    public function testFindOneItemByTitle(): void
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
