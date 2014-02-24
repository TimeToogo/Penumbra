<?php

namespace Storm\Tests\Integration;

use \Storm\Tests\Integration\ORMTestCase;
use \Storm\Api;
use \Storm\Drivers\Base\Object\Properties\Proxies;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\IPlatform;
use \Storm\Drivers\Platforms;

class BlogTestCase extends ORMTestCase {
    protected function GetDomainDatabaseMapFactory(IPlatform $Platform) {
        
    }
}

?>