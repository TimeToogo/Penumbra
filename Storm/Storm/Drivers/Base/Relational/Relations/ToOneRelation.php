<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToOneRelation extends ToOneRelationBase {
    public function __construct(ForeignKey $ForeignKey) {
         parent::__construct(
                 $ForeignKey, 
                $ForeignKey->GetReferencedTable(), 
                Relational\DependencyOrder::After, 
                Relational\DependencyOrder::Before);
    }
}

?>