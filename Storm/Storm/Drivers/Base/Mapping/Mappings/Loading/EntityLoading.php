<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class EntityLoading extends RelationshipLoading implements IEntityLoading {
    public function VerifyCompatibility(Object\IEntityProperty $Property) {
        
    }
    
    final protected function MapParentRowKeysToRelatedRevivalData(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToOneRelation $ToOneRelation,
            array $ParentRows, 
            array $RelatedRows) {
        $ParentKeyRelatedRowMap = $ToOneRelation->MapParentKeysToRelatedRow($ParentRows, $RelatedRows);
        
        $RelatedRevivalDataArray = $EntityRelationalMap->MapResultRowsToRevivalData($ParentKeyRelatedRowMap);
        
        $MappedRelatedRevivalData = [];
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = isset($RelatedRevivalDataArray[$Key]) ?
                    $RelatedRevivalDataArray[$Key] : null;
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MapParentRowKeysToLazyRevivalData(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToOneRelation $ToOneRelation,
            array $ParentRowArray,
            callable $KeyedLoader) {
        $LoadingValues = [];
        foreach($ParentRowArray as $Key => $ParentRow) {
            $Loader = function () use (&$KeyedLoader, $Key) {
                return $KeyedLoader($Key);
            };
            
            $LoadingValues[$Key] = $this->MakeLazyRevivalData(
                    $EntityRelationalMap, 
                    $ToOneRelation, 
                    $ParentRow, 
                    $Loader);
        }
        
        return $LoadingValues;
    }
    
    final protected function MakeLazyRevivalData(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToOneRelation $ToOneRelation,
            Relational\ResultRow $ParentData,
            callable $RevivalDataLoader) {
        $RelatedData = $EntityRelationalMap->ResultRow();
        $ToOneRelation->MapRelationalParentDataToRelatedData($ParentData, $RelatedData);
        $AlreadyKnownRelatedRevivalData = 
                $EntityRelationalMap->MapResultRowDataToRevivalData([$RelatedData])[0];
        
        return new LazyRevivalData($AlreadyKnownRelatedRevivalData, $RevivalDataLoader);
    }
    
}

?>