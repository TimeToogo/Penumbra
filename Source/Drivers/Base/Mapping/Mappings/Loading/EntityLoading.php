<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Object\LazyRevivalData;

abstract class EntityLoading extends RelationshipLoading implements IEntityLoading {
    public function VerifyCompatibility(Object\IEntityProperty $Property) {
        
    }
    
    public function AddLoadingRequirementsToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\IToOneRelation $ToOneRelation, 
            Relational\ResultSetSelect $Select) {
        $Select->AddColumns($ToOneRelation->GetParentColumns());
    }
    
    final protected function LoadRelatedRevivalDataMap(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\Database $Database, 
            Relational\IToOneRelation $ToOneRelation, 
            array $ParentRowArray,
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        
        $RelatedRows = $this->LoadRelatedRows(
                $EntityRelationalMap,
                $ToOneRelation, 
                $Database, 
                $ParentRowArray, 
                $AlreadyKnownRevivalData);
        
        return $this->MapParentRowKeysToRelatedRevivalData(
                $EntityRelationalMap, 
                $ToOneRelation, 
                $ParentRowArray, 
                $RelatedRows);
    }
    
    final protected function MapParentRowKeysToRelatedRevivalData(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToOneRelation $ToOneRelation,
            array $ParentRows, 
            array $RelatedRows) {
        $ParentKeyRelatedRowMap = $ToOneRelation->MapParentKeysToRelatedRow($ParentRows, $RelatedRows);
        
        $RelatedRevivalDataArray = $EntityRelationalMap->MapResultRowsToRevivalData(array_filter($ParentKeyRelatedRowMap));
        
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