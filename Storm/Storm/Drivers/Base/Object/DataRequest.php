<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

class DataRequest extends Request implements Object\IDataRequest {
    private $AliasExpressionMap = [];
    
    public function __construct(
            $EntityOrType, 
            array $AliasExpressionMap, 
            array $GroupByExpressions,
            array $AggregatePredicateExpressions,
            Object\ICriteria $Criteria = null) {
        parent::__construct(
                $EntityOrType, 
                $GroupByExpressions, 
                $AggregatePredicateExpressions, 
                $Criteria);
        
        foreach($AliasExpressionMap as $Alias => $Expression) {
            $this->AddDataExpression($Alias, $Expression);
        }
    }
    
    final protected function AddDataExpression($Alias, Expression $Expression) {
        $this->AliasExpressionMap[$Alias] = $Expression;
    }
    
    final public function GetAliasExpressionMap() {
        return $this->AliasExpressionMap;
    }
}

?>