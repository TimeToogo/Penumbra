<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Object\LazyRevivalData;

class ExtraLazyCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\IProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {        
        foreach($ResultRowArray as $Key => $ParentRow) {
            
            $RelatedRevivalDataArrayLoader = 
                    function () use (&$Database, $ParentRow) {
                        $RelatedRows = $this->LoadRelatedRows($Database, [$ParentRow]);
                        
                        $RelatedRevivalDataArray = $this->EntityRelationalMap->MapResultRowsToRevivalData($Database, $RelatedRows);

                        return $RelatedRevivalDataArray;
                    };
            
            $RevivalDataArray[$Key][$this->Property] = 
                    $this->MakeMultipleLazyRevivalData(
                            $ParentRow, 
                            $RelatedRevivalDataArrayLoader);
        }
    }
}

?>