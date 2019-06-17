<?php

final class SubQueryTest extends TestCase
{
    public function testSubQueries(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $unknownBorn = $db->author("born", null); // authors with unknown date of born
        foreach ($db->application("author_id", $unknownBorn) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'JUSH', 'Nette', 'Dibi']);
    }
}
