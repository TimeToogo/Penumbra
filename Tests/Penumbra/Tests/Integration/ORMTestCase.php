<?php

namespace Penumbra\Tests\Integration;

use \Penumbra\Tests\PenumbraTestCase;
use \Penumbra\Api;
use \Penumbra\Drivers\Base\Object\Properties\Proxies;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\IPlatform;
use \Penumbra\Drivers\Platforms;

abstract class ORMTestCase extends PenumbraTestCase {
    /**
     * @var IConnection 
     */
    private static $Connection = null;
    /**
     * @var IConnection 
     */
    private static $DBName = null;
    
    /**
     * @var IPlatform 
     */
    private static $Platform = null;
    
    /**
     * @var Proxies\IProxyGenerator
     */
    private static $ProxyGenerator = null;
    
    /**
     * @return IConnection
     */
    protected static function GetConnection() {
        if (self::$Connection === null) {
            self::$Connection = \Penumbra\Drivers\Platforms\PDO\Connection::Connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            self::$DBName = $GLOBALS['DB_DBNAME'];
        }
        
        return self::$Connection;
    }
    
    /**
     * @return IConnection
     */
    protected static function GetDBName() {
        self::GetConnection();        
        return self::$DBName;
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
     * @var Api\Base\ORM
     */
    private $Penumbra;
    
    /**
     * @return callabe
     */
    protected abstract function GetDomainDatabaseMapFactory(IPlatform $Platform);
    
    /**
     * @return Api\Base\ORM
     */
    final protected function GetPenumbra() {
        if($this->Penumbra === null) {
            $Configuration = new Api\DefaultConfiguration(
                    $this->GetDomainDatabaseMapFactory(self::GetPlatform()),
                    self::GetConnection(),
                    self::GetProxyGenerator());

            $this->Penumbra = $Configuration->BuildORM();
        }
        
        return $this->Penumbra;
    }
    
    /**
     * @return Api\Base\EntityManager
     */
    final protected function GetRepository($EntityType) {
        return $this->GetPenumbra()->GetEntityManger($EntityType);
    }
}

?>