<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class LazyEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation) {
        if($EntityProperty->IsOptional()) {
            throw MappingException::OptionalEntityInLazyContext($ToOneRelation);
        }
        
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $RelatedRevivalDataLoader = function ($ParentRowKey) use (&$Database, &$ResultRowArray) {
            static $ParentKeyRelatedRevivalDataMap = null;
            
            if($ParentKeyRelatedRevivalDataMap === null) {
                $RelatedRows = $this->LoadRelatedRows($Database, $ResultRowArray);
                $ParentKeyRelatedRevivalDataMap = $this->MapParentRowKeysToRelatedRevivalData($Database, $ResultRowArray, $RelatedRows);
            }
            
            return $ParentKeyRelatedRevivalDataMap[$ParentRowKey];
        };
        
        foreach($RevivalDataArray as $Key => $RevivalData) {
            $Loader = function () use (&$RelatedRevivalDataLoader, $Key) {
                return $RelatedRevivalDataLoader($Key);
            };
            
            $RevivalData[$this->Property] = 
                    $this->MakeLazyRevivalData(
                            $ResultRowArray[$Key], 
                            $Loader);
        }
    }
}

?>