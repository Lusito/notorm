<?php

final class BackJoinTest extends TestCase
{
    public function testBackwardsJoin(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->author()->select("author.*, COUNT(DISTINCT application:application_tag:tag_id) AS tags")->group("author.id")->order("tags DESC") as $author)
            $result []= [$author['name'], $author['tags']];

        $this->assertEquals($result, [['Jakub Vrana', 3], ['David Grudl', 2]]);
    }
}
