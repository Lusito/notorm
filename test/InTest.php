<?php

final class InTest extends TestCase
{
    public function testInOperator(): void
    {
        $db = $this->setupDatabase();

        $this->assertEquals($db->application("maintainer_id", [])->count("*"), 0);
        $this->assertEquals($db->application("maintainer_id", [11])->count("*"), 1);
        $this->assertEquals($db->application("NOT maintainer_id", [11])->count("*"), 2);
        $this->assertEquals($db->application("NOT maintainer_id", [])->count("*"), 3);
    }
}
