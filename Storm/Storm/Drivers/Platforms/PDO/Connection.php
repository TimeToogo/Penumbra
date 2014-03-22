<?php

namespace Storm\Drivers\Platforms\PDO;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;

class Connection extends Queries\Connection {    
    private $PDO;
    
    public function __construct(\PDO $Connection) {
        $Connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $Connection->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
        $Connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $Connection->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
        $Connection->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        $this->PDO = $Connection;
    }
    
    /**
     * @return \PDO
     */
    public function GetPDO() {
        return $this->PDO;
    }
    
    /**
     * @return Connection
     */
    public static function Connect($DSN, $Username = null, $Password = null, array $DriverOptions = null) {
        return new self(new \PDO($DSN, $Username, $Password, $DriverOptions));
    }
    
    public function BeginTransaction() {
        return $this->PDO->beginTransaction();
    }
    
    public function CommitTransaction() {
        return $this->PDO->commit();
    }
    
    public function RollbackTransaction() {
        return $this->PDO->rollBack();
    }

    public function IsInTransaction() {
        return $this->PDO->inTransaction();
    }
    
    public function GetLastInsertIncrement() {
        return $this->PDO->lastInsertId();
    }

    public function Disconnect() {
        $this->PDO = null;
    }
    
    public function Escape($Value, $ParameterType) {
        //PDO puts quotes around integers
        if($ParameterType == Queries\ParameterType::Integer) {
            return (string)((int)$Value);
        }
        
        $PDOParameterType = PDOParameterType::MapParameterType($ParameterType);
        return $this->PDO->quote($Value, $PDOParameterType);
    }
    
    public function QueryBuilder(Queries\Bindings $Bindings = null) {
        if($Bindings === null) {
            $Bindings = new Queries\Bindings();
        }        
        return new Queries\QueryBuilder($this, '?', $Bindings, 
                $this->ExpressionCompiler, 
                $this->QueryCompiler,  
                $this->IdentifierEscaper);
    }

    public function Execute($QueryString, Queries\Bindings $Bindings = null) {
        return (new Query($this->PDO->prepare($QueryString), $Bindings))->Execute();
    }

    public function FetchValue($QueryString, Queries\Bindings $Bindings = null) {
        $Row = (new Query($this->PDO->prepare($QueryString), $Bindings))->Execute()->FetchRow();
        if(empty($Row))
            return null;
        else
            return reset($Row);
    }
    
    public function Prepare($QueryString, Queries\Bindings $Bindings = null) {
        return new Query($this->PDO->prepare($QueryString), $Bindings);
    }
}

?>