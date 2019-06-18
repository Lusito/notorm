<?php
use Lusito\NotORM\DB;

final class RowSetTest extends TestCase
{
    public function testUpdateRowThroughPropertyDB(): void
    {
        $this->setupDB();
        
        $application = DB::getRow('application', 1);
        $application->author = DB::getRow('author', 12);
        $this->assertEquals($application->update(), 1);
        $application->update(["author_id" => 11]);
    }

    public function testUpdateRowThroughPropertyDatabase(): void
    {
        $db = $this->setupDatabase();
        
        $application = $db->application[1];
        $application->author = $db->author[12];
        $this->assertEquals($application->update(), 1);
        $application->update(["author_id" => 11]);
    }
}
