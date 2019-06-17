<?php

final class ViaTest extends TestCase
{
    public function testVia(): void
    {
        $db = $this->setupDatabase();
        $result = [];

        foreach ($db->author() as $author) {
            $applications = $author->application()->via("maintainer_id");
            foreach ($applications as $application)
                $result []= [$author['name'], $application['title']];
        }

        $this->assertEquals($result, [['Jakub Vrana', 'Adminer'], ['David Grudl', 'Nette'], ['David Grudl', 'Dibi']]);
        $this->assertEquals("$applications", 'SELECT * FROM application WHERE (application.maintainer_id IN (11, 12))');
    }
}
