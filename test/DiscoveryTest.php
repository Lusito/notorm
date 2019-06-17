<?php
use Lusito\NotORM\Structure\DiscoveryStructure;

final class DiscoveryTest extends TestCase
{
    public function testDiscovery(): void
    {
        $db = $this->setupDatabase(function($builder) {
            $builder->structure(new DiscoveryStructure());
        });
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
