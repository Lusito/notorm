<?php
use Lusito\NotORM\DB;
use Lusito\NotORM\Literal;

final class LiteralTest extends TestCase
{
    public function testLiteralValueWithParametersDB(): void
    {
        $this->setupDB();
        $result = [];
        foreach (DB::author()->select(new Literal("? + ?", 1, 2))->fetch() as $val)
            $result []= "$val";

        $this->assertEquals($result, ['3']);
    }

    public function testLiteralValueWithParametersDatabase(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->author()->select(new Literal("? + ?", 1, 2))->fetch() as $val)
            $result []= "$val";

        $this->assertEquals($result, ['3']);
    }
}
