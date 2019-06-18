<?php

use Lusito\NotORM\ConfigBuilder;
use Lusito\NotORM\Database;

require_once(__DIR__ . '/../vendor/autoload.php');

abstract class TestCase extends \PHPUnit\Framework\TestCase {
    protected function setupDatabase($build=null) {
	    $builder = new ConfigBuilder('sqlite::memory:');
        $builder
            ->pdoAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING)
            ->pdoAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

        if ($build)
            $build($builder);
        $config = $builder->build();
        $db = new Database($config);
        $config->connection->exec(file_get_contents(__dir__ . '/software.sql'));
        return $db;
    }
}
