<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use Storm\Drivers\Base\Object\Properties\Property;
use \Storm\Core\Relational;

abstract class PropertyCollectionMapping extends PropertyRelationMapping implements IPropertyCollectionMapping {
    private $ToManyRelation;
    
    public function __construct( 
            Object\IProperty $Property,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
        
        $this->ToManyRelation = $ToManyRelation;
    }
    
    /**
     * @return Relational\IToManyRelation
     */
    final protected function GetToManyRelation() {
        return $this->ToManyRelation;
    }
    
    protected function ReviveProperties(
            Map $ParentRowRevivalDataMap, 
            Map $ParentRelatedRowsMap, 
            Map $RelatedRowRevivalDataMap) {
        $Property = $this->GetProperty();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RevivalData = $RelatedRowRevivalDataMap[$ParentRow];
            $RelatedRows = $ParentRelatedRowsMap[$ParentRow];
            $RevivalDataArray = array();
            foreach($RelatedRows as $RelatedRow) {
                $RevivalDataArray[] = $RelatedRow;
            }
            $RevivalData[$Property] = $RevivalDataArray;
        }
    }
    
    public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        
    }
    
    public function Discard(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        
    }
}

?>