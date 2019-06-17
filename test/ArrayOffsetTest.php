<?php

final class ArrayOffsetTest extends TestCase
{
    public function testArrayOffset(): void
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
