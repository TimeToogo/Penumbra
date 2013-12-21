<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Core\Relational\IToManyRelation;

abstract class ToManyReviver implements IToManyReviver {
    final public function Revive(IConnection $Connection, IToManyRelation $ToManyRelation, array $Rows) {
        switch (get_class($ToManyRelation)) {
            case ToManyRelation::GetType():
                return $this->ReviveToManyRelation($Connection, $ToManyRelation, $Rows);
            case JoinTableRelation::GetType():
                return $this->ReviveJoinTableRelation($Connection, $ToManyRelation, $Rows);
            default:
                throw new \InvalidArgumentException('Unsupported IToManyRelation');
        }
    }
    
    public abstract function ReviveToManyRelation(IConnection $Connection, 
            ToManyRelation $ToManyRelation, array $Rows);
    public abstract function ReviveJoinTableRelation(IConnection $Connection, 
            JoinTableRelation $JoinTableRelation, array $Rows);
}

?>
