<?php

use Lusito\NotORM\DB;
use Lusito\NotORM\ConfigBuilder;
use Lusito\NotORM\Database;

require_once(__DIR__ . '/../vendor/autoload.php');

abstract class TestCase extends \PHPUnit\Framework\TestCase {
    private function setupConfig($build=null) {
	    $builder = new ConfigBuilder('sqlite::memory:');
        $builder
            ->pdoAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING)
            ->pdoAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

        if ($build)
            $build($builder);
        $config = $builder->build();
        $config->connection->exec(file_get_contents(__dir__ . '/software.sql'));
        return $config;
    }

    protected function setupDatabase($build=null) {
        return new Database($this->setupConfig($build));
    }

    protected function setupDB($build=null) {
        DB::setConfig($this->setupConfig($build));
    }

    // Weird ordering, too lazy to flip all calls
    public static function assertEquals($actual, $expected, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false): void {
        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }
}
