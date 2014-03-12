<?php

namespace Storm\Core\Relational;

use \Storm\Core\Relational\Expressions;

/**
 * The procedure represents a set of changes to columns values to
 * a variable amount of rows defined by a criteria.
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
     * @var Criteria
     */
    private $Criteria;
    
    public function __construct(Criteria $Criteria = null) {
        $this->Criteria = $Criteria ?: $Criteria;
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
     * @return Criteria
     */
    final public function GetCriteria() {
        return $this->Criteria;
    }
    
    /**
     * Set the procedure's criteria.
     * 
     * @param Criteria $Criteria The criteria to set
     */
    final public function SetCriteria(Criteria $Criteria) {
        $this->Criteria = $Criteria;
    }
}

?>