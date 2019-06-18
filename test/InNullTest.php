<?php

final class IsNullTest extends TestCase
{
    public function testInWithNullValue(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("maintainer_id", [11, null]) as $application)
            $result []= "$application[id]";

        $this->assertEquals($result, ['1', '2']);
    }
}
