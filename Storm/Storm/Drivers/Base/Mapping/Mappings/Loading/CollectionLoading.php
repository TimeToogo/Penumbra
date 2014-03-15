<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class CollectionLoading extends RelationshipLoading implements ICollectionLoading {
    public function VerifyCompatibility(Object\ICollectionProperty $Property) {
        
    }
    
    final protected function MapParentRowKeysToRelatedRevivalDataArray(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToManyRelation $ToManyRelation,
            array $ParentRows, 
            array $RelatedRows) {
        $ParentKeyRelatedRowsMap = $ToManyRelation->MapParentKeysToRelatedRows($ParentRows, $RelatedRows);
        
        $RelatedRevivalData = $EntityRelationalMap->MapResultRowsToRevivalData($RelatedRows);
        
        $MappedRelatedRevivalData = [];
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = array_intersect_key($RelatedRevivalData, $ParentKeyRelatedRowsMap[$Key]);
        }
        
        return $MappedRelatedRevivalData;
    }
    
    final protected function MapParentRowKeysToMultipleLazyRevivalData(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToManyRelation $ToManyRelation,
            array $ParentRowArray,
            callable $KeyedLoader) {
        $LoadingValues = [];
        foreach($ParentRowArray as $Key => $ParentRow) {
            $Loader = function () use (&$KeyedLoader, $Key) {
                return $KeyedLoader($Key);
            };
            
            $LoadingValues[$Key] = $this->MakeMultipleLazyRevivalData(
                    $EntityRelationalMap, 
                    $ToManyRelation, 
                    $ParentRow, 
                    $Loader);
        }
        
        return $LoadingValues;
    }
    
    final protected function MakeMultipleLazyRevivalData(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToManyRelation $ToManyRelation,
            Relational\ResultRow $ParentData,
            callable $RevivalDataLoader) {
        $RelatedData = $EntityRelationalMap->ResultRow();
        $ToManyRelation->MapRelationalParentDataToRelatedData($ParentData, $RelatedData);
        $AlreadyKnownRelatedRevivalData = 
            $EntityRelationalMap->MapResultRowDataToRevivalData([$RelatedData])[0];
        
        return new MultipleLazyRevivalData($AlreadyKnownRelatedRevivalData, $RevivalDataLoader);
    }
}

?>