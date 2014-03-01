<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core;

class Platform implements IPlatform {
    private $Connection;
    private $ExpressionConverter;
    private $ColumnSet;
    private $KeyGeneratorSet;
    private $ExpressionCompiler;
    private $CriterionCompiler;
    private $IdentifierEscaper;
    private $DatabaseSyncer;
    private $QueryExecutor;
    
    public function __construct(
            Expressions\Converters\IExpressionConverter $ExpressionConverter,
            Columns\IColumnSet $ColumnSet,
            PrimaryKeys\IKeyGeneratorSet $KeyGeneratorSet,
            Queries\IExpressionCompiler $ExpressionCompiler, 
            Queries\ICriterionCompiler $CriterionCompiler, 
            Queries\IIdentifierEscaper $IdentifierEscaper,
            Syncing\IDatabaseSyncer $DatabaseSyncer, 
            Queries\IQueryExecutor $QueryExecutor) {
        $this->ExpressionConverter = $ExpressionConverter;
        $this->ColumnSet = $ColumnSet;
        $this->KeyGeneratorSet = $KeyGeneratorSet;
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->CriterionCompiler = $CriterionCompiler;
        $this->IdentifierEscaper = $IdentifierEscaper;
        $this->DatabaseSyncer = $DatabaseSyncer;
        $this->QueryExecutor = $QueryExecutor;
    }
    
    
    final public function GetConnection() {
        return $this->Connection;
    }
    
    public function HasConnection() {
        return $this->Connection !== null;
    }
    
    public function SetConnection(Queries\IConnection $Connection) {
        $this->Connection = $Connection;
        $this->Connection->SetExpressionCompiler($this->ExpressionCompiler);
        $this->Connection->SetCriterionCompiler($this->CriterionCompiler);
        $this->Connection->SetIdentifierEscaper($this->IdentifierEscaper);
        $this->OnSetConnection($Connection);
    }
    protected function OnSetConnection(Queries\IConnection $Connection) { }
    
    final protected function VerifyConnection($Method) {
        if(!($this->Connection instanceof Queries\IConnection)) {
            throw new Core\Relational\RelationalException(
                    'Call to %s requires connection to be set',
                    $Method);
        }
    }
    
    final public function __sleep() {
        return array_diff(array_keys((array)$this), ['Connection']);
    }
    
    final public function GetExpressionConverter() {
        return $this->ExpressionConverter;
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
        $this->VerifyConnection(__METHOD__);
        
        return $this->DatabaseSyncer->Sync($this->Connection, $Database);
    }
    
    final public function Select(Core\Relational\Request $Request) {
        $this->VerifyConnection(__METHOD__);
        
        return $this->QueryExecutor->Select($this->Connection, $Request);
    }

    final public function Commit(
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Core\Relational\Transaction $Transaction) {
        $this->VerifyConnection(__METHOD__);
        
        return $this->QueryExecutor->Commit(
                $this->Connection, 
                $TablesOrderedByPersistingDependency, 
                $TablesOrderedByDiscardingDependency, 
                $Transaction);
    }
}

?>