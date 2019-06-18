<?php
use Lusito\NotORM\DB;

final class ArrayOffsetTest extends TestCase
{
    public function testArrayOffsetDB(): void
    {
        $this->setupDB();
        $where = [
            "author_id" => "11",
            "maintainer_id" => null,
        ];

        $this->assertEquals(DB::getRow('application', $where)["id"], 2);
        $applications = DB::application()->order("id");
        $this->assertEquals($applications[$where]["id"], 2);
    }

    public function testArrayOffsetDatabase(): void
    {
        $db = $this->setupDatabase();
        $where = [
            "author_id" => "11",
            "maintainer_id" => null,
        ];

        $this->assertEquals($db->application[$where]["id"], 2);
        $applications = $db->application()->order("id");
        $this->assertEquals($applications[$where]["id"], 2);
    }
}
