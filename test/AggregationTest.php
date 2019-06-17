<?php

final class AggregationTest extends TestCase
{
    public function testAggregationFunctions(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        $this->assertEquals($db->application()->count("*"), 4);
        foreach ($db->application() as $application) {
            $count = $application->application_tag()->count("*");
            $result []= [$application['title'],  $count];
        }

        $this->assertEquals($result, [['Adminer', 2], ['JUSH', 1], ['Nette', 1], ['Dibi', 2]]);
    }
}

