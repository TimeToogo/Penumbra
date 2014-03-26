<?php

namespace Penumbra\Core\Relational;

/**
 * The join represents a join constraint for a table
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Join {
    private $JoinType;
    
    /**
     * @var IResultSetSource 
     */
    private $Source = [];
    
    /**
     * @var Expression 
     */
    private $JoinPredicateExpression;
    
    public function __construct($JoinType, IResultSetSource $Source, Expression $JoinPredicateExpression) {
        if(!JoinType::IsValid($JoinType)) {
            throw new RelationalException('The supplied join type is not valid: %s given', $JoinType);
        }
        $this->JoinType = $JoinType;
        $this->Source = $Source;
        $this->JoinPredicateExpression = $JoinPredicateExpression;
    }
    
    public function GetJoinType() {
        return $this->JoinType;
    }

    /**
     * @return IResultSetSource 
     */
    public function GetSource() {
        return $this->Source;
    }

    /**
     * @return Expression
     */
    public function GetJoinPredicateExpression() {
        return $this->JoinPredicateExpression;
    }
}

?>