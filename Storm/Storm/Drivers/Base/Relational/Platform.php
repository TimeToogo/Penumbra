<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core;

class Platform implements IPlatform {
    /**
     * @var Queries\IConnection 
     */
    private $Connection;
    private $ColumnSet;
    private $KeyGeneratorSet;
    private $ExpressionCompiler;
    private $CriterionCompiler;
    private $QueryCompiler;
    private $IdentifierEscaper;
    private $DatabaseSyncer;
    private $TransactionCommiter;
    
    public function __construct(
            Columns\IColumnSet $ColumnSet,
            PrimaryKeys\IKeyGeneratorSet $KeyGeneratorSet,
            Queries\IExpressionCompiler $ExpressionCompiler, 
            Queries\ICriterionCompiler $CriterionCompiler, 
            Queries\IQueryCompiler $QueryCompiler, 
            Queries\IIdentifierEscaper $IdentifierEscaper,
            Syncing\IDatabaseSyncer $DatabaseSyncer, 
            Queries\ITransactionCommiter $TransactionCommiter) {
        $this->ColumnSet = $ColumnSet;
        $this->KeyGeneratorSet = $KeyGeneratorSet;
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->CriterionCompiler = $CriterionCompiler;
        $this->QueryCompiler = $QueryCompiler;
        $this->ProcedureCompiler = $ProcedureCompiler;
        $this->IdentifierEscaper = $IdentifierEscaper;
        $this->DatabaseSyncer = $DatabaseSyncer;
        $this->TransactionCommiter = $TransactionCommiter;
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
        $this->Connection->SetRequestCompiler($this->QueryCompiler);
        $this->Connection->SetProcedureCompiler($this->ProcedureCompiler);
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
    
    final public function GetQueryCompiler() {
        return $this->QueryCompiler;
    }
    
    final public function GetIdentifierEscaper() {
        return $this->IdentifierEscaper;
    }

    final public function GetDatabaseSyncer() {
        return $this->DatabaseSyncer;
    }

    final public function GetTransactionCommiter() {
        return $this->TransactionCommiter;
    }
    
    final public function Sync(Database $Database) {
        $this->VerifyConnection(__METHOD__);
        
        return $this->DatabaseSyncer->Sync($this->Connection, $Database);
    }
    
    final public function LoadResultRowData(Core\Relational\ResultSetSelect $Select) {
        $Query = $this->ExecuteSelect(__METHOD__, $Select);
        
        return $Query->FetchAll();
    }
    
    public function LoadData(Core\Relational\DataSelect $Select) {
        $Query = $this->ExecuteSelect(__METHOD__, $Select);
        
        return $Query->FetchAll();
    }
    
    final public function LoadExists(Core\Relational\ExistsSelect $Select) {
        $Value = $this->ExecuteSelect(__METHOD__, $Select)->FetchValue();
        
        return (bool)$Value;
    }
    
    /**
     * @return IQuery
     */
    private function ExecuteSelect($Method, Core\Relational\Select $Select) {
        $this->VerifyConnection($Method);
        
        $QueryBuilder = $this->Connection->QueryBuilder();
        $this->QueryCompiler->AppendSelect($QueryBuilder, $Select);
        
        return $QueryBuilder->Build()->Execute();
    }

    final public function Commit(
            array $TablesOrderedByPersistingDependency, 
            array $TablesOrderedByDiscardingDependency, 
            Core\Relational\Transaction $Transaction) {
        $this->VerifyConnection(__METHOD__);
        
        return $this->TransactionCommiter->Commit(
                $this->Connection, 
                $TablesOrderedByPersistingDependency, 
                $TablesOrderedByDiscardingDependency, 
                $Transaction);
    }
}

?>