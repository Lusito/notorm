<?php

final class AndTest extends TestCase
{
    public function testCallingAnd(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("author_id", 11)->and("maintainer_id", 11) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer']);
    }
}
