<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullPlatform implements Relational\IPlatform {
    private $Connection;
    private $ColumnSet;
    private $KeyGeneratorSet;
    private $IdentifierEscaper;
    private $DatabaseSyncer;
    private $QueryExecuter;
    
    public function __construct() {
        $this->Connection = new NullConnection();
        $this->ColumnSet = new NullColumnSet();
        $this->KeyGeneratorSet = new NullKeyGeneratorSet();
        $this->IdentifierEscaper = new NullIdentifierEscaper();
        $this->DatabaseSyncer = new NullDatabaseSyncer();
        $this->QueryExecuter = new NullQueryExecutor();
    }

    
    public function GetConnection() {
        return $this->Connection;
    }
    
    public function GetColumnSet() {
        return $this->ColumnSet;
    }
    
    public function GetKeyGeneratorSet() {
        return $this->KeyGeneratorSet;
    }
    
    public function GetIdentifierEscaper() {
        return $this->IdentifierEscaper;
    }

    public function GetDatabaseSyncer() {
        return $this->DatabaseSyncer;
    }

    public function GetQueryExecutor() {
        return $this->QueryExecuter;
    }

    public function GetExpressionCompiler() {
        
    }

    public function GetExpressionMapper() {
        
    }

    public function GetPredicateCompiler() {
        
    }

    public function GetRequestCompiler() {
        
    }

}

?>