<?php

namespace Penumbra\Drivers\Base\Relational\Relations;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Traits\ForeignKey;

class ToOneRelation extends ToOneRelationBase {
    public function __construct(ForeignKey $ForeignKey) {
         parent::__construct(
                $ForeignKey, 
                $ForeignKey->GetReferencedTable(), 
                Relational\DependencyOrder::After, 
                Relational\DependencyOrder::Before);
    }
    
    public function MapRelationalParentDataToRelatedData(
            Relational\ColumnData $ParentData, Relational\ColumnData $RelatedData) {
        $this->ForeignKey->MapParentToReferencedKey($ParentData, $RelatedData);
    }
}

?>