<?php

namespace Storm\Drivers\Base\Relational;

class Platform implements IPlatform {
    private $Connection;
    private $ExpressionMapper;
    private $ColumnSet;
    private $KeyGeneratorSet;
    private $RequestCompiler;
    private $PredicateCompiler;
    private $IdentifierEscaper;
    private $DatabaseSyncer;
    private $QueryExecutor;
    private $ToOneRelationReviver;
    private $ToManyRelationReviver;
    
    public function __construct(
            Queries\IConnection $Connection,
            Expressions\IExpressionMapper $ExpressionMapper,
            Columns\IColumnSet $ColumnSet,
            PrimaryKeys\IKeyGeneratorSet $KeyGeneratorSet,
            Queries\IRequestCompiler $RequestCompiler, 
            Queries\IPredicateCompiler $PredicateCompiler, 
            Queries\IIdentifierEscaper $IdentifierEscaper,
            Syncing\IDatabaseSyncer $DatabaseSyncer, 
            Queries\IQueryExecutor $QueryExecutor, 
            Relations\IToOneReviver $ToOneRelationReviver, 
            Relations\IToManyReviver $ToManyRelationReviver) {
        $this->Connection = $Connection;
        $this->ExpressionMapper = $ExpressionMapper;
        $this->ColumnSet = $ColumnSet;
        $this->KeyGeneratorSet = $KeyGeneratorSet;
        $this->RequestCompiler = $RequestCompiler;
        $this->PredicateCompiler = $PredicateCompiler;
        $this->IdentifierEscaper = $IdentifierEscaper;
        $this->Connection->SetRequestCompiler($RequestCompiler);
        $this->Connection->SetPredicateCompiler($PredicateCompiler);
        $this->Connection->SetIdentifierEscaper($IdentifierEscaper);
        $this->DatabaseSyncer = $DatabaseSyncer;
        $this->QueryExecutor = $QueryExecutor;
        $this->ToOneRelationReviver = $ToOneRelationReviver;
        $this->ToManyRelationReviver = $ToManyRelationReviver;
    }

    
    final public function GetConnection() {
        return $this->Connection;
    }
    
    public function GetExpressionMapper() {
        return $this->ExpressionMapper;
    }
    
    public function GetColumnSet() {
        return $this->ColumnSet;
    }
    
    public function GetKeyGeneratorSet() {
        return $this->KeyGeneratorSet;
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

    final public function GetToOneRelationReviver() {
        return $this->ToOneRelationReviver;
    }

    final public function GetToManyRelationReviver() {
        return $this->ToManyRelationReviver;
    }
}

?>