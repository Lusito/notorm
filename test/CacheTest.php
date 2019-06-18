<?php
use Lusito\NotORM\DB;
use Lusito\NotORM\Cache\SessionCache;

final class CacheTest extends TestCase
{
    public function testSessionCacheDB(): void
    {
        $_GLOBALS['_SESSION'] = []; // not session_start() - headers already sent

        $this->setupDB(function($builder) {
            $builder->cache(new SessionCache());
        });
        
        $applications = DB::application();
        $application = $applications->fetch();
        $application["title"];
        $application->author["name"];
        $this->assertEquals("$applications", 'SELECT * FROM application'); // get all columns with no cache
        $applications->__destruct();

        $applications = DB::application();
        $application = $applications->fetch();
        $this->assertEquals("$applications", 'SELECT id, title, author_id FROM application'); // get only title and author_id
        $application["slogan"]; // script changed and now we want also slogan
        $this->assertEquals("$applications", 'SELECT * FROM application'); // all columns must have been retrieved to get slogan
        $applications->__destruct();

        $applications = DB::application();
        $applications->fetch();
        $this->assertEquals("$applications", 'SELECT id, title, author_id, slogan FROM application'); // next time, get only title, author_id and slogan
    }

    public function testSessionCacheDatabase(): void
    {
        $_GLOBALS['_SESSION'] = []; // not session_start() - headers already sent

        $db = $this->setupDatabase(function($builder) {
            $builder->cache(new SessionCache());
        });
        
        $applications = $db->application();
        $application = $applications->fetch();
        $application["title"];
        $application->author["name"];
        $this->assertEquals("$applications", 'SELECT * FROM application'); // get all columns with no cache
        $applications->__destruct();

        $applications = $db->application();
        $application = $applications->fetch();
        $this->assertEquals("$applications", 'SELECT id, title, author_id FROM application'); // get only title and author_id
        $application["slogan"]; // script changed and now we want also slogan
        $this->assertEquals("$applications", 'SELECT * FROM application'); // all columns must have been retrieved to get slogan
        $applications->__destruct();

        $applications = $db->application();
        $applications->fetch();
        $this->assertEquals("$applications", 'SELECT id, title, author_id, slogan FROM application'); // next time, get only title, author_id and slogan
    }
}
