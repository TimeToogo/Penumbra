<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core;

/**
 * The platform contains all specific implementation related to
 * the underlying database.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IPlatform {
    
    /**
     * @return Queries\IConnection
     */
    public function GetConnection();
    
    /**
     * @return void
     */
    public function SetConnection(Queries\IConnection $Connection);
    
    /**
     * The connection should not be serialized.
     * @return string[]
     */
    public function __sleep();
    
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
     * @return Queries\IExpressionCompiler
     */
    public function GetExpressionCompiler();
    
    /**
     * @return Queries\ICriterionCompiler
     */
    public function GetCriterionCompiler();
    
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
        
    public function Sync(Database $Database);
    
    /**
     * @return array[]
     */
    public function Select(Core\Relational\Request $Request);
    
    public function Commit(
            array $TablesOrderedByPersistingDependency,
            array $TablesOrderedByDiscardingDependency,
            Core\Relational\Transaction $Transaction);
}

?>