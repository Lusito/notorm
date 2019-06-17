<?php

final class SearchOrderTest extends TestCase
{
    public function testSearchAndOrderItems(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("web LIKE ?", "http://%")->order("title")->limit(3) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'Dibi', 'JUSH']);
    }
}
