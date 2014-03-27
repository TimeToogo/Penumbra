<?php

namespace Penumbra\Core\Relational;

/**
 * This select represents custom data to load
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DataSelect extends Select {
    private $AliasExpressionMap = [];
    
    public function __construct(array $AliasExpressionMap, ResultSetSpecification $ResultSetSpecification) {
        parent::__construct($ResultSetSpecification);
        
        $this->AddAllDataExpressions($AliasExpressionMap);
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
    
    public function AddDataExpression($Alias, Expression $DataExpression) {
        $this->AliasExpressionMap[$Alias] = $DataExpression;
    }
    
    public function AddAllDataExpressions(array $AliasExpressionMap) {
        foreach ($AliasExpressionMap as $Alias => $DataExpression) {
            $this->AddDataExpression($Alias, $DataExpression);
        }
    }
    
    
}

?>