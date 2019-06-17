<?php

final class ToStringTest extends TestCase
{
    public function testToString(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application() as $application)
            $result []= "$application";

        $this->assertEquals($result, ['1', '2', '3', '4']);
    }
}
