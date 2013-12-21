<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\IQueryExecutor;
use \Storm\Core\Relational\IToOneRelation;

abstract class ToOneReviver implements IToOneReviver {
    final public function Revive(IConnection $Connection, 
            IToOneRelation $ToOneRelation, array $Rows) {
        switch (get_class($ToOneRelation)) {
            case ToOneRelation::GetType():
                return $this->ReviveToOneRelation($Connection, $ToOneRelation, $Rows);
            case InversedToOneRelation::GetType():
                return $this->ReviveInversedToOneRelation($Connection, $ToOneRelation, $Rows);
            default:
                throw new \InvalidArgumentException('Unsupported IToOneRelation');
        }
    }
    
    public abstract function ReviveToOneRelation(IConnection $Connection,
            ToOneRelation $ToOneRelation, array $Rows);
    public abstract function ReviveInversedToOneRelation(IConnection $Connection,
            InversedToOneRelation $InversedToOneRelation, array $Rows);
}

?>
