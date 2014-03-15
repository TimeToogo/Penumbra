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
     * @var ResultSetSources
     */
    private $Sources;
    
    /**
     * @var Criteria
     */
    private $Criteria;
    
    
    public function __construct(ResultSetSources $Sources, Criteria $Criteria) {
        $this->Sources = $Sources;
        $this->Criteria = $Criteria;
    }
    
    /**
     * @return ResultSetSources
     */
    final public function GetSources() {
        return $this->Sources;
    }

    /**
     * @return Criteria
     */
    final public function GetCriteria() {
        return $this->Criteria;
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
}

?>