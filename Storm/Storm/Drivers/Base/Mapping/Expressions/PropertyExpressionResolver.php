<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Mapping;
use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

class PropertyExpressionResolver {
    /**
     * @var Relational\ResultSetSpecification 
     */
    private $ResultSetSpecification;
    /**
     * @var Mapping\DomainDatabaseMap 
     */
    private $DomainDatabaseMap;
    
    /**
     * @var O\PropertyExpression[] 
     */
    private $AddedPropertyExpressions = [];
    
    public function __construct(Relational\ResultSetSpecification $ResultSetSpecification, Mapping\DomainDatabaseMap $DomainDatabaseMap) {
        $this->ResultSetSpecification = $ResultSetSpecification;
        $this->DomainDatabaseMap = $DomainDatabaseMap;
    }
    
    private function GetPropertyMapping(O\PropertyExpression $Expression) {
        return $this->DomainDatabaseMap->GetPropertyMapping($Expression->GetProperty());
    }
    
    public function MapProperty(O\PropertyExpression $Expression, O\TraversalExpression $TraversalExpression = null) {
        $this->AddPropertyToResultSet($Expression);
        return $this->GetPropertyMapping($Expression)->MapPropertyExpression(
                $this->ResultSetSpecification->GetSources(),
                $TraversalExpression);
    }
    
    private function AddPropertyToResultSet(O\PropertyExpression $Expression) {
        if($Expression->HasParentPropertyExpression()) {
            $this->AddPropertyToResultSet($Expression->GetParentPropertyExpression());
        }
        $PropertyMapping = $this->GetPropertyMapping(Expression);
        
        if(!in_array($Expression, $this->AddedPropertyExpressions)) {
            $PropertyMapping->AddTraversalRequirementsToResultSet($this->ResultSetSpecification);
            $this->AddedPropertyExpressions[] = $Expression;
        }
    }
}

?>