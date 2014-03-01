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
class Procedure {
    /**
     * The tables to update.
     * 
     * @var ITable[]
     */
    private $Tables;
    
    /**
     * The set expressions.
     * 
     * @var Expression[] 
     */
    private $Expressions;
    
    /**
     * @var Criterion
     */
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
     * @return Expression[]
     */
    final public function GetExpressions() {
        return $this->Expressions;
    }
    final public function AddExpression(Expression $Expression) {
        $this->Expressions[] = $Expression;
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