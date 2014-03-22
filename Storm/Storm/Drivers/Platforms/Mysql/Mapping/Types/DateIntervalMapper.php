<?php

namespace Storm\Drivers\Platforms\Mysql\Mapping\Types;

use \Storm\Drivers\Platforms\Standard\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class DateIntervalMapper extends Mapping\ObjectTypeMapper {
    
    public function GetClass() {
        return 'DateInterval';
    }

    protected function MapClassInstance($Instance) {
        return new Mapping\ContextualObjectExpression($Instance);
    }
    
    protected function ReviveClassInstance($MappedValue) {
        throw new \Storm\Core\NotSupportedException;
    }

    protected function MapNewClass(array $MappedArgumentExpressions) {
        if(!isset($MappedArgumentExpressions[0])) {
            throw new \Storm\Drivers\Base\Relational\PlatformException('Date interval must have one constructor argument');
        }
        if(!($MappedArgumentExpressions[0] instanceof R\ValueExpression)) {
            throw new \Storm\Drivers\Base\Relational\PlatformException('Date interval must have one constant constructor argument');
        }
        return new Mapping\ContextualObjectExpression(new \DateInterval($MappedArgumentExpressions[0]->GetValue()));
    }

    public function MapValue(R\Expression $ValueExpression) {
        throw new \Storm\Drivers\Base\Relational\PlatformException();
    }

}

?>