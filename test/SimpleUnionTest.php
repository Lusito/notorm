<?php

final class SimpleUnionTest extends TestCase
{
    public function testSimpleUnion(): void
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
