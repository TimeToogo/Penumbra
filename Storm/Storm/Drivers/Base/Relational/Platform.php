<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core;

class Platform implements IPlatform {
    private $Connection;
    private $ExpressionMapper;
    private $ColumnSet;
    private $KeyGeneratorSet;
    private $ExpressionCompiler;
    private $CriterionCompiler;
    private $IdentifierEscaper;
    private $DatabaseSyncer;
    private $QueryExecutor;
    
    public function __construct(
            Queries\IConnection $Connection,
            Expressions\IExpressionMapper $ExpressionMapper,
            Columns\IColumnSet $ColumnSet,
            PrimaryKeys\IKeyGeneratorSet $KeyGeneratorSet,
            Queries\IExpressionCompiler $ExpressionCompiler, 
            Queries\ICriterionCompiler $CriterionCompiler, 
            Queries\IIdentifierEscaper $IdentifierEscaper,
            Syncing\IDatabaseSyncer $DatabaseSyncer, 
            Queries\IQueryExecutor $QueryExecutor) {
        $this->Connection = $Connection;
        $this->ExpressionMapper = $ExpressionMapper;
        $this->ColumnSet = $ColumnSet;
        $this->KeyGeneratorSet = $KeyGeneratorSet;
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->CriterionCompiler = $CriterionCompiler;
        $this->IdentifierEscaper = $IdentifierEscaper;
        $this->DatabaseSyncer = $DatabaseSyncer;
        $this->QueryExecutor = $QueryExecutor;
        
        $this->Connection->SetExpressionCompiler($this->ExpressionCompiler);
        $this->Connection->SetCriterionCompiler($this->CriterionCompiler);
        $this->Connection->SetIdentifierEscaper($this->IdentifierEscaper);
    }
    
    
    final public function GetConnection() {
        return $this->Connection;
    }
    
    final public function GetExpressionMapper() {
        return $this->ExpressionMapper;
    }
    
    final public function GetColumnSet() {
        return $this->ColumnSet;
    }
    
    final public function GetKeyGeneratorSet() {
        return $this->KeyGeneratorSet;
    }
    
    final public function GetExpressionCompiler() {
        return $this->ExpressionCompiler;
    }
    
    final public function GetCriterionCompiler() {
        return $this->CriterionCompiler;
    }
    
    final public function GetIdentifierEscaper() {
        return $this->IdentifierEscaper;
    }

    final public function GetDatabaseSyncer() {
        return $this->DatabaseSyncer;
    }

    final public function GetQueryExecutor() {
        return $this->QueryExecutor;
    }
    
    final public function Sync(Database $Database) {
        return $this->DatabaseSyncer->Sync($this->Connection, $Database);
    }
    
    final public function Select(Core\Relational\Request $Request) {
        return $this->QueryExecutor->Select($this->Connection, $Request);
    }

    final public function Commit(
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Core\Relational\Transaction $Transaction) {
        
        return $this->QueryExecutor->Commit(
                $this->Connection, 
                $TablesOrderedByPersistingDependency, 
                $TablesOrderedByDiscardingDependency, 
                $Transaction);
    }
}

?>