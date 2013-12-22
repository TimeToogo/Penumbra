<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\JoinedRow;

interface IConnection {
    public function SetExpressionCompiler(IExpressionCompiler $ExpressionCompiler);
    public function SetIdentifierEscaper(IIdentifierEscaper $IdentifierEscaper);
    public function SetRequestCompiler(IRequestCompiler $RequestCompiler);
    public function SetPredicateCompiler(IPredicateCompiler $PredicateCompiler);
    
    public function BeginTransaction();
    public function IsInTransaction();
    public function CommitTransaction();
    public function RollbackTransaction();
    
    public function GetLastInsertIncrement();
    
    public function Escape($Value, $ParameterType);
    
    /**
     * @return QueryBuilder
     */
    public function QueryBuilder(Bindings $Bindings = null);
    
    /**
     * @return IQuery
     */
    public function Prepare($QueryString, Bindings $Bindings = null);
    public function Execute($QueryString, Bindings $Bindings = null);
    public function FetchValue($QueryString, Bindings $Bindings = null);
    
    /**
     * @return Relational\Row[]
     */
    public function LoadRows(Relational\Table $Table, IQuery $Query);
    
    /**
     * @return JoinedRow[]
     */
    public function LoadJoinedRows(array $JoinedTables, IQuery $Query);
    
    public function Disconnect();
}

?>
