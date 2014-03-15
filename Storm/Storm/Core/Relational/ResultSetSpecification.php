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
class ResultSetSpecification {
    
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
}

?>