<?php

namespace Storm\Drivers\Base\Relational;

class Platform implements IPlatform {
    private $Connection;
    private $ExpressionMapper;
    private $ColumnSet;
    private $KeyGeneratorSet;
    private $ExpressionCompiler;
    private $RequestCompiler;
    private $PredicateCompiler;
    private $IdentifierEscaper;
    private $DatabaseSyncer;
    private $QueryExecutor;
    
    public function __construct(
            Queries\IConnection $Connection,
            Expressions\IExpressionMapper $ExpressionMapper,
            Columns\IColumnSet $ColumnSet,
            PrimaryKeys\IKeyGeneratorSet $KeyGeneratorSet,
            Queries\IExpressionCompiler $ExpressionCompiler, 
            Queries\IRequestCompiler $RequestCompiler, 
            Queries\IPredicateCompiler $PredicateCompiler, 
            Queries\IIdentifierEscaper $IdentifierEscaper,
            Syncing\IDatabaseSyncer $DatabaseSyncer, 
            Queries\IQueryExecutor $QueryExecutor) {
        $this->Connection = $Connection;
        $this->ExpressionMapper = $ExpressionMapper;
        $this->ColumnSet = $ColumnSet;
        $this->KeyGeneratorSet = $KeyGeneratorSet;
        $this->ExpressionCompiler = $ExpressionCompiler;
        $this->RequestCompiler = $RequestCompiler;
        $this->PredicateCompiler = $PredicateCompiler;
        $this->IdentifierEscaper = $IdentifierEscaper;
        $this->Connection->SetExpressionCompiler($ExpressionCompiler);
        $this->Connection->SetRequestCompiler($RequestCompiler);
        $this->Connection->SetPredicateCompiler($PredicateCompiler);
        $this->Connection->SetIdentifierEscaper($IdentifierEscaper);
        $this->DatabaseSyncer = $DatabaseSyncer;
        $this->QueryExecutor = $QueryExecutor;
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
    
    final public function GetRequestCompiler() {
        return $this->RequestCompiler;
    }
    
    final public function GetPredicateCompiler() {
        return $this->PredicateCompiler;
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
}

?>