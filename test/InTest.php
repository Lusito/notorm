<?php
use Lusito\NotORM\DB;

final class InTest extends TestCase
{
    public function testInOperatorDB(): void
    {
        $this->setupDB();

        $this->assertEquals(DB::application("maintainer_id", [])->count("*"), 0);
        $this->assertEquals(DB::application("maintainer_id", [11])->count("*"), 1);
        $this->assertEquals(DB::application("NOT maintainer_id", [11])->count("*"), 2);
        $this->assertEquals(DB::application("NOT maintainer_id", [])->count("*"), 3);
    }

    public function testInOperatorDatabase(): void
    {
        $db = $this->setupDatabase();

        $this->assertEquals($db->application("maintainer_id", [])->count("*"), 0);
        $this->assertEquals($db->application("maintainer_id", [11])->count("*"), 1);
        $this->assertEquals($db->application("NOT maintainer_id", [11])->count("*"), 2);
        $this->assertEquals($db->application("NOT maintainer_id", [])->count("*"), 3);
    }
}
