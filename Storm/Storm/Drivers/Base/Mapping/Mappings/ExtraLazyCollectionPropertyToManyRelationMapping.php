<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class ExtraLazyCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\IProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }

    public function Revive(Mapping\DomainDatabaseMap $DomainDatabaseMap, Map $ParentRowRevivalDataMap) {
        $Property = $this->GetProperty();
        $EntityType = $this->GetEntityType();
        $ToManyRelation = $this->GetToManyRelation();
        $Database = $DomainDatabaseMap->GetDatabase();
        foreach($ParentRowRevivalDataMap as $ParentRow) {
            $RevivalData = $ParentRowRevivalDataMap[$ParentRow];
            $RelatedEntityRevivalDataLoader = function () use (&$DomainDatabaseMap, &$Database, &$ToManyRelation, $EntityType, $ParentRow) {
                $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, [$ParentRow]);
                $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($EntityType, $RelatedRows);
                
                return $RelatedRevivalDataArray;
            };
            
            $RevivalData[$Property] = $RelatedEntityRevivalDataLoader;
        }
    }
}

?>