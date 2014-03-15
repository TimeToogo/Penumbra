<?php

namespace Storm\Core\Relational;

/**
 * The base class for a query on result set specification
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Query {    
     /**
     * @var ResultSetSpecification
     */
    protected $ResultSetSpecification;
    
     /**
     * @var ResultSetSources
     */
    protected $Sources;
    
     /**
     * @var Criteria
     */
    protected $Criteria;
    
    
    public function __construct(ResultSetSpecification $ResultSetSpecification) {
        $this->ResultSetSpecification = $ResultSetSpecification;
        $this->Sources = $ResultSetSpecification->GetSources();
        $this->Criteria = $ResultSetSpecification->GetCriteria();
    }
    
    /**
     * @return ResultSetSpecification
     */
    final public function GetResultSetSpecification() {
        return $this->ResultSetSpecification;
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