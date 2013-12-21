<?php

namespace Storm\Drivers\Base\Relational;

interface IPlatform {
    
    /**
     * @return Queries\IConnection
     */
    public function GetConnection();
    
    /**
     * @return Expressions\IExpressionMapper
     */
    public function GetExpressionMapper();
    
    /**
     * @return Columns\IColumnSet
     */
    public function GetColumnSet();
    
    /**
     * @return PrimaryKeys\IKeyGeneratorSet
     */
    public function GetKeyGeneratorSet();
    
    /**
     * @return Queries\IRequestCompiler
     */
    public function GetRequestCompiler();
    
    /**
     * @return Queries\IPredicateCompiler
     */
    public function GetPredicateCompiler();
    
    /**
     * @return Queries\IIdentifierEscaper
     */
    public function GetIdentifierEscaper();
    
    /**
     * @return Syncing\IDatabaseSyncer
     */
    public function GetDatabaseSyncer();
    
    /**
     * @return Queries\IQueryExecutor
     */
    public function GetQueryExecutor();
    
    /**
     * @return Relations\IToOneReviver
     */
    public function GetToOneRelationReviver();
    
    /**
     * @return Relations\IToManyReviver
     */
    public function GetToManyRelationReviver();
}

?>