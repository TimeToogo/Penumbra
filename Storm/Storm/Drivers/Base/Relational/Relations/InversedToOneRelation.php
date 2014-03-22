<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

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