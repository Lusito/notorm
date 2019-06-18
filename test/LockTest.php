<?php
use Lusito\NotORM\DB;

final class LockTest extends TestCase
{
    public function testSelectLockDB(): void
    {
        $this->setupDB();
        $this->assertEquals(DB::application()->lock(), 'SELECT * FROM application FOR UPDATE');
    }

    public function testSelectLockDatabase(): void
    {
        $db = $this->setupDatabase();
        $this->assertEquals($db->application()->lock(), 'SELECT * FROM application FOR UPDATE');
    }
}
