<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Object\MultipleLazyRevivalData;

abstract class CollectionLoading extends RelationshipLoading implements ICollectionLoading {
    public function VerifyCompatibility(Object\ICollectionProperty $Property) {
        
    }
    
    public function AddLoadingRequirementsToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\IToManyRelation $ToManyRelation, 
            Relational\ResultSetSelect $Select) {
        $Select->AddColumns($ToManyRelation->GetParentColumns());
    }
    
    final protected function LoadRelatedRevivalDataArrayMap(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\Database $Database, 
            Relational\IToManyRelation $ToManyRelation, 
            array $ParentRowArray,
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        
        $RelatedRows = $this->LoadRelatedRows(
                $EntityRelationalMap,
                $ToManyRelation, 
                $Database, 
                $ParentRowArray, 
                $AlreadyKnownRevivalData);
        
        return $this->MapParentRowKeysToRelatedRevivalDataArray(
                $EntityRelationalMap, 
                $ToManyRelation, 
                $ParentRowArray, 
                $RelatedRows);
    }
    
    final protected function MapParentRowKeysToRelatedRevivalDataArray(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IToManyRelation $ToManyRelation,
            array $ParentRows, 
            array $RelatedRows) {
        $this->RemoveDuplicateRows($EntityRelationalMap->GetPrimaryKeyTable(), $RelatedRows);
        $ParentKeyRelatedRowsMap = $ToManyRelation->MapParentKeysToRelatedRows($ParentRows, $RelatedRows);
        
        $RelatedRevivalData = $EntityRelationalMap->MapResultRowsToRevivalData($RelatedRows);
        
        $MappedRelatedRevivalData = [];
        foreach($ParentRows as $Key => $ParentRow) {            
            $MappedRelatedRevivalData[$Key] = array_intersect_key($RelatedRevivalData, $ParentKeyRelatedRowsMap[$Key]);
        }
        
        return $MappedRelatedRevivalData;
    }
    
    private function RemoveDuplicateRows(Relational\ITable $PrimaryKeyTable, array &$RelatedRows) {
        $Hashes = [];
        foreach ($RelatedRows as $Key => $RelatedRow) {
            $Hash = $RelatedRow->GetPrimaryKey($PrimaryKeyTable)->HashData();
            if(isset($Hashes[$Hash])) {
                unset($RelatedRows[$Key]);
            }
            $Hashes[$Hash] = true;
        }
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