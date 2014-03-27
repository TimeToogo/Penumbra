<?php

namespace Penumbra\Drivers\Platforms\Mysql\Mapping\Types;

use \Penumbra\Drivers\Platforms\Standard\Mapping;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

class DateIntervalMapper extends Mapping\ObjectTypeMapper {
    
    public function GetClass() {
        return 'DateInterval';
    }

    protected function MapClassInstance($Instance) {
        return new Mapping\ContextualObjectExpression($Instance);
    }
    
    protected function ReviveClassInstance($MappedValue) {
        throw new \Penumbra\Core\NotSupportedException;
    }

    protected function MapNewClass(array $MappedArgumentExpressions) {
        if(!isset($MappedArgumentExpressions[0])) {
            throw new \Penumbra\Drivers\Base\Relational\PlatformException('Date interval must have one constructor argument');
        }
        if(!($MappedArgumentExpressions[0] instanceof R\ValueExpression)) {
            throw new \Penumbra\Drivers\Base\Relational\PlatformException('Date interval must have one constant constructor argument');
        }
        return new Mapping\ContextualObjectExpression(new \DateInterval($MappedArgumentExpressions[0]->GetValue()));
    }

    public function MapValue(R\Expression $ValueExpression) {
        throw new \Penumbra\Drivers\Base\Relational\PlatformException();
    }

}

?>