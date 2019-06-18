<?php
use Lusito\NotORM\DB;
use Lusito\NotORM\Literal;

final class DateTimeTest extends TestCase
{
    public function testDateTimeProcessingDB(): void
    {
        $this->setupDB();
        $date = new DateTime("2011-08-30");

        DB::application()->insert([
            "id" => 5,
            "author_id" => 11,
            "title" => $date,
            "slogan" => new Literal("?", $date),
        ]);
        $application = DB::application()->where("title = ?", $date)->fetch();

        $this->assertEquals($application['slogan'], '2011-08-30 00:00:00');
        $application->delete();
    }

    public function testDateTimeProcessingDatabase(): void
    {
        $db = $this->setupDatabase();
        $date = new DateTime("2011-08-30");

        $db->application()->insert([
            "id" => 5,
            "author_id" => 11,
            "title" => $date,
            "slogan" => new Literal("?", $date),
        ]);
        $application = $db->application()->where("title = ?", $date)->fetch();

        $this->assertEquals($application['slogan'], '2011-08-30 00:00:00');
        $application->delete();
    }
}
