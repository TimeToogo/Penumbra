<?php

namespace Storm\Tests\Integration;

use \Storm\Tests\StormTestCase;
use \Storm\Api;
use \Storm\Drivers\Base\Object\Properties\Proxies;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\IPlatform;
use \Storm\Drivers\Platforms;

abstract class ORMTestCase extends StormTestCase {
    /**
     * @var IConnection 
     */
    private static $Connection = null;
    
    /**
     * @var IPlatform 
     */
    private static $Platform = null;
    
    /**
     * @var Proxies\IProxyGenerator
     */
    private static $ProxyGenerator = null;
    
    private static function GetConnection() {
        if (self::$Connection === null) {
            self::$Connection = \Storm\Drivers\Platforms\PDO\Connection::Connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        }
        
        return self::$Connection;
    }
    
    private static function GetPlatform() {
        if (self::$Platform === null) {
            self::$Platform = new Platforms\Mysql\Platform(false);
        }
        
        return self::$Platform; 
    }
    
    
    private static function GetProxyGenerator() {
        if (self::$ProxyGenerator === null) {
            self::$ProxyGenerator = new Proxies\EvalProxyGenerator(__NAMESPACE__ . '\\' . 'Proxies');
        }
        
        return self::$ProxyGenerator;
    }
    
    /**
     * @var Api\Base\Storm
     */
    private $Storm;
    
    /**
     * @return callabe
     */
    protected abstract function GetDomainDatabaseMapFactory(IPlatform $Platform);
    
    /**
     * @return Api\Base\Storm
     */
    final protected function GetStorm() {
        if($this->Storm === null) {
            $Configuration = new Api\DefaultConfiguration(
                    $this->GetDomainDatabaseMapFactory(self::GetPlatform()),
                    self::GetConnection(),
                    self::GetProxyGenerator());

            $this->Storm = $Configuration->Storm();
        }
        
        return $this->Storm;
    }
}

?>