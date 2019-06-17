<?php

final class TransactionTest extends TestCase
{
    public function testTransactions(): void
    {
        $db = $this->setupDatabase();

        $db->transaction = "BEGIN";
        $db->tag()->insert(array("id" => 99, "name" => "Test"));
        $this->assertEquals($db->tag[99] . '', '99');
        $db->transaction = "ROLLBACK";
        $this->assertEquals($db->tag[99], false);
    }
}
