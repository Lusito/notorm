<?php

use Lusito\NotORM\Literal;

final class LiteralTest extends TestCase
{
    public function testLiteralValueWithParameters(): void
    {
        $db = $this->setupDatabase();
        $result = [];
        foreach ($db->author()->select(new Literal("? + ?", 1, 2))->fetch() as $val)
            $result []= "$val";

        $this->assertEquals($result, ['3']);
    }
}
