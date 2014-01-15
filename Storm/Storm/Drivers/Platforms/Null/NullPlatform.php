<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullPlatform implements Relational\IPlatform {
    
    public function Commit(array $TablesOrderedByPersistingDependency, array $TablesOrderedByDiscardingDependency, \Storm\Core\Relational\Transaction $Transaction) {
        
    }

    public function GetColumnSet() {
        
    }

    public function GetConnection() {
        
    }

    public function GetCriterionCompiler() {
        
    }

    public function GetDatabaseSyncer() {
        
    }

    public function GetExpressionCompiler() {
        
    }

    public function GetExpressionMapper() {
        
    }

    public function GetIdentifierEscaper() {
        
    }

    public function GetKeyGeneratorSet() {
        
    }

    public function GetQueryExecutor() {
        
    }

    public function Select(\Storm\Core\Relational\Request $Request) {
        
    }

    public function Sync(Relational\Database $Database) {
        
    }

}

?>