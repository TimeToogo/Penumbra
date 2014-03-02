<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions\Expression;

class IndexGetter extends IndexBase implements IPropertyGetter {

    public function Expression(Expression $EntityExpression) {
        return Expression::Index($EntityExpression, $this->Index);
    }
    
    public function GetValueFrom($Entity) {
        return $Entity[$this->Index];
    }
}

?>
