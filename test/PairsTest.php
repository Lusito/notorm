<?php

final class PairsTest extends TestCase
{
    public function testFetchPairs(): void
    {
        $db = $this->setupDatabase();

        $this->assertEquals($db->application()->order("title")->fetchPairs("id", "title"), [
            1 => 'Adminer',
            4 => 'Dibi',
            2 => 'JUSH',
            3 => 'Nette'
        ]);
        $this->assertEquals($db->application()->order("id")->fetchPairs("id", "id"), [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4
        ]);
    }
}
