<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;

abstract class Connection implements IConnection {
    /**
     * @var IIdentifierEscaper 
     */
    protected $IdentifierEscaper;
    /**
     * @var IExpressionCompiler 
     */
    protected $ExpressionCompiler;
    /**
     * @var ISelectCompiler 
     */
    protected $QueryCompiler;
    
    final public function SetExpressionCompiler(IExpressionCompiler $ExpressionCompiler) {
        $this->ExpressionCompiler = $ExpressionCompiler;
    }
        
    final public function SetIdentifierEscaper(IIdentifierEscaper $IdentifierEscaper) {
        $this->IdentifierEscaper = $IdentifierEscaper;
    }
    
    final public function SetQueryCompiler(IQueryCompiler $QueryCompiler) {
        $this->QueryCompiler = $QueryCompiler;
    }
    
    final public function SetProcedureCompiler(IUpdateCompiler $ProcedureCompiler) {
        $this->ProcedureCompiler = $ProcedureCompiler;
    }
}

?>
