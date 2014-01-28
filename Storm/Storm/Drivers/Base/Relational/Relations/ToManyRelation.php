<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends ToManyRelationBase {
    public function __construct(ForeignKey $ForeignKey) {
        parent::__construct($ForeignKey, 
                $ForeignKey->GetParentTable(),
                Relational\DependencyOrder::Before, 
                Relational\DependencyOrder::Before);
    }
    
    protected function GroupRelatedRowsByParentKeys(array &$MappedRelatedRows, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        
        $GroupedRelatedRows = $this->GroupRowsByColumnValues($RelatedRows, $ForeignKey->GetParentColumnIdentifiers());
        $KeyedParentRows = $this->MakeHashedDataToKeyMap($ParentRows, $ForeignKey->GetReferencedColumnIdentifiers());
        
        foreach($KeyedParentRows as $HashData => $ParentRowKey) {
            $MappedRelatedRows[$ParentRowKey] = isset($GroupedRelatedRows[$HashData]) ?
                    $GroupedRelatedRows[$HashData] : array();
        }
        
    }
    
    protected function PersistIdentifyingRelationship(
            Relational\Transaction $Transaction, 
            array $ParentRow, array &$ChildRows) {
        $ForeignKey = $this->GetForeignKey();
        $ParentTable = $this->GetParentTable();
        
        if($ParentTable->HasPrimaryKeyData($ParentRow)) {
            foreach($ChildRows as $ChildRow) {
                $ForeignKey->MapReferencedToParentKey($ParentRow, $ChildRow);
            }
        }
        else {
            $Transaction->SubscribeToPrePersistEvent($this->GetTable(), 
                    function () use (&$ForeignKey, &$ParentRow, &$ChildRows) {
                        foreach($ChildRows as $ChildRow) {
                            $ForeignKey->MapReferencedToParentKey($ParentRow, $ChildRow);
                        }
                    });
        }
    }
}

?>