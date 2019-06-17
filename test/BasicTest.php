<?php

final class BasicTest extends TestCase
{
    public function testBasicOperations(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->application() as $application) {
            $tags = [];
            foreach ($application->application_tag() as $application_tag)
                $tags []= $application_tag->tag["name"];
            $result []= [
                $application['title'],
                $application->author["name"],
                $tags
            ];
        }

        $this->assertEquals($result, [
            ['Adminer', 'Jakub Vrana', ['PHP', 'MySQL']],
            ['JUSH', 'Jakub Vrana', ['JavaScript']],
            ['Nette', 'David Grudl', ['PHP']],
            ['Dibi', 'David Grudl', ['PHP', 'MySQL']]
        ]);
    }
}
