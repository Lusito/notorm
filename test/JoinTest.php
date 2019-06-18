<?php
use Lusito\NotORM\DB;

final class JoinTest extends TestCase
{
    public function testOrderFromOtherTableDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::application()->order("author.name, title") as $application)
            $result []= [$application->author["name"], $application['title']];

        $this->assertEquals($result, [
            ['David Grudl', 'Dibi'],
            ['David Grudl', 'Nette'],
            ['Jakub Vrana', 'Adminer'],
            ['Jakub Vrana', 'JUSH']
        ]);

        $result = [];
        foreach (DB::application_tag("application.author.name", "Jakub Vrana")->group("application_tag.tag_id") as $application_tag)
            $result []= $application_tag->tag["name"];

        $this->assertEquals($result, [
            'PHP',
            'MySQL',
            'JavaScript'
        ]);
    }

    public function testOrderFromOtherTableDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application()->order("author.name, title") as $application)
            $result []= [$application->author["name"], $application['title']];

        $this->assertEquals($result, [
            ['David Grudl', 'Dibi'],
            ['David Grudl', 'Nette'],
            ['Jakub Vrana', 'Adminer'],
            ['Jakub Vrana', 'JUSH']
        ]);

        $result = [];
        foreach ($db->application_tag("application.author.name", "Jakub Vrana")->group("application_tag.tag_id") as $application_tag)
            $result []= $application_tag->tag["name"];

        $this->assertEquals($result, [
            'PHP',
            'MySQL',
            'JavaScript'
        ]);
    }
}
