<?php

namespace Storm\Core\Relational;

/**
 * This select represents custom data to load
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DataSelect extends Select {
    private $AliasExpressionMap = [];
    
    public function __construct(array $AliasExpressionMap, ResultSetSources $SelectSources, Criteria $Criteria) {
        parent::__construct($SelectSources, $Criteria);
        
        foreach ($AliasExpressionMap as $Alias => $DataExpression) {
            $this->AddData($Alias, $DataExpression);
        }
    }

    final public function GetSelectType() {
        return SelectType::Data;
    }
    
    /**
     * @return array<string, Expression>
     */
    public function GetAliasExpressionMap() {
        return $this->AliasExpressionMap;
    }
    
    public function AddData($Alias, Expression $DataExpression) {
        $this->AliasExpressionMap[$Alias] = $DataExpression;
    }
}

?>