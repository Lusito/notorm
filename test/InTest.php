<?php

final class InTest extends TestCase
{
    public function testInOperator(): void
    {
        $db = $this->setupDatabase();

        $this->assertEquals($db->application("maintainer_id", array())->count("*"), 0);
        $this->assertEquals($db->application("maintainer_id", array(11))->count("*"), 1);
        $this->assertEquals($db->application("NOT maintainer_id", array(11))->count("*"), 2);
        $this->assertEquals($db->application("NOT maintainer_id", array())->count("*"), 3);
    }
}
