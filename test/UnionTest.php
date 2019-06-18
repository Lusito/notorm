<?php
use Lusito\NotORM\DB;

final class UnionTest extends TestCase
{
    /**
     * @group noSQLite
     * @group noOracle
     */
    public function testComplexUnionDB(): void
    {
        $this->setupDB();

        $result = [];
        $applications = DB::application()->select("id")->order("id DESC")->limit(2);
        $tags = DB::tag()->select("id")->order("id")->limit(2);
        foreach ($applications->union($tags)->order("id DESC") as $row)
            $result []= $row['id'];

        $this->assertEquals($result, [22, 21, 4, 3]);
    }

    /**
     * @group noSQLite
     * @group noOracle
     */
    public function testComplexUnionDatabase(): void
    {
        $db = $this->setupDatabase();

        $result = [];
        $applications = $db->application()->select("id")->order("id DESC")->limit(2);
        $tags = $db->tag()->select("id")->order("id")->limit(2);
        foreach ($applications->union($tags)->order("id DESC") as $row)
            $result []= $row['id'];

        $this->assertEquals($result, [22, 21, 4, 3]);
    }
}
