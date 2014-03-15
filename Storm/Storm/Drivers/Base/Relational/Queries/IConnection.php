<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;

interface IConnection {
    public function SetExpressionCompiler(IExpressionCompiler $ExpressionCompiler);
    public function SetQueryCompiler(IQueryCompiler $QueryCompiler);
    public function SetIdentifierEscaper(IIdentifierEscaper $IdentifierEscaper);
    
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
    /**
     * @return IQuery
     */
    public function Execute($QueryString, Bindings $Bindings = null);
    public function FetchValue($QueryString, Bindings $Bindings = null);
        
    public function Disconnect();
}

?>
