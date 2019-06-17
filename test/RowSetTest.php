<?php

final class RowSetTest extends TestCase
{
    public function testUpdateRowThroughProperty(): void
    {
        $db = $this->setupDatabase();
        
        $application = $db->application[1];
        $application->author = $db->author[12];
        $this->assertEquals($application->update(), 1);
        $application->update(array("author_id" => 11));
    }
}
