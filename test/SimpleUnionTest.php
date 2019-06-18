<?php
use Lusito\NotORM\DB;

final class SimpleUnionTest extends TestCase
{
    public function testSimpleUnionDB(): void
    {
        $this->setupDB();
        $result = [];
        $applications = DB::application()->select("id");
        $tags = DB::tag()->select("id");
        foreach ($applications->union($tags)->order("id DESC") as $row) 
            $result []= $row['id'];

        $this->assertEquals($result, [23, 22, 21, 4, 3, 2, 1]);
    }

    public function testSimpleUnionDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $applications = $db->application()->select("id");
        $tags = $db->tag()->select("id");
        foreach ($applications->union($tags)->order("id DESC") as $row) 
            $result []= $row['id'];

        $this->assertEquals($result, [23, 22, 21, 4, 3, 2, 1]);
    }
}
