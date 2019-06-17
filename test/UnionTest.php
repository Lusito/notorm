<?php

final class UnionTest extends TestCase
{
    public function testComplexUnion(): void
    {
        $db = $this->setupDatabase();
        
        // fixme: skip if:
        // $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        // preg_match('~^(sqlite|oci)$~', $driver) ? "Not supported in $driver.\n" : "";

        $result = [];
        $applications = $db->application()->select("id")->order("id DESC")->limit(2);
        $tags = $db->tag()->select("id")->order("id")->limit(2);
        foreach ($applications->union($tags)->order("id DESC") as $row)
            $result []= $row['id'];

        $this->assertEquals($result, [22, 21, 4, 3]);
    }
}
