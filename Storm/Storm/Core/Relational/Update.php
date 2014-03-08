<?php

namespace Storm\Core\Relational;

use \Storm\Core\Relational\Expressions;

/**
 * The procedure represents a set of changes to columns values to
 * a variable amount of rows defined by a criterion.
 * This can be thought of an UPDATE statement
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Update {
    
    /**
     * The update expressions.
     * 
     * @var Expression[] 
     */
    private $Expressions;
    
    /**
     * @var Criterion
     */
    private $Criterion;
    
    public function __construct(Criterion $Criterion = null) {
        $this->Criterion = $Criterion ?: $Criterion;
    }
    
    /**
     * @return Expression[]
     */
    final public function GetExpressions() {
        return $this->Expressions;
    }
    
    final public function AddExpression(Expression $Expression) {
        $this->Expressions[] = $Expression;
    }
    
    final public function AddExpressions(array $Expressions) {
        array_walk($Expressions, [$this, 'AddExpression']);
    }
    
    /**
     * @return Criterion
     */
    final public function GetCriterion() {
        return $this->Criterion;
    }
    
    /**
     * Set the procedure's criterion.
     * 
     * @param Criterion $Criterion The criterion to set
     */
    final public function SetCriterion(Criterion $Criterion) {
        $this->Criterion = $Criterion;
    }
}

?>