<?php

namespace Storm\Core\Relational;

use \Storm\Core\Relational\Expressions;

class Procedure {
    private $Tables;
    private $Expressions;
    private $Criterion;
    
    public function __construct(array $Tables, Criterion $Criterion = null) {
        $this->Tables = $Tables;
        $this->Criterion = $Criterion ?: $Criterion;
    }
    
    /**
     * @return Table[]
     */
    public function GetTables() {
        return $this->Tables;
    }

    /**
     * @return Expressions\Expression[]
     */
    final public function GetExpressions() {
        return $this->Expressions;
    }
    final public function AddExpression(Expressions\Expression $Expression) {
        $this->Expressions[] = $Expression;
    }
    
    /**
     * @return Criterion
     */
    final public function GetCriterion() {
        return $this->Criterion;
    }
    
    final public function SetCriterion(Criterion $Criterion) {
        $this->Criterion = $Criterion;
    }
}

?>