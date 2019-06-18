<?php
use Lusito\NotORM\DB;

final class UpdatePrimaryTest extends TestCase
{
    public function testUpdatePrimaryKeyOfARowDB(): void
    {
        $this->setupDB();
        $application = DB::tag()->insert(['id' => 24, 'name' => 'HTML']);

        $this->assertEquals($application['id'], 24);
        $application['id'] = 25;
        $this->assertEquals($application['id'], 25);
        $this->assertEquals($application->update(), 1);
        $this->assertEquals($application->delete(), 1);
    }

    public function testUpdatePrimaryKeyOfARowDatabase(): void
    {
        $db = $this->setupDatabase();
        $application = $db->tag()->insert(['id' => 24, 'name' => 'HTML']);

        $this->assertEquals($application['id'], 24);
        $application['id'] = 25;
        $this->assertEquals($application['id'], 25);
        $this->assertEquals($application->update(), 1);
        $this->assertEquals($application->delete(), 1);
    }
}
