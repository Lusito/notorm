<?php
use Lusito\NotORM\DB;

final class InMultipleTest extends TestCase
{
    public function testInOperatorWithMultiResultDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::author()->order("id") as $author) {
            foreach (DB::application_tag("application_id", $author->application())->order("application_id, tag_id") as $application_tag)
                $result []= [$author . '', $application_tag['application_id'], $application_tag['tag_id']];
        }

        $this->assertEquals($result, [
            [11, 1, 21],
            [11, 1, 22],
            [11, 2, 23],
            [12, 3, 21],
            [12, 4, 21],
            [12, 4, 22]
        ]);
    }

    public function testInOperatorWithMultiResultDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->author()->order("id") as $author) {
            foreach ($db->application_tag("application_id", $author->application())->order("application_id, tag_id") as $application_tag)
                $result []= [$author . '', $application_tag['application_id'], $application_tag['tag_id']];
        }

        $this->assertEquals($result, [
            [11, 1, 21],
            [11, 1, 22],
            [11, 2, 23],
            [12, 3, 21],
            [12, 4, 21],
            [12, 4, 22]
        ]);
    }
}
