<?php

final class OrTest extends TestCase
{
    public function testCallingOr(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("author_id", 12)->or("maintainer_id", 11)->order("title") as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'Dibi', 'Nette']);
    }
}
