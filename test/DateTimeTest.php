<?php
use Lusito\NotORM\Literal;

final class DateTimeTest extends TestCase
{
    public function testDateTimeProcessing(): void
    {
        $db = $this->setupDatabase();
        $date = new DateTime("2011-08-30");

        $db->application()->insert(array(
            "id" => 5,
            "author_id" => 11,
            "title" => $date,
            "slogan" => new Literal("?", $date),
        ));
        $application = $db->application()->where("title = ?", $date)->fetch();

        $this->assertEquals($application['slogan'], '2011-08-30 00:00:00');
        $application->delete();
    }
}
