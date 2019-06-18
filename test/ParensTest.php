<?php
use Lusito\NotORM\DB;

final class ParensTest extends TestCase
{
    public function testUsingParensDB(): void
    {
        $this->setupDB();
        $result = [];

        $applications = DB::application()
            ->where("(author_id", 11)->and("maintainer_id", 11)->where(")")
            ->or("(author_id", 12)->and("maintainer_id", 12)->where(")");

        foreach ($applications->order("title") as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'Dibi', 'Nette']);
    }

    public function testUsingParensDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];

        $applications = $db->application()
            ->where("(author_id", 11)->and("maintainer_id", 11)->where(")")
            ->or("(author_id", 12)->and("maintainer_id", 12)->where(")");

        foreach ($applications->order("title") as $application)
            $result []= $application['title'];

        $this->assertEquals($result, ['Adminer', 'Dibi', 'Nette']);
    }
}
