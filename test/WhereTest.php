<?php
use Lusito\NotORM\DB;

final class WhereTest extends TestCase
{
    public function testWhereDB(): void
    {
        $this->setupDB();
        $result = [];

        $list = [
            DB::application("id", 4),
            DB::application("id < ?", 4),
            DB::application("id < ?", [4]),
            DB::application("id", [1, 2]),
            DB::application("id", null),
            DB::application("id", DB::application()),
            DB::application("id < ?", 4)->where("maintainer_id IS NOT NULL"),
            DB::application(["id < ?" => 4, "author_id" => 12]),
         ];

        foreach ($list as $result)
            $result []= array_keys(iterator_to_array($result->order("id"))); // aggregation("GROUP_CONCAT(id)") is not available in all drivers

        $this->assertEquals($result, [
            [4],
            [1, 2, 3],
            [1, 2, 3],
            [1, 2],
            [],
            [1, 2, 3, 4],
            [1, 3],
            [3]
        ]);
    }

    public function testWhereDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];

        $list = [
            $db->application("id", 4),
            $db->application("id < ?", 4),
            $db->application("id < ?", [4]),
            $db->application("id", [1, 2]),
            $db->application("id", null),
            $db->application("id", $db->application()),
            $db->application("id < ?", 4)->where("maintainer_id IS NOT NULL"),
            $db->application(["id < ?" => 4, "author_id" => 12]),
         ];

        foreach ($list as $result)
            $result []= array_keys(iterator_to_array($result->order("id"))); // aggregation("GROUP_CONCAT(id)") is not available in all drivers

        $this->assertEquals($result, [
            [4],
            [1, 2, 3],
            [1, 2, 3],
            [1, 2],
            [],
            [1, 2, 3, 4],
            [1, 3],
            [3]
        ]);
    }
}
