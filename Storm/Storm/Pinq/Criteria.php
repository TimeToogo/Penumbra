<?php

namespace Storm\Pinq;

use \Storm\Core\Object\IEntityMap;
use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\Expressions\Expression;

class Criteria extends Object\Criteria {
    
    use FunctionParsing;
    
    public function __construct(IEntityMap $EntityMap, IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityMap->GetEntityType());
        $this->EntityMap = $EntityMap;
        $this->FunctionToExpressionTreeConverter = $FunctionToExpressionTreeConverter;
    }
    
    public function AddPredicateFunction(callable $Function) {
        $this->AddPredicate($this->ParseFunctionReturn($Function, 'predicate', [0 => Expression::Entity()]));
    }
    
    public function AddOrderByFunction(callable $Function, $Ascending) {
        $this->AddOrderByExpression($this->ParseFunctionReturn($Function, 'order by', [0 => Expression::Entity()]), $Ascending);
    }
}

?>
