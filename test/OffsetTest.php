<?php
use Lusito\NotORM\DB;

final class OffsetTest extends TestCase
{
    public function testLimitAndOffsetDB(): void
    {
        $this->setupDB();
        $result = [];
        
        $application = DB::getRow('application', 1);
        foreach ($application->application_tag()->order("tag_id")->limit(1, 1) as $application_tag)
            $result []= $application_tag->tag["name"];

        $this->assertEquals($result, ['MySQL']);

        $result = [];
        foreach (DB::application() as $application) {
            foreach ($application->application_tag()->order("tag_id")->limit(1, 1) as $application_tag)
                $result []= $application_tag->tag["name"];
        }
        $this->assertEquals($result, ['MySQL', 'MySQL']);
    }

    public function testLimitAndOffsetDatabase(): void
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
