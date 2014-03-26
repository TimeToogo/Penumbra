<?php

namespace Penumbra\Drivers\Base\Relational\Relations;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Traits\ForeignKey;

class InversedToOneRelation extends ToOneRelationBase {    
    public function __construct(ForeignKey $ForeignKey) {
        parent::__construct(
                $ForeignKey, 
                $ForeignKey->GetParentTable(), 
                Relational\DependencyOrder::Before, 
                Relational\DependencyOrder::After);
    }
    
    
    
    public function MapRelationalParentDataToRelatedData(
            Relational\ColumnData $ParentData, Relational\ColumnData $RelatedData) {
        $this->ForeignKey->MapReferencedToParentKey($ParentData, $RelatedData);
    }
}

?>