<?php

final class UpdatePrimaryTest extends TestCase
{
    public function testUpdatePrimaryKeyOfARow(): void
    {
        $db = $this->setupDatabase();
        $application = $db->tag()->insert(array('id' => 24, 'name' => 'HTML'));

        $this->assertEquals($application['id'], 24);
        $application['id'] = 25;
        $this->assertEquals($application['id'], 25);
        $this->assertEquals($application->update(), 1);
        $this->assertEquals($application->delete(), 1);
    }
}
