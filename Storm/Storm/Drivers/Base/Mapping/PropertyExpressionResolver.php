<?php

namespace Storm\Drivers\Base\Mapping;

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
     * @var Mapping\IEntityRelationalMap
     */
    private $EntityRelationalMap;
    
    /**
     * @var O\PropertyExpression[] 
     */
    private $AddedPropertyExpressions = [];
    
    public function __construct(Relational\ResultSetSpecification $ResultSetSpecification, Mapping\IEntityRelationalMap $EntityRelationalMap) {
        $this->ResultSetSpecification = $ResultSetSpecification;
        $this->EntityRelationalMap = $EntityRelationalMap;
    }
    
    public function MapProperty(O\PropertyExpression $Expression, &$ReturnType) {
        $PropertyMapping = $this->AddPropertyToResultSet($Expression);
        return $PropertyMapping->MapPropertyExpression(
                $this->ResultSetSpecification->GetSources(),
                $ReturnType);
    }
    
    /**
     * @return IPropertyMapping
     */
    private function AddPropertyToResultSet(O\PropertyExpression $Expression) {
        $EntityRelationalMap = $this->EntityRelationalMap;
        $Property = $Expression->GetProperty();
        
        if($Expression->HasParentPropertyExpression()) {
            $ParentPropertyMapping = $this->AddPropertyToResultSet($Expression->GetParentPropertyExpression());
            if(!($ParentPropertyMapping instanceof Mapping\IRelationshipPropertyRelationMapping)) {
                throw new Mapping\MappingException(
                        'Invalid property expression tree: %s must have a parent property expression which maps to a relationship',
                        $Property->GetIdentifier());
            }
            
            $EntityRelationalMap = $ParentPropertyMapping->GetEntityRelationalMap();
        }
        
        if(!$EntityRelationalMap->HasPropertyMapping($Property)) {
            throw new Mapping\MappingException(
                    'Invalid property expression tree: %s is not mapped in entity relational map for %s',
                    $Property->GetIdentifier(),
                    $EntityRelationalMap->GetEntityType());
        }
        
        $PropertyMapping = $EntityRelationalMap->GetPropertyMapping($Property);
        
        if(!in_array($Expression, $this->AddedPropertyExpressions)) {
            $PropertyMapping->AddTraversalRequirementsToResultSet($this->ResultSetSpecification);
            $this->AddedPropertyExpressions[] = $Expression;
        }
        
        return $PropertyMapping;
    }
}

?>