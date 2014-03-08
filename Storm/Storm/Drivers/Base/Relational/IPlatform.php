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
     * @return boolean
     */
    public function HasConnection();
    
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
     * @return Queries\IQueryCompiler
     */
    public function GetQueryCompiler();
    
    /**
     * @return Queries\IIdentifierEscaper
     */
    public function GetIdentifierEscaper();
    
    /**
     * @return Syncing\IDatabaseSyncer
     */
    public function GetDatabaseSyncer();
    
    /**
     * @return Queries\ITransactionCommiter
     */
    public function GetTransactionCommiter();
        
    public function Sync(Database $Database);
    
    /**
     * @return ResultRow[]
     */
    public function Select(Core\Relational\Select $Select);
    
    public function Commit(
            array $TablesOrderedByPersistingDependency,
            array $TablesOrderedByDiscardingDependency,
            Core\Relational\Transaction $Transaction);
}

?>