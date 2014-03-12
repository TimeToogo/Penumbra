<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Mapping;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

class PropertyExpressionResolver {
    /**
     * @var Relational\Criteria 
     */
    private $Criteria;
    /**
     * @var Mapping\DomainDatabaseMap 
     */
    private $DomainDatabaseMap;
    
    /**
     * @var O\PropertyExpression[] 
     */
    private $AddedPropertyExpressions = [];
    
    public function __construct(Relational\Criteria $Criteria, Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        $this->Criteria = $Criteria;
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    private function GetPropertyMapping(O\PropertyExpression $Expression) {
        return $this->DomainDatabaseMap->GetPropertyMapping($Expression->GetProperty());
    }
            
    public function MapProperty(O\PropertyExpression $Expression, O\TraversalExpression $TraversalExpression = null) {
        $this->AddPropertyToCriteria($Expression);
        return $this->GetPropertyMapping($Expression)->MapPropertyExpression($TraversalExpression);
    }
    
    private function AddPropertyToCriteria(O\PropertyExpression $Expression) {
        if($Expression->HasParentPropertyExpression()) {
            $this->AddPropertyToCriteria($Expression->GetParentPropertyExpression());
        }
        $PropertyMapping = $this->GetPropertyMapping($Expression);
        
        if(!in_array($Expression, $this->AddedPropertyExpressions)) {
            $PropertyMapping->AddToCriteria($this->Criteria);
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