<?php

final class ExtendedTest extends TestCase
{
    /**
     * @group noSQLite
     * @group noOracle
     */
    public function testExtendedInsert(): void
    {
        $db = $this->setupDatabase();

        $result = [];
        $application = $db->application[3];
        $application->application_tag()->insert(["tag_id" => 22], ["tag_id" => 23]);
        foreach ($application->application_tag()->order("tag_id DESC") as $application_tag)
            $result []= [$application_tag['application_id'], $application_tag['tag_id']];
        $application->application_tag("tag_id", [22, 23])->delete();

        $this->assertEquals($result, [[3, 23], [3, 22], [3, 21]]);
    }
}
