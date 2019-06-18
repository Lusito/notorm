<?php
use Lusito\NotORM\DB;

final class SearchOrderTest extends TestCase
{
    public function testSearchAndOrderItemsDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::application("web LIKE ?", "http://%")->order("title")->limit(3) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'Dibi', 'JUSH']);
    }

    public function testSearchAndOrderItemsDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application("web LIKE ?", "http://%")->order("title")->limit(3) as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'Dibi', 'JUSH']);
    }
}
