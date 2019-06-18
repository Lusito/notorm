<?php
use Lusito\NotORM\DB;

final class TransactionTest extends TestCase
{
    public function testTransactionsDB(): void
    {
        $this->setupDB();

        DB::beginTransaction();
        DB::tag()->insert(["id" => 99, "name" => "Test"]);
        $this->assertEquals(DB::getRow('tag', 99) . '', '99');
        DB::rollbackTransaction();
        $this->assertEquals(DB::getRow('tag', 99), false);
    }

    public function testTransactionsDatabase(): void
    {
        $db = $this->setupDatabase();

        $db->transaction = "BEGIN";
        $db->tag()->insert(["id" => 99, "name" => "Test"]);
        $this->assertEquals($db->tag[99] . '', '99');
        $db->transaction = "ROLLBACK";
        $this->assertEquals($db->tag[99], false);
    }
}
