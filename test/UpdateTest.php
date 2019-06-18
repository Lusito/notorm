<?php
use Lusito\NotORM\DB;
use Lusito\NotORM\Literal;

final class UpdateTest extends TestCase
{
    public function testInsertUpdateDeleteDB(): void
    {
        $this->setupDB();

        $id = 5; // auto_increment is disabled in demo
        $application = DB::application()->insert([
            "id" => $id,
            "author_id" => DB::getRow('author', 12),
            "title" => new Literal("'Texy'"),
            "web" => "",
            "slogan" => "The best humane Web text generator"
        ]);
        $application->application_tag()->insert(["tag_id" => 21]);

        // retrieve the really stored value
        $application = DB::getRow('application', $id);
        $this->assertEquals($application["title"], 'Texy');

        $application["web"] = "http://texy.info/";
        $this->assertEquals($application->update(), 1);
        $this->assertEquals(DB::getRow('application', $id)["web"], 'http://texy.info/');

        DB::application_tag("application_id", 5)->delete(); // foreign keys may be disabled
        $this->assertEquals($application->delete(), 1);
        $this->assertEquals(count(DB::application("id", $id)), 0);
    }

    public function testInsertUpdateDeleteDatabase(): void
    {
        $db = $this->setupDatabase();

        $id = 5; // auto_increment is disabled in demo
        $application = $db->application()->insert([
            "id" => $id,
            "author_id" => $db->author[12],
            "title" => new Literal("'Texy'"),
            "web" => "",
            "slogan" => "The best humane Web text generator"
        ]);
        $application->application_tag()->insert(["tag_id" => 21]);

        // retrieve the really stored value
        $application = $db->application[$id];
        $this->assertEquals($application["title"], 'Texy');

        $application["web"] = "http://texy.info/";
        $this->assertEquals($application->update(), 1);
        $this->assertEquals($db->application[$id]["web"], 'http://texy.info/');

        $db->application_tag("application_id", 5)->delete(); // foreign keys may be disabled
        $this->assertEquals($application->delete(), 1);
        $this->assertEquals(count($db->application("id", $id)), 0);
    }
}
