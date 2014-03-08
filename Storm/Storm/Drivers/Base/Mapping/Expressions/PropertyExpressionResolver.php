<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Mapping;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

class PropertyExpressionResolver {
    /**
     * @var Relational\Criterion 
     */
    private $Criterion;
    /**
     * @var Mapping\DomainDatabaseMap 
     */
    private $DomainDatabaseMap;
    
    /**
     * @var O\PropertyExpression[] 
     */
    private $AddedPropertyExpressions = [];
    
    public function __construct(Relational\Criterion $Criterion, Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        $this->Criterion = $Criterion;
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    private function GetPropertyMapping(O\PropertyExpression $Expression) {
        return $this->DomainDatabaseMap->GetPropertyMapping($Expression->GetProperty());
    }
            
    public function MapProperty(O\PropertyExpression $Expression) {
        $this->AddPropertyToCriterion($Expression);
        return $PropertyMapping->MapPropertyExpression();
    }
    
    private function AddPropertyToCriterion(O\PropertyExpression $Expression) {
        if($Expression->HasParentPropertyExpression()) {
            $this->AddPropertyToCriterion($Expression->GetParentPropertyExpression());
        }
        $PropertyMapping = $this->GetPropertyMapping($Expression);
        
        if(!in_array($Expression, $this->AddedPropertyExpressions)) {
            $PropertyMapping->AddToCriterion($this->Criterion);
            $this->AddedPropertyExpressions[] = $Expression;
        }
    }
    
    /**
     * @return O\Expression
     */
    public function ResolveTraversalExpression(O\PropertyExpression $Expression, O\TraversalExpression $TraversalExpression) {
        $PropertyMapping = $this->GetPropertyMapping($Expression);
    }
    
    /**
     * @return O\Expression[]
     */
    public function ResolveAssignmentExpression(O\PropertyExpression $Expression, $Operator, O\Expression $AssignmentValueExpression) {
        $PropertyMapping = $this->GetPropertyMapping($Expression);
    }
    
    /**
     * @return O\Expression
     */
    public function ResolveBinaryOperationExpression(O\PropertyExpression $Expression, $Operator, O\Expression $OtherOperandExpression) {
        $PropertyMapping = $this->GetPropertyMapping($Expression);
    }
    
    /**
     * @return O\Expression
     */
    public function ResolveUnaryOperationExpression(O\PropertyExpression $Expression, $Operator) {
        $PropertyMapping = $this->GetPropertyMapping($Expression);
    }
    
    /**
     * @return O\Expression
     */
    public function ResolveCastExpression(O\PropertyExpression $Expression, $CastType) {
        $PropertyMapping = $this->GetPropertyMapping($Expression);
    }
}

?>