<?php
use Lusito\NotORM\DB;

final class ExtendedTest extends TestCase
{
    /**
     * @group noSQLite
     * @group noOracle
     */
    public function testExtendedInsertDB(): void
    {
        $this->setupDB();

        $result = [];
        $application = DB::getRow('application', 3);
        $application->application_tag()->insert(["tag_id" => 22], ["tag_id" => 23]);
        foreach ($application->application_tag()->order("tag_id DESC") as $application_tag)
            $result []= [$application_tag['application_id'], $application_tag['tag_id']];
        $application->application_tag("tag_id", [22, 23])->delete();

        $this->assertEquals($result, [[3, 23], [3, 22], [3, 21]]);
    }

    /**
     * @group noSQLite
     * @group noOracle
     */
    public function testExtendedInsertDatabase(): void
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
