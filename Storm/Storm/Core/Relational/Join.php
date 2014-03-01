<?php

namespace Storm\Core\Relational;

/**
 * The join represents a join constraint for a table
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Join {
    private $JoinType;
    
    /**
     * @var ITable 
     */
    private $Table = [];
    
    /**
     * @var Expression 
     */
    private $JoinPredicateExpression;
    
    public function __construct($JoinType, ITable $Table, Expression $JoinPredicateExpression) {
        if(!JoinType::IsValid($JoinType)) {
            throw new RelationalException('The supplied join type is not valid: %s given', $JoinType);
        }
        $this->JoinType = $JoinType;
        $this->Table = $Table;
        $this->JoinPredicateExpression = $JoinPredicateExpression;
    }
    
    public function GetJoinType() {
        return $this->JoinType;
    }

    /**
     * @return ITable 
     */
    public function GetTable() {
        return $this->Table;
    }

    /**
     * @return Expression[] 
     */
    public function GetJoinPredicateExpression() {
        return $this->JoinPredicateExpression;
    }
}

?>