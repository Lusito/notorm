<?php

final class WhereTest extends TestCase
{
    public function testWhere(): void
    {
        $db = $this->setupDatabase();
        $result = [];

        $list = [
            $db->application("id", 4),
            $db->application("id < ?", 4),
            $db->application("id < ?", array(4)),
            $db->application("id", array(1, 2)),
            $db->application("id", null),
            $db->application("id", $db->application()),
            $db->application("id < ?", 4)->where("maintainer_id IS NOT NULL"),
            $db->application(array("id < ?" => 4, "author_id" => 12)),
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
