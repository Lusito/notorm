<?php

final class LockTest extends TestCase
{
    public function testSelectLock(): void
    {
        $db = $this->setupDatabase();
        $this->assertEquals($db->application()->lock(), 'SELECT * FROM application FOR UPDATE');
    }
}
