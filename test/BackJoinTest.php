<?php
use Lusito\NotORM\DB;

final class BackJoinTest extends TestCase
{
    public function testBackwardsJoinDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::author()->select("author.*, COUNT(DISTINCT application:application_tag:tag_id) AS tags")->group("author.id")->order("tags DESC") as $author)
            $result []= [$author['name'], $author['tags']];

        $this->assertEquals($result, [['Jakub Vrana', 3], ['David Grudl', 2]]);
    }

    public function testBackwardsJoinDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->author()->select("author.*, COUNT(DISTINCT application:application_tag:tag_id) AS tags")->group("author.id")->order("tags DESC") as $author)
            $result []= [$author['name'], $author['tags']];

        $this->assertEquals($result, [['Jakub Vrana', 3], ['David Grudl', 2]]);
    }
}
