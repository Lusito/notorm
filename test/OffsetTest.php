<?php

final class OffsetTest extends TestCase
{
    public function testLimitAndOffset(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        
        $application = $db->application[1];
        foreach ($application->application_tag()->order("tag_id")->limit(1, 1) as $application_tag)
            $result []= $application_tag->tag["name"];

        $this->assertEquals($result, ['MySQL']);

        $result = [];
        foreach ($db->application() as $application) {
            foreach ($application->application_tag()->order("tag_id")->limit(1, 1) as $application_tag)
                $result []= $application_tag->tag["name"];
        }
        $this->assertEquals($result, ['MySQL', 'MySQL']);
    }
}
